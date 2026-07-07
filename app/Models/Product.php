<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'origin',
        'grade',
        'unit',
        'price',
        'currency',
        'min_order_quantity',
        'stock_quantity',
        'is_featured',
        'is_active',
        'export_ready',
        'image_url',
        'meta_title',
        'meta_description',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'export_ready' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($inner) use ($term): void {
            $inner->where('name', 'like', "%{$term}%")
                ->orWhere('origin', 'like', "%{$term}%")
                ->orWhere('grade', 'like', "%{$term}%")
                ->orWhere('short_description', 'like', "%{$term}%");
        });
    }

    public function formattedPrice(): string
    {
        if ($this->price === null) {
            return 'Hubungi kami';
        }

        return 'Rp'.number_format((float) $this->price, 0, ',', '.').'/'.$this->unit;
    }
}
