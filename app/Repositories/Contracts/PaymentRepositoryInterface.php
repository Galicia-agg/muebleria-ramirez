<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface extends RepositoryInterface
{
    public function pendingManualReview(int $perPage = 20): LengthAwarePaginator;
}
