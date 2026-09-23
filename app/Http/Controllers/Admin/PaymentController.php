<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentRepositoryInterface $payments,
        private readonly PaymentService $paymentService,
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Payments/Index', [
            'payments' => $this->payments->pendingManualReview(),
        ]);
    }

    public function verify(Payment $payment): RedirectResponse
    {
        $this->paymentService->verifyManualPayment($payment, request()->user());

        return back()->with('success', 'Pago verificado y pedido confirmado.');
    }

    public function reject(Payment $payment): RedirectResponse
    {
        $this->paymentService->rejectManualPayment($payment, request()->user());

        return back()->with('success', 'Pago rechazado.');
    }
}
