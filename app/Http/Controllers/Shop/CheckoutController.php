<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Models\Address;
use App\Repositories\Contracts\AddressRepositoryInterface;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    // Envío nacional incluido en el precio del producto: no se cobra por separado.
    private const SHIPPING_COST = 0.00;

    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
        private readonly AddressRepositoryInterface $addresses,
    ) {}

    public function create(Request $request): Response
    {
        $cart = $this->cartService->getOrCreateForUser($request->user());

        return Inertia::render('Shop/Checkout', [
            'cart' => $cart->load('items.product'),
            'addresses' => $this->addresses->forUser($request->user()->id),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();
        $cart = $this->cartService->getOrCreateForUser($user);

        $address = $request->filled('address_id')
            ? Address::query()->where('user_id', $user->id)->find($request->input('address_id'))
            : null;

        try {
            $order = $this->orderService->checkout(
                cart: $cart,
                user: $user,
                address: $address,
                deliveryMethod: $request->input('delivery_method'),
                paymentMethod: $request->input('payment_method'),
                shippingCost: self::SHIPPING_COST,
                paymentProof: $request->file('payment_proof'),
            );
        } catch (InsufficientStockException|InvalidArgumentException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        return redirect()->route('shop.orders.show', $order->id)
            ->with('success', 'Pedido realizado correctamente. Número de pedido: '.$order->order_number);
    }
}
