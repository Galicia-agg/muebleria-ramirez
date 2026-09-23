<?php

namespace App\Services\Payments;

use App\Models\Order;

interface OnlinePaymentGatewayInterface
{
    /**
     * Start a hosted checkout for the order and return the data the
     * frontend needs to redirect the customer to pay (e.g. a URL).
     *
     * @return array{redirect_url: string, gateway_reference: string}
     */
    public function createCheckoutSession(Order $order): array;

    /**
     * Verify an incoming webhook payload belongs to a legitimate,
     * successfully paid transaction for one of our orders.
     */
    public function verifyWebhookPayload(array $payload, array $headers = []): bool;

    public function resolveOrderNumberFromPayload(array $payload): ?string;
}
