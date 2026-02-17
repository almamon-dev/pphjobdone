<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebsiteService
{
    /**
     * Fetch basic content and metadata from a URL
     */
    public function fetchContent(string $url): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ])
                ->withoutVerifying() // Disable SSL verification for local/compatibility issues
                ->timeout(15)
                ->get($url);

            if (! $response->successful()) {
                return ['error' => 'Could not reach the website (Status: '.$response->status().').'];
            }

            $html = $response->body();

            // Extract Title (Robust Regex)
            $title = '';
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                $title = $matches[1];
            }

            // Extract Meta Description (Supports name="description" or property="og:description")
            $description = '';
            if (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches) ||
                preg_match('/<meta[^>]*content=["\'](.*?)["\'][^>]*name=["\']description["\']/is', $html, $matches)) {
                $description = $matches[1];
            } elseif (preg_match('/<meta[^>]*property=["\']og:description["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)) {
                $description = $matches[1];
            }

            // Extract Keywords
            $keywords = '';
            if (preg_match('/<meta[^>]*name=["\']keywords["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)) {
                $keywords = $matches[1];
            }

            // Extract body text
            $text = strip_tags($html);
            $text = preg_replace('/\s+/', ' ', $text);
            $text = mb_substr(trim($text), 0, 3000);

            // Extract Headings
            $headings = [];
            preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/is', $html, $hMatches);
            if (! empty($hMatches[1])) {
                $headings = array_slice($hMatches[1], 0, 15);
            }

            // Image and Link Count
            $imageCount = preg_match_all('/<img/i', $html, $unused);
            $linkCount = preg_match_all('/<a\s+href/i', $html, $unused);

            return [
                'url' => $url,
                'title' => html_entity_decode(trim($title)),
                'description' => html_entity_decode(trim($description)),
                'keywords' => trim($keywords),
                'content' => $text,
                'headings' => implode(', ', array_map(fn ($h) => trim(strip_tags($h)), $headings)),
                'image_count' => $imageCount,
                'link_count' => $linkCount,
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Website Fetch Error: ['.$url.'] '.$e->getMessage());

            return ['error' => 'Failed to fetch website content: '.$e->getMessage()];
        }
    }
}
