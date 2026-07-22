<?php

namespace App\Support;

use Illuminate\Support\Str;

class MediaUrl
{
    public static function public(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $path = ltrim(parse_url($value, PHP_URL_PATH) ?: $value, '/');

        if (Str::startsWith($path, 'storage/')) {
            return url('media/'.self::encodePath(Str::after($path, 'storage/')));
        }

        if (Str::startsWith($path, 'app/public/')) {
            return url('media/'.self::encodePath(Str::after($path, 'app/public/')));
        }

        if (Str::startsWith($value, ['http://', 'https://', '//', 'data:'])) {
            return $value;
        }

        return asset($path);
    }

    private static function encodePath(string $path): string
    {
        return collect(explode('/', trim($path, '/')))
            ->map(fn (string $segment): string => rawurlencode($segment))
            ->implode('/');
    }
}
