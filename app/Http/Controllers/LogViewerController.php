<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;

class LogViewerController extends Controller
{
    /**
     * Display the log viewer page
     */
    public function index(Request $request): Response
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            return Inertia::render('developer/Logs', [
                'logs' => [],
                'filters' => [],
                'fileExists' => false
            ]);
        }

        $logs = $this->parseLogFile($logFile, $request);

        return Inertia::render('developer/Logs', [
            'logs' => $logs,
            'filters' => $request->only(['level', 'search']),
            'fileExists' => true
        ]);
    }

    /**
     * Parse the log file and apply filters
     */
    private function parseLogFile(string $filePath, Request $request): array
    {
        $content = File::get($filePath);
        
        // Pattern to match Laravel log entries
        // Format: [2025-12-25 18:31:51] local.DEBUG: Message {"context":"value"}
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(?=\n\[|\z)/s';
        
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        $logs = collect($matches)->map(function ($match) {
            $message = trim($match[4]);
            
            // Try to extract JSON context if present
            $context = null;
            if (preg_match('/^(.*?)\s+(\{.*\})\s*$/s', $message, $contextMatch)) {
                $message = trim($contextMatch[1]);
                $context = $contextMatch[2];
            }
            
            return [
                'timestamp' => $match[1],
                'environment' => $match[2],
                'level' => strtoupper($match[3]),
                'message' => $message,
                'context' => $context,
                'full' => trim($match[4])
            ];
        });

        // Apply level filter
        if ($level = $request->input('level')) {
            $logs = $logs->filter(fn($log) => strtolower($log['level']) === strtolower($level));
        }

        // Apply search filter
        if ($search = $request->input('search')) {
            $logs = $logs->filter(fn($log) => 
                str_contains(strtolower($log['message']), strtolower($search)) ||
                str_contains(strtolower($log['full']), strtolower($search))
            );
        }

        // Return latest first, limited to 200 entries
        return $logs->reverse()->values()->take(200)->toArray();
    }

    /**
     * Clear the log file
     */
    public function clear()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        return back()->with('notification', [
            'type' => 'success',
            'title' => 'Success',
            'message' => 'Logs cleared successfully'
        ]);
    }

    /**
     * Download the log file
     */
    public function download()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            return back()->with('notification', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Log file not found'
            ]);
        }

        return response()->download($logFile, 'laravel-' . date('Y-m-d-His') . '.log');
    }
}
