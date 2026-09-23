<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends RepositoryInterface
{
    public function paginateForAdmin(int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array{category?: string, search?: string, min_price?: float, max_price?: float, sort?: string}  $filters
     */
    public function paginateForStorefront(array $filters = [], int $perPage = 12): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Product;

    public function featured(int $limit = 8): Collection;
}
