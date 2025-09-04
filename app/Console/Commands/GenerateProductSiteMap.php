<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateProductSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemap for products and categories';

    public function handle()
    {
        $this->info('Generating XML sitemap...');

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        // Add homepage
        $url = $xml->addChild('url');
        $url->addChild('loc', url('/'));
        $url->addChild('changefreq', 'daily');
        $url->addChild('priority', '1.0');
        $url->addChild('lastmod', now()->toAtomString());

        // Add category pages
        $categories = Category::where('is_active', true)->get();
        foreach ($categories as $category) {
            $url = $xml->addChild('url');
            $url->addChild('loc', route('products.category', $category->slug));
            $url->addChild('changefreq', 'weekly');
            $url->addChild('priority', '0.8');
            $url->addChild('lastmod', $category->updated_at->toAtomString());
        }

        // Add product pages
        Product::where('is_active', true)
            ->chunk(100, function ($products) use ($xml) {
                foreach ($products as $product) {
                    $url = $xml->addChild('url');
                    $url->addChild('loc', route('products.show', $product->slug));
                    $url->addChild('changefreq', 'weekly');
                    $url->addChild('priority', '0.6');
                    $url->addChild('lastmod', $product->updated_at->toAtomString());
                }
            });

        // Add static pages
        $staticPages = [
            ['url' => route('about'), 'priority' => '0.5'],
            ['url' => route('contact'), 'priority' => '0.5'],
            ['url' => route('privacy'), 'priority' => '0.3'],
            ['url' => route('terms'), 'priority' => '0.3'],
        ];

        foreach ($staticPages as $page) {
            $url = $xml->addChild('url');
            $url->addChild('loc', $page['url']);
            $url->addChild('changefreq', 'monthly');
            $url->addChild('priority', $page['priority']);
        }

        // Save sitemap to public directory
        $sitemapContent = $xml->asXML();
        Storage::disk('public')->put('sitemap.xml', $sitemapContent);

        $this->info('Sitemap generated successfully at: ' . Storage::disk('public')->path('sitemap.xml'));
        return 0;
    }
}