<?php

namespace App\Repositories\Contracts;

use App\Models\Cart;

interface CartRepositoryInterface extends RepositoryInterface
{
    public function findForUser(int $userId): ?Cart;

    public function findBySessionToken(string $token): ?Cart;
}
