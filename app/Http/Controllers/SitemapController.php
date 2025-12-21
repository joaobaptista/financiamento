<?php

namespace App\Http\Controllers;

use App\Domain\Campaign\Campaign;
use App\Enums\CampaignStatus;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember('seo:sitemap.xml', now()->addHour(), function (): string {
            $baseUrl = rtrim((string) config('app.url'), '/');

            $staticPaths = [
                '/',
                '/campaigns',
                '/about',
                '/how-it-works',
                '/blog',
                '/team',
                '/press',
                '/retrospectiva-2020',
                '/support',
                '/contact',
                '/updates',
                '/fees',
                '/security',
                '/terms',
                '/privacy',
                '/popular',
                '/no-ar',
                '/finalizados',
                '/assinaturas',
            ];

            $urls = [];

            foreach ($staticPaths as $path) {
                $urls[] = [
                    'loc' => $baseUrl . $path,
                    'lastmod' => now()->toDateString(),
                    'changefreq' => $path === '/' || $path === '/campaigns' ? 'daily' : 'weekly',
                    'priority' => $path === '/' ? '1.0' : ($path === '/campaigns' ? '0.8' : '0.6'),
                ];
            }

            $campaigns = Campaign::query()
                ->whereIn('status', [
                    CampaignStatus::Active->value,
                    CampaignStatus::Successful->value,
                    CampaignStatus::Failed->value,
                ])
                ->orderByDesc('updated_at')
                ->get(['slug', 'updated_at', 'status']);

            foreach ($campaigns as $campaign) {
                $isActive = ($campaign->status instanceof \BackedEnum)
                    ? $campaign->status === CampaignStatus::Active
                    : ((string) $campaign->status === CampaignStatus::Active->value);

                $urls[] = [
                    'loc' => $baseUrl . '/campaigns/' . $campaign->slug,
                    'lastmod' => optional($campaign->updated_at)->toDateString() ?? now()->toDateString(),
                    'changefreq' => $isActive ? 'daily' : 'monthly',
                    'priority' => $isActive ? '0.8' : '0.5',
                ];
            }

            $lines = [];
            $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
            $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            foreach ($urls as $u) {
                $lines[] = '  <url>';
                $lines[] = '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc>';
                $lines[] = '    <lastmod>' . htmlspecialchars($u['lastmod'], ENT_XML1) . '</lastmod>';
                $lines[] = '    <changefreq>' . htmlspecialchars($u['changefreq'], ENT_XML1) . '</changefreq>';
                $lines[] = '    <priority>' . htmlspecialchars($u['priority'], ENT_XML1) . '</priority>';
                $lines[] = '  </url>';
            }

            $lines[] = '</urlset>';

            return implode("\n", $lines) . "\n";
        });

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
