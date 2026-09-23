<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface AddressRepositoryInterface extends RepositoryInterface
{
    public function forUser(int $userId): Collection;
}
