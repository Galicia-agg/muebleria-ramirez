<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findOrFail(int $id): Product
    {
        return parent::findOrFail($id);
    }

    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['category', 'images'])
            ->orderByDesc('created_at');

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ilike', "%{$term}%")
                    ->orWhere('sku', 'ilike', "%{$term}%");
            });
        }

        if (($filters['stock'] ?? null) === 'low') {
            $query->where('active', true)->where('stock', '>', 0)->where('stock', '<=', 5);
        } elseif (($filters['stock'] ?? null) === 'out') {
            $query->where('active', true)->where('stock', 0);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function paginateForStorefront(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['images' => fn ($q) => $q->orderBy('position')->limit(1)])
            ->where('active', true)
            ->where('stock', '>', 0);

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters['category']));
        }

        if (! empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('name', 'ilike', "%{$term}%")
                    ->orWhere('description', 'ilike', "%{$term}%");
            });
        }

        if (! empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        match ($filters['sort'] ?? null) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('featured')->orderByDesc('created_at'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->model->newQuery()
            ->with(['category', 'images'])
            ->where('slug', $slug)
            ->first();
    }

    public function featured(int $limit = 8): Collection
    {
        return $this->model->newQuery()
            ->with(['images' => fn ($q) => $q->orderBy('position')->limit(1)])
            ->where('active', true)
            ->where('featured', true)
            ->where('stock', '>', 0)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
