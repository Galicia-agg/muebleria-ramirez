<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const MAX_IMAGES = 10;

    protected $fillable = [
        'category_id', 'sku', 'slug', 'name', 'description', 'price', 'compare_at_price',
        'stock', 'material', 'color', 'width_cm', 'height_cm', 'depth_cm', 'weight_kg',
        'featured', 'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'width_cm' => 'decimal:1',
            'height_cm' => 'decimal:1',
            'depth_cm' => 'decimal:1',
            'weight_kg' => 'decimal:2',
            'featured' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
