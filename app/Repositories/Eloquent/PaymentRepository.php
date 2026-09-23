<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function pendingManualReview(int $perPage = 20): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['order.user'])
            ->where('method', 'transferencia')
            ->where('status', 'pendiente')
            ->orderBy('created_at')
            ->paginate($perPage);
    }
}
