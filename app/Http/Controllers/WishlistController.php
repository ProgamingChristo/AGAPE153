<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Request $request, Product $product)
    {
        $wishlist = $request->user()->wishlists()->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();

            return back()->with('status', 'Produk dihapus dari wishlist.');
        }

        $request->user()->wishlists()->create(['product_id' => $product->id]);

        return back()->with('status', 'Produk ditambahkan ke wishlist.');
    }
}
