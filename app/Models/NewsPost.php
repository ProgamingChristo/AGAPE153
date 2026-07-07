<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image_url',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($inner): void {
                $inner->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }
}
