<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function findOrFail(int $id): Order
    {
        return parent::findOrFail($id);
    }

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['items', 'payment'])
            ->where('user_id', $userId)
            ->orderByDesc('placed_at')
            ->paginate($perPage);
    }

    public function paginateForAdmin(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['user', 'payment']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('placed_at')->paginate($perPage)->withQueryString();
    }

    public function findWithDetails(int $id): ?Order
    {
        return $this->model->newQuery()
            ->with(['items.product.images', 'address', 'user', 'payment'])
            ->find($id);
    }
}
