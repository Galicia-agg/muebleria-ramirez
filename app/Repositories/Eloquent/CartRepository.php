<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function findForUser(int $userId): ?Cart
    {
        return $this->model->newQuery()
            ->with('items.product.images')
            ->where('user_id', $userId)
            ->first();
    }

    public function findBySessionToken(string $token): ?Cart
    {
        return $this->model->newQuery()
            ->with('items.product.images')
            ->where('session_token', $token)
            ->first();
    }
}
