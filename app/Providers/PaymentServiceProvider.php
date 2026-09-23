<?php

namespace App\Providers;

use App\Services\Payments\OnlinePaymentGatewayInterface;
use App\Services\Payments\RecurrentePaymentGateway;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OnlinePaymentGatewayInterface::class, function () {
            return new RecurrentePaymentGateway(
                config('services.recurrente.api_key'),
                config('services.recurrente.webhook_secret'),
            );
        });
    }
}
