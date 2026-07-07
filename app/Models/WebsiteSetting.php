<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function value(string $key, ?string $default = null): ?string
    {
        return cache()->remember("setting:{$key}", 3600, function () use ($key, $default) {
            return static::query()->where('key', $key)->value('value') ?? $default;
        });
    }
}
