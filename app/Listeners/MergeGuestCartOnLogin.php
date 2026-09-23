<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class MergeGuestCartOnLogin
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly Request $request,
    ) {
    }

    public function handle(Login $event): void
    {
        $token = $this->request->session()->get('cart_token');

        if (! $token) {
            return;
        }

        $this->cartService->mergeGuestCartIntoUser($token, $event->user);
        $this->request->session()->forget('cart_token');
    }
}
