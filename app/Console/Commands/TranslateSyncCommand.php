<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class TranslateSyncCommand extends Command
{
    protected $signature = 'translate:sync {--locales= : Comma separated locales to sync} {--all : Sync all supported locales} {--batch=20 : Number of keys to translate per request}';
    protected $description = 'Sync translations with Gemini API (with Google Translate fallback)';

    public function handle()
    {
        $supportedLocales = config('app.supported_locales', []);
        
        // Determine target locales
        if ($this->option('all')) {
            $targetLocales = $supportedLocales;
        } elseif ($this->option('locales')) {
            $targetLocales = explode(',', $this->option('locales'));
        } else {
            $targetLocales = $this->choice(
                'Which locales do you want to sync?',
                array_merge(['All'], $supportedLocales),
                0,
                $maxAttempts = null,
                $allowMultipleSelections = true
            );
            
            if (in_array('All', $targetLocales)) {
                $targetLocales = $supportedLocales;
            }
        }

        // Get API Key
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            $apiKey = $this->secret('Enter your Gemini API Key:');
        }

        if (!$apiKey) {
            $this->error('API Key is required.');
            return;
        }

        $sourcePath = lang_path('en');
        $files = File::files($sourcePath);
        $batchSize = $this->option('batch');

        foreach ($targetLocales as $locale) {
            if ($locale === 'en') {
                $this->generateJson('en');
                continue;
            }

            $this->info("Syncing locale: {$locale}");
            $localePath = lang_path($locale);
            
            if (!File::exists($localePath)) {
                File::makeDirectory($localePath, 0755, true);
            }

            foreach ($files as $file) {
                $filename = $file->getFilename();
                $targetFile = $localePath . '/' . $filename;
                
                $sourceData = include $file->getPathname();
                $targetData = File::exists($targetFile) ? include $targetFile : [];

                $missingKeysStructure = $this->getMissingKeys($sourceData, $targetData);

                if (empty($missingKeysStructure)) {
                    $this->line("  - {$filename}: Up to date.");
                    continue;
                }

                $flattenedMissing = Arr::dot($missingKeysStructure);
                $missingCount = count($flattenedMissing);

                $this->warn("  - {$filename}: Found {$missingCount} missing keys. Processing in batches of {$batchSize}...");
                
                // Chunk the flattened keys to avoid timeouts
                $chunks = array_chunk($flattenedMissing, $batchSize, true);
                $translatedFlattened = [];

                foreach ($chunks as $index => $chunk) {
                    $chunkNum = $index + 1;
                    $totalChunks = count($chunks);
                    $this->line("    Processing batch {$chunkNum}/{$totalChunks}...");

                    try {
                        $translatedChunk = $this->translateBatchGemini($chunk, $locale, $apiKey);
                    } catch (\Exception $e) {
                         $this->error("    Batch {$chunkNum} failed with Gemini: " . $e->getMessage());
                         $this->info("    Falling back to Google Translate for batch {$chunkNum}...");
                         $translatedChunk = $this->translateBatchGoogle($chunk, $locale);
                    }

                    if (empty($translatedChunk)) {
                        $this->error("    Failed to translate batch {$chunkNum}.");
                    }

                    $translatedFlattened = array_merge($translatedFlattened, $translatedChunk);
                    
                    // Small delay to be nice to APIs
                    usleep(500000); // 0.5s
                }

                if (empty($translatedFlattened)) {
                     $this->error("    No translations processed for {$filename}");
                     continue;
                }

                // Unflatten and merge
                $translatedStructure = Arr::undot($translatedFlattened);
                $newData = array_replace_recursive($targetData, $translatedStructure);
                
                $this->writePhpFile($targetFile, $newData);
                
                $this->info("    Translated and saved.");
            }
            
            // Generate JSON for this locale after syncing
            $this->generateJson($locale);
        }
    }

    private function getMissingKeys($source, $target)
    {
        $missing = [];
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                if (!isset($target[$key]) || !is_array($target[$key])) {
                     $missing[$key] = $value;
                } else {
                    $nestedMissing = $this->getMissingKeys($value, $target[$key]);
                    if (!empty($nestedMissing)) {
                        $missing[$key] = $nestedMissing;
                    }
                }
            } else {
                if (!isset($target[$key])) {
                    $missing[$key] = $value;
                }
            }
        }
        return $missing;
    }

    private function translateBatchGemini(array $flattenedData, string $targetLocale, string $apiKey)
    {
        // $flattenedData is ['key.subkey' => 'Value', ...]
        // We only send values to save tokens, but need to map back keys.
        // Actually, simplest is to send keys too so the AI knows context, but let's try just values 
        // OR better: send the associative array, ask AI to return JSON.
        
        $prompt = "Translate the values of the following JSON to '{$targetLocale}' (ISO 639-1). 
        Do not change the keys. Return ONLY the valid JSON, no markdown formatting.
        Context: Real estate / financial application.
        
        JSON:
        " . json_encode($flattenedData, JSON_UNESCAPED_UNICODE);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]);

        if (!$response->successful()) {
             throw new \Exception("API Error: " . $response->body());
        }

        $content = $response->json('candidates.0.content.parts.0.text');
        
        // Strip markdown code blocks if present
        $content = preg_replace('/^```json\s*|\s*```$/', '', trim($content));
        
        $decoded = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON Decode Error: " . json_last_error_msg());
        }

        return $decoded;
    }

    private function translateBatchGoogle(array $flattenedData, string $targetLocale)
    {
        $translated = [];
        
        foreach ($flattenedData as $key => $text) {
             // Free Google Translate Endpoint (GTX)
             // https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=es&dt=t&q=Hello
             
             try {
                 $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl={$targetLocale}&dt=t&q=" . urlencode($text);
                 
                 $response = Http::timeout(10)->get($url);
                 
                 if ($response->successful()) {
                     $json = $response->json();
                     // Result is [[[ "Hola", "Hello", ... ]]]
                     if (isset($json[0][0][0])) {
                         $translated[$key] = $json[0][0][0];
                     } else {
                         $translated[$key] = $text; // Fallback to original
                     }
                 } else {
                     $translated[$key] = $text; // Fallback
                 }
                 
                 // Be polite to the free API
                 usleep(200000); // 0.2s per word
                 
             } catch (\Exception $e) {
                 $translated[$key] = $text;
             }
        }
        
        return $translated;
    }

    private function writePhpFile($path, $data)
    {
        $content = "<?php\n\nreturn " . $this->prettyPrintArray($data) . ";\n";
        File::put($path, $content);
    }
    
    // Custom pretty printer to match Laravel's lang file style (roughly)
    private function prettyPrintArray($data, $indent = 0)
    {
        $str = "[\n";
        $indentStr = str_repeat("    ", $indent + 1);
        
        foreach ($data as $key => $value) {
            $formattedKey = is_int($key) ? $key : "'$key'";
            
            $str .= $indentStr . "$formattedKey => ";
            
            if (is_array($value)) {
                $str .= $this->prettyPrintArray($value, $indent + 1);
            } else {
                $value = str_replace("'", "\'", $value); // Escape single quotes
                $str .= "'$value'";
            }
            
            $str .= ",\n";
        }
        
        $str .= str_repeat("    ", $indent) . "]";
        
        return $str;
    }

    private function generateJson($locale)
    {
        $this->info("Generating JSON for locale: {$locale}");
        
        $path = lang_path($locale);
        $files = File::files($path);
        $combined = [];

        foreach ($files as $file) {
            $keyPrefix = $file->getFilenameWithoutExtension();
            $content = include $file->getPathname();
            
            // Flatten with filename prefix
            $flattened = Arr::dot($content, $keyPrefix . '.');
            $combined = array_merge($combined, $flattened);
        }

        $jsonPath = lang_path("php_{$locale}.json");
        File::put($jsonPath, json_encode($combined, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->line("  - Generated " . basename($jsonPath));
    }
}
