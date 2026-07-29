<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

final class ProductCatalog
{
    /**
     * Catalog photographs that ship with the application.
     *
     * @var array<string, string>
     */
    private const IMAGE_BY_SKU = [
        'SP-NUTMEG-MACE' => 'nutmeg-pala-whole-mace.jpg',
        'SP-CLOVE' => 'cloves-cengkeh.jpg',
        'SP-WHITE-PEPPER' => 'white-pepper.jpg',
        'SP-BLACK-PEPPER' => 'black-pepper.jpg',
        'SP-DRIED-CHILI' => 'dried-chili-cabe-kering.jpg',
        'SP-CHILI-POWDER' => 'chili-powder-cabe-bubuk.jpg',
        'SP-GARLIC' => 'garlic-bawang-putih.jpg',
        'HR-TURMERIC' => 'turmeric-kunyit.jpg',
        'HR-GALANGAL' => 'galangal-lengkuas.jpg',
        'HR-TEMULAWAK' => 'curcuma-xanthorrhiza-temulawak.jpg',
        'AG-PAPAYA-LEAVES' => 'papaya-leaves-daun-papaya.jpg',
        'AG-BANANA-STEM' => 'banana-stem-batang-pisang.jpg',
        'CF-ROBUSTA-GREEN' => 'robusta-green-beans.jpg',
        'CF-ARABICA-ROASTED' => 'arabica-roasted.png',
        'RB-JBI-A' => 'robusta-green-beans.jpg',
        'AR-GYO-P' => 'arabica-roasted.png',
        'AR-TRJ-S' => 'arabica-roasted.png',
        'AR-KNT-B' => 'arabica-roasted.png',
        'RB-LPG-C' => 'robusta-green-beans.jpg',
        'SP-LD-H' => 'black-pepper.jpg',
        'SP-LD-P' => 'white-pepper.jpg',
        'SP-CGK' => 'cloves-cengkeh.jpg',
        'SP-KM-C' => 'cassia-cinnamon.jpg',
        'SP-JHE-K' => 'dried-ginger.jpg',
        'SP-KNY-K' => 'turmeric-kunyit.jpg',
        'SP-PLA' => 'nutmeg-pala-whole-mace.jpg',
    ];

    public static function name(Product $product): string
    {
        return self::translated($product, 'name') ?: $product->name;
    }

    public static function shortDescription(Product $product): string
    {
        return self::translated($product, 'short_description')
            ?: trim((string) $product->short_description);
    }

    public static function description(Product $product): string
    {
        $translated = self::translated($product, 'description');

        if ($translated) {
            return $translated;
        }

        $shortDescription = self::shortDescription($product);
        $suffix = self::siteText('product.catalog_description_suffix');

        return trim($shortDescription."\n\n".$suffix);
    }

    public static function categoryName(?Category $category): string
    {
        if (! $category) {
            return self::siteText('home.uncategorized', 'Uncategorized');
        }

        $key = 'products.categories.'.Str::slug($category->slug ?: $category->name, '_');
        $translated = trans($key);

        if (is_string($translated) && $translated !== $key) {
            return $translated;
        }

        return $category->name;
    }

    public static function categoryDescription(Category $category): string
    {
        $key = 'products.category_descriptions.'.Str::slug($category->slug ?: $category->name, '_');
        $translated = trans($key);

        if (is_string($translated) && $translated !== $key) {
            return $translated;
        }

        return trim((string) $category->description);
    }

    public static function imageFilename(Product $product): ?string
    {
        $sku = strtoupper(trim((string) $product->sku));

        if (isset(self::IMAGE_BY_SKU[$sku])) {
            return self::IMAGE_BY_SKU[$sku];
        }

        $name = Str::lower($product->name);

        return match (true) {
            Str::contains($name, ['white pepper', 'lada putih']) => 'white-pepper.jpg',
            Str::contains($name, ['black pepper', 'lada hitam']) => 'black-pepper.jpg',
            Str::contains($name, ['clove', 'cengkeh']) => 'cloves-cengkeh.jpg',
            Str::contains($name, ['nutmeg', 'pala']) => 'nutmeg-pala-whole-mace.jpg',
            Str::contains($name, ['cassia', 'cinnamon', 'kayu manis']) => 'cassia-cinnamon.jpg',
            Str::contains($name, ['ginger', 'jahe']) => 'dried-ginger.jpg',
            Str::contains($name, ['curcuma', 'temulawak']) => 'curcuma-xanthorrhiza-temulawak.jpg',
            Str::contains($name, ['turmeric', 'kunyit']) => 'turmeric-kunyit.jpg',
            Str::contains($name, ['galangal', 'lengkuas']) => 'galangal-lengkuas.jpg',
            Str::contains($name, ['dried chili', 'cabe kering', 'cabai kering']) => 'dried-chili-cabe-kering.jpg',
            Str::contains($name, ['chili powder', 'cabe bubuk', 'cabai bubuk']) => 'chili-powder-cabe-bubuk.jpg',
            Str::contains($name, ['garlic', 'bawang putih']) => 'garlic-bawang-putih.jpg',
            Str::contains($name, ['papaya leave', 'daun papaya', 'daun pepaya']) => 'papaya-leaves-daun-papaya.jpg',
            Str::contains($name, ['banana stem', 'batang pisang']) => 'banana-stem-batang-pisang.jpg',
            Str::contains($name, ['arabica', 'arabika']) => 'arabica-roasted.png',
            Str::contains($name, ['robusta']) => 'robusta-green-beans.jpg',
            default => null,
        };
    }

    public static function imageUrl(Product $product): ?string
    {
        $rawUrl = trim((string) $product->getRawOriginal('image_url'));
        $isAdminUpload = Str::contains($rawUrl, ['/storage/', 'storage/', '/media/']);
        $isCustomRemote = Str::startsWith($rawUrl, ['http://', 'https://'])
            && ! Str::contains($rawUrl, ['images.unsplash.com', 'source.unsplash.com']);

        if ($rawUrl !== '' && ($isAdminUpload || $isCustomRemote)) {
            return $product->image_url;
        }

        $filename = self::imageFilename($product);

        if ($filename && is_file(public_path('images/catalog/'.$filename))) {
            return route('catalog-media.show', ['filename' => $filename]);
        }

        return $product->image_url;
    }

    public static function detailLabel(string $label): string
    {
        $normalized = Str::slug(
            preg_replace('/\bproduct\s+name\b/i', 'item name', trim($label)) ?: $label,
            '_'
        );
        $key = "products.detail_labels.{$normalized}";
        $translated = trans($key);

        return is_string($translated) && $translated !== $key
            ? $translated
            : (strcasecmp($label, 'Product Name') === 0 ? 'Item Name' : $label);
    }

    public static function detailValue(string $value): string
    {
        $key = 'products.detail_values.'.Str::slug($value, '_');
        $translated = trans($key);

        return is_string($translated) && $translated !== $key
            ? $translated
            : $value;
    }

    private static function translated(Product $product, string $field): ?string
    {
        $key = self::translationKey($product);

        if (! $key) {
            return null;
        }

        $translationKey = "products.items.{$key}.{$field}";
        $translated = trans($translationKey);

        return is_string($translated) && $translated !== $translationKey
            ? trim($translated)
            : null;
    }

    private static function translationKey(Product $product): ?string
    {
        $sku = trim((string) $product->sku);

        return $sku === '' ? null : Str::lower(str_replace('-', '_', $sku));
    }

    private static function siteText(string $key, string $fallback = ''): string
    {
        $translations = trans('site');

        return is_array($translations) && is_string($translations[$key] ?? null)
            ? $translations[$key]
            : $fallback;
    }
}
