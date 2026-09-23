<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CatalogService
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {
    }

    /**
     * @param  UploadedFile[]  $images
     */
    public function createProduct(array $data, array $images = []): Product
    {
        if (count($images) > Product::MAX_IMAGES) {
            throw new InvalidArgumentException('Un producto admite un máximo de '.Product::MAX_IMAGES.' fotografías.');
        }

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        return DB::transaction(function () use ($data, $images) {
            $product = $this->products->create($data);

            $this->storeImages($product, $images);

            return $product->load('images');
        });
    }

    /**
     * @param  UploadedFile[]  $images
     */
    public function updateProduct(Product $product, array $data, array $images = []): Product
    {
        $existingCount = $product->images()->count();

        if ($existingCount + count($images) > Product::MAX_IMAGES) {
            throw new InvalidArgumentException('Un producto admite un máximo de '.Product::MAX_IMAGES.' fotografías.');
        }

        return DB::transaction(function () use ($product, $data, $images) {
            $this->products->update($product, $data);
            $this->storeImages($product, $images);

            return $product->fresh('images');
        });
    }

    public function removeImage(ProductImage $image): void
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }

    /**
     * @param  int[]  $orderedImageIds
     */
    public function reorderImages(Product $product, array $orderedImageIds): void
    {
        foreach ($orderedImageIds as $position => $imageId) {
            ProductImage::query()
                ->where('id', $imageId)
                ->where('product_id', $product->id)
                ->update(['position' => $position]);
        }
    }

    /**
     * @param  UploadedFile[]  $images
     */
    private function storeImages(Product $product, array $images): void
    {
        $nextPosition = ($product->images()->max('position') ?? -1) + 1;

        foreach ($images as $index => $image) {
            $path = $image->store('products', 'public');

            $product->images()->create([
                'path' => $path,
                'position' => $nextPosition + $index,
            ]);
        }
    }
}
