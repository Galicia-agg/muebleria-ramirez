<?php

namespace App\Services\Payments;

use App\Exceptions\PaymentGatewayNotConfiguredException;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class RecurrentePaymentGateway implements OnlinePaymentGatewayInterface
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly ?string $webhookSecret,
    ) {
    }

    public function createCheckoutSession(Order $order): array
    {
        if (! $this->apiKey) {
            throw new PaymentGatewayNotConfiguredException(
                'Recurrente no está configurado. Define RECURRENTE_API_KEY en .env para habilitar pagos en línea.'
            );
        }

        $response = Http::withToken($this->apiKey)
            ->post('https://app.recurrente.com/api/checkouts', [
                'items' => [[
                    'name' => "Pedido {$order->order_number}",
                    'amount_in_cents' => (int) round($order->total * 100),
                    'currency' => 'GTQ',
                    'quantity' => 1,
                ]],
                'metadata' => ['order_number' => $order->order_number],
                'success_url' => route('checkout.success', $order->order_number),
                'cancel_url' => route('checkout.cancelled', $order->order_number),
            ])
            ->throw()
            ->json();

        return [
            'redirect_url' => $response['checkout_url'],
            'gateway_reference' => $response['id'],
        ];
    }

    public function verifyWebhookPayload(array $payload, array $headers = []): bool
    {
        if (! $this->webhookSecret) {
            return false;
        }

        // Recurrente signs webhooks; validate the signature header here
        // against $this->webhookSecret before trusting the payload.
        return isset($payload['event_type'], $payload['data']);
    }

    public function resolveOrderNumberFromPayload(array $payload): ?string
    {
        return $payload['data']['metadata']['order_number'] ?? null;
    }
}
