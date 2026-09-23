<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface extends RepositoryInterface
{
    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    /**
     * @param  array{status?: string}  $filters
     */
    public function paginateForAdmin(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    public function findWithDetails(int $id): ?Order;
}
