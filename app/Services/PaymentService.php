<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\OnlinePaymentGatewayInterface;
use Illuminate\Http\UploadedFile;

class PaymentService
{
    public function __construct(
        private readonly OnlinePaymentGatewayInterface $onlineGateway,
    ) {
    }

    public function createManualPayment(Order $order, string $method, ?UploadedFile $proof = null): Payment
    {
        $path = null;

        if ($method === 'transferencia' && $proof) {
            $path = $proof->store('payment-proofs', 'public');
        }

        return Payment::query()->create([
            'order_id' => $order->id,
            'method' => $method,
            'status' => 'pendiente',
            'amount' => $order->total,
            'proof_path' => $path,
        ]);
    }

    /**
     * @return array{redirect_url: string, gateway_reference: string}
     */
    public function initiateOnlinePayment(Order $order): array
    {
        $session = $this->onlineGateway->createCheckoutSession($order);

        Payment::query()->create([
            'order_id' => $order->id,
            'method' => 'tarjeta_online',
            'status' => 'pendiente',
            'amount' => $order->total,
            'gateway_reference' => $session['gateway_reference'],
        ]);

        return $session;
    }

    public function verifyManualPayment(Payment $payment, User $admin): Payment
    {
        $payment->update([
            'status' => 'verificado',
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $payment->order->update(['status' => 'confirmado']);

        return $payment->fresh();
    }

    public function rejectManualPayment(Payment $payment, User $admin): Payment
    {
        $payment->update([
            'status' => 'fallido',
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        return $payment->fresh();
    }

    public function handleOnlineWebhook(array $payload, array $headers = []): bool
    {
        if (! $this->onlineGateway->verifyWebhookPayload($payload, $headers)) {
            return false;
        }

        $orderNumber = $this->onlineGateway->resolveOrderNumberFromPayload($payload);
        $order = $orderNumber ? Order::query()->where('order_number', $orderNumber)->first() : null;

        if (! $order || ! $order->payment) {
            return false;
        }

        $order->payment->update(['status' => 'pagado']);
        $order->update(['status' => 'confirmado']);

        return true;
    }
}
