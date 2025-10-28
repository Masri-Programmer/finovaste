<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Generates an XML sitemap for the public routes of the application.
 *
 * NOTE: For the dynamic routes ('dynamic.index' and 'dynamic.show'), 
 * you MUST replace the placeholder/mock data logic with actual database queries 
 * that retrieve the models and their items/slugs.
 */
class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a sitemap.xml file for public routes, including dynamic resource routes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sitemap generation...');

        // Initialize the array to hold all sitemap entries
        $urls = [];

        // 1. Add Static Public Routes
        // The 'dashboard' route is authenticated and excluded.
        // The 'language.switch' route is utility and excluded.

        $urls[] = [
            'loc' => URL::route('home', [], true), // Use absolute URL (true)
            'changefreq' => 'hourly',
            'priority' => '1.0',
            'lastmod' => Carbon::now()->toAtomString(),
        ];

        // 2. Handle Dynamic Resource Routes (/{model} and /{model}/{item})

        // IMPORTANT: Replace this array with a method that dynamically determines 
        // the valid slugs for your public resources (e.g., 'posts', 'products').
        $dynamicModels = ['posts', 'products', 'services'];

        foreach ($dynamicModels as $modelSlug) {

            // Add the dynamic index page (e.g., /posts, /products)
            $urls[] = [
                'loc' => URL::route('dynamic.index', ['model' => $modelSlug], true),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'lastmod' => Carbon::now()->toAtomString(),
            ];

            // --- Dynamic Items (Show Pages) ---

            // IMPORTANT: This section must be replaced by real database queries.
            // Example Placeholder Logic (Assuming 'Post' model drives 'posts' slug):
            if ($modelSlug === 'posts') {
                // In a real app, you would do:
                // $items = \App\Models\Post::where('is_public', true)->get(['slug', 'updated_at']);

                // MOCK DATA for demonstration:
                $mockItems = [
                    (object)['slug' => 'first-post-title', 'updated_at' => Carbon::now()->subMonths(1)],
                    (object)['slug' => 'latest-blog-update', 'updated_at' => Carbon::now()->subDays(3)],
                ];

                foreach ($mockItems as $item) {
                    $urls[] = [
                        'loc' => URL::route('dynamic.show', ['model' => $modelSlug, 'item' => $item->slug], true),
                        'changefreq' => 'monthly',
                        'priority' => '0.6',
                        'lastmod' => $item->updated_at->toAtomString(),
                    ];
                }
            }

            // Add similar logic here for other models like 'products', 'services', etc.
        }

        // 3. Build XML Content
        $xml = $this->buildSitemapXml($urls);

        // 4. Save the File
        $path = public_path('sitemap.xml');
        File::put($path, $xml);

        $this->info("Sitemap successfully generated! File saved to: {$path}");
        $this->info('Total URLs included: ' . count($urls));
    }

    /**
     * Helper to construct the XML content from the URL array.
     *
     * @param array $urls
     * @return string
     */
    protected function buildSitemapXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $data) {
            $xml .= '    <url>' . PHP_EOL;
            $xml .= '        <loc>' . htmlspecialchars($data['loc']) . '</loc>' . PHP_EOL;

            if (isset($data['lastmod'])) {
                $xml .= '        <lastmod>' . htmlspecialchars($data['lastmod']) . '</lastmod>' . PHP_EOL;
            }
            if (isset($data['changefreq'])) {
                $xml .= '        <changefreq>' . htmlspecialchars($data['changefreq']) . '</changefreq>' . PHP_EOL;
            }
            if (isset($data['priority'])) {
                $xml .= '        <priority>' . htmlspecialchars($data['priority']) . '</priority>' . PHP_EOL;
            }

            $xml .= '    </url>' . PHP_EOL;
        }

        $xml .= '</urlset>' . PHP_EOL;

        return $xml;
    }
}
