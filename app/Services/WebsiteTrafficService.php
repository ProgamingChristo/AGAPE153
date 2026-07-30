<?php

namespace App\Services;

use App\Models\WebsiteVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class WebsiteTrafficService
{
    private const PUBLIC_STATS_CACHE_KEY = 'website-traffic:public-stats';

    public function track(Request $request, Response $response): void
    {
        if (! $this->shouldTrack($request, $response) || ! $this->tableAvailable()) {
            return;
        }

        try {
            $visitorHash = $this->visitorHash($request);
            $path = '/'.ltrim($request->path(), '/');
            $dedupeKey = 'website-traffic:dedupe:'.hash('sha256', $visitorHash.'|'.$path);

            if (! Cache::add($dedupeKey, true, now()->addMinutes(5))) {
                return;
            }

            WebsiteVisit::query()->create([
                'user_id' => $request->user()?->id,
                'visitor_hash' => $visitorHash,
                'session_hash' => $this->sessionHash($request),
                'path' => Str::limit($path, 191, ''),
                'route_name' => Str::limit((string) $request->route()?->getName(), 120, '') ?: null,
                'locale' => app()->getLocale(),
                'source_host' => $this->sourceHost($request),
                'device' => $this->device($request->userAgent()),
                'browser' => $this->browser($request->userAgent()),
            ]);

            Cache::forget(self::PUBLIC_STATS_CACHE_KEY);
        } catch (Throwable) {
            // Traffic collection must never interrupt a visitor's request.
        }
    }

    /**
     * @return array{total_views: int, unique_visitors: int, today_views: int, online_now: int}
     */
    public function publicStats(): array
    {
        $empty = [
            'total_views' => 0,
            'unique_visitors' => 0,
            'today_views' => 0,
            'online_now' => 0,
        ];

        if (! $this->tableAvailable()) {
            return $empty;
        }

        try {
            return Cache::remember(self::PUBLIC_STATS_CACHE_KEY, now()->addMinute(), fn (): array => [
                'total_views' => WebsiteVisit::query()->count(),
                'unique_visitors' => WebsiteVisit::query()->distinct()->count('visitor_hash'),
                'today_views' => WebsiteVisit::query()->where('created_at', '>=', now()->startOfDay())->count(),
                'online_now' => WebsiteVisit::query()
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->distinct()
                    ->count('visitor_hash'),
            ]);
        } catch (Throwable) {
            return $empty;
        }
    }

    /**
     * @return array<int, array{date: string, label: string, views: int, visitors: int}>
     */
    public function dailyTrend(int $days = 14): array
    {
        if (! $this->tableAvailable()) {
            return [];
        }

        try {
            $start = now()->startOfDay()->subDays($days - 1);
            $rows = WebsiteVisit::query()
                ->selectRaw('DATE(created_at) as visit_date, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
                ->where('created_at', '>=', $start)
                ->groupByRaw('DATE(created_at)')
                ->get()
                ->keyBy('visit_date');

            return collect(range(0, $days - 1))
                ->map(function (int $offset) use ($start, $rows): array {
                    $date = $start->copy()->addDays($offset);
                    $row = $rows->get($date->toDateString());

                    return [
                        'date' => $date->toDateString(),
                        'label' => $date->format('d M'),
                        'views' => (int) ($row->views ?? 0),
                        'visitors' => (int) ($row->visitors ?? 0),
                    ];
                })
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    public function topPages(int $limit = 8)
    {
        if (! $this->tableAvailable()) {
            return collect();
        }

        try {
            return WebsiteVisit::query()
                ->selectRaw('path, COUNT(*) as views, COUNT(DISTINCT visitor_hash) as visitors')
                ->groupBy('path')
                ->orderByDesc('views')
                ->limit($limit)
                ->get();
        } catch (Throwable) {
            return collect();
        }
    }

    public function deviceTraffic()
    {
        if (! $this->tableAvailable()) {
            return collect();
        }

        try {
            return WebsiteVisit::query()
                ->selectRaw('device, COUNT(*) as total')
                ->groupBy('device')
                ->orderByDesc('total')
                ->get();
        } catch (Throwable) {
            return collect();
        }
    }

    public function sourceTraffic(int $limit = 8)
    {
        if (! $this->tableAvailable()) {
            return collect();
        }

        try {
            return WebsiteVisit::query()
                ->selectRaw('COALESCE(source_host, ?) as source, COUNT(*) as total', ['Direct'])
                ->groupBy('source_host')
                ->orderByDesc('total')
                ->limit($limit)
                ->get();
        } catch (Throwable) {
            return collect();
        }
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || ! $response->isSuccessful()) {
            return false;
        }

        if ($request->header('DNT') === '1' || $this->isBot($request->userAgent())) {
            return false;
        }

        if ($request->is('admin', 'admin/*', 'media/*', 'catalog-media/*', 'up', 'robots.txt', 'sitemap.xml')) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type');

        return $contentType === '' || str_contains($contentType, 'text/html');
    }

    private function tableAvailable(): bool
    {
        try {
            return Schema::hasTable('website_visits');
        } catch (Throwable) {
            return false;
        }
    }

    private function visitorHash(Request $request): string
    {
        $seed = implode('|', [
            $request->ip() ?: 'unknown',
            $request->userAgent() ?: 'unknown',
        ]);

        return hash_hmac('sha256', $seed, (string) config('app.key', 'agape153'));
    }

    private function sessionHash(Request $request): ?string
    {
        if (! $request->hasSession() || $request->session()->getId() === '') {
            return null;
        }

        return hash_hmac('sha256', $request->session()->getId(), (string) config('app.key', 'agape153'));
    }

    private function sourceHost(Request $request): ?string
    {
        $referrer = $request->headers->get('referer');

        if (! $referrer) {
            return null;
        }

        $host = parse_url($referrer, PHP_URL_HOST);

        if (! is_string($host) || $host === '' || strcasecmp($host, $request->getHost()) === 0) {
            return null;
        }

        return Str::limit(Str::lower($host), 255, '');
    }

    private function device(?string $userAgent): string
    {
        $userAgent = Str::lower($userAgent ?? '');

        return match (true) {
            Str::contains($userAgent, ['ipad', 'tablet']) => 'tablet',
            Str::contains($userAgent, ['mobile', 'android', 'iphone']) => 'mobile',
            default => 'desktop',
        };
    }

    private function browser(?string $userAgent): string
    {
        $userAgent = Str::lower($userAgent ?? '');

        return match (true) {
            Str::contains($userAgent, ['edg/', 'edge/']) => 'Edge',
            Str::contains($userAgent, ['opr/', 'opera']) => 'Opera',
            Str::contains($userAgent, ['chrome/', 'crios/']) => 'Chrome',
            Str::contains($userAgent, ['firefox/', 'fxios/']) => 'Firefox',
            Str::contains($userAgent, ['safari/']) => 'Safari',
            default => 'Other',
        };
    }

    private function isBot(?string $userAgent): bool
    {
        return (bool) preg_match(
            '/bot|crawler|spider|slurp|bingpreview|facebookexternalhit|whatsapp|telegrambot|headless/i',
            $userAgent ?? ''
        );
    }
}
