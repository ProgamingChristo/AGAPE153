<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'product_details',
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
        'video_url',
        'meta_title',
        'meta_description',
        'view_count',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'export_ready' => 'boolean',
            'product_details' => 'array',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value): ?string => MediaUrl::public($value),
        );
    }

    protected function videoUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value): ?string => MediaUrl::public($value),
        );
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

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function publishedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('status', 'published')->latest();
    }

    public function averageRating(): float
    {
        return round((float) $this->publishedReviews()->avg('rating'), 1);
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
        $rawPrice = $this->getRawOriginal('price');

        if ($rawPrice === null || $rawPrice === '' || ! is_numeric($rawPrice)) {
            return 'Hubungi kami';
        }

        return 'Rp'.number_format((float) $rawPrice, 0, ',', '.').'/'.$this->unit;
    }

    public function formattedMoq(): string
    {
        return $this->formattedQuantity($this->min_order_quantity);
    }

    public function formattedStock(): string
    {
        return $this->formattedQuantity($this->stock_quantity);
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function buyerDetails(): array
    {
        $details = $this->normalizedDetails();

        return [
            ['label' => 'Product', 'value' => $details['product'] ?? $this->name],
            ['label' => 'Origin', 'value' => $details['origin'] ?? ($this->origin ?: 'Indonesia')],
            ['label' => 'Quality', 'value' => $details['quality'] ?? 'Export Quality'],
            ['label' => 'Moisture Content', 'value' => $details['moisturecontent'] ?? $details['moistureconten'] ?? $details['moisture'] ?? 'Max. 13%'],
            ['label' => 'Packaging', 'value' => $details['packaging'] ?? '20 kg PP Bag'],
        ];
    }

    private function formattedQuantity(int|float|null $quantity): string
    {
        $value = (float) ($quantity ?? 0);
        $formatted = number_format($value, 0, '.', ',');
        $unit = strtolower($this->unit) === 'kg' ? 'kgs' : $this->unit;

        return "{$formatted} {$unit}";
    }

    /**
     * @return array<string, string>
     */
    private function normalizedDetails(): array
    {
        $details = [];

        foreach ($this->product_details ?? [] as $detail) {
            $label = (string) ($detail['label'] ?? '');
            $value = trim((string) ($detail['value'] ?? ''));

            if ($label === '' || $value === '') {
                continue;
            }

            $key = strtolower((string) preg_replace('/[^a-z0-9]+/i', '', $label));
            $details[$key] = $value;
        }

        return $details;
    }

    public function videoEmbedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $url = $this->video_url;

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        if (preg_match('/\.(html?|php)$/i', $path)) {
            return $url;
        }

        return null;
    }

    public function videoFileUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        $path = parse_url($this->video_url, PHP_URL_PATH) ?: $this->video_url;

        return preg_match('/\.(mp4|webm|ogg)$/i', $path) ? $this->video_url : null;
    }
}
