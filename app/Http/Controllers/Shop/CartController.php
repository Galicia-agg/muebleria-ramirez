<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\AddToCartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Shop/Cart', [
            'cart' => $this->resolveCart($request)->load('items.product.images'),
        ]);
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $cart = $this->resolveCart($request);
        $product = Product::query()->findOrFail($request->integer('product_id'));

        try {
            $this->cartService->addItem($cart, $product, $request->integer('quantity'));
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()]);
        }

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:50']]);

        try {
            $this->cartService->updateQuantity($cartItem, $request->integer('quantity'));
        } catch (InsufficientStockException $e) {
            return back()->withErrors(['quantity' => $e->getMessage()]);
        }

        return back();
    }

    public function destroy(CartItem $cartItem): RedirectResponse
    {
        $this->cartService->removeItem($cartItem);

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    private function resolveCart(Request $request): Cart
    {
        if ($request->user()) {
            return $this->cartService->getOrCreateForUser($request->user());
        }

        $token = $request->session()->get('cart_token');

        if (! $token) {
            $token = $this->cartService->newSessionToken();
            $request->session()->put('cart_token', $token);
        }

        return $this->cartService->getOrCreateForSession($token);
    }
}
