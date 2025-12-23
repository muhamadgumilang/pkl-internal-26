<?php
namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    public function created(Product $product): void
    {
        Cache::forget('featured_products');
        Cache::forget('category_' . $product->category_id . '_products');

        // Logging native Laravel (AMAN)
        if (auth()->check()) {
            Log::info('Produk baru dibuat', [
                'product_id' => $product->id,
                'name'       => $product->name,
                'user_id'    => auth()->id(),
            ]);
        }
    }

    public function updated(Product $product): void
    {
        Cache::forget('product_' . $product->id);
        Cache::forget('featured_products');

        if ($product->isDirty('category_id')) {
            Cache::forget('category_' . $product->getOriginal('category_id') . '_products');
            Cache::forget('category_' . $product->category_id . '_products');
        }

        if (auth()->check()) {
            Log::info('Produk diupdate', [
                'product_id' => $product->id,
                'changes'    => $product->getChanges(),
                'user_id'    => auth()->id(),
            ]);
        }
    }

    public function deleted(Product $product): void
    {
        Cache::forget('product_' . $product->id);
        Cache::forget('featured_products');
        Cache::forget('category_' . $product->category_id . '_products');

        if (auth()->check()) {
            Log::warning('Produk dihapus', [
                'product_id' => $product->id,
                'name'       => $product->name,
                'user_id'    => auth()->id(),
            ]);
        }
    }
}
