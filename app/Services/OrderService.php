<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly PaymentService $paymentService,
    ) {
    }

    public function checkout(
        Cart $cart,
        User $user,
        ?Address $address,
        string $deliveryMethod,
        string $paymentMethod,
        float $shippingCost = 0,
        ?UploadedFile $paymentProof = null,
    ): Order {
        $cart->loadMissing('items.product');

        if ($cart->items->isEmpty()) {
            throw new InvalidArgumentException('El carrito está vacío.');
        }

        if ($deliveryMethod === 'domicilio' && ! $address) {
            throw new InvalidArgumentException('Debes seleccionar una dirección de envío.');
        }

        return DB::transaction(function () use ($cart, $user, $address, $deliveryMethod, $paymentMethod, $shippingCost, $paymentProof) {
            $subtotal = 0;

            foreach ($cart->items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    throw new InsufficientStockException(
                        "Stock insuficiente para \"{$product->name}\": disponible {$product->stock}."
                    );
                }

                $subtotal += $item->quantity * $item->unit_price;
            }

            $total = $subtotal + $shippingCost;

            $order = Order::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $user->id,
                'address_id' => $address?->id,
                'delivery_method' => $deliveryMethod,
                'status' => 'pendiente',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'placed_at' => now(),
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->quantity * $item->unit_price,
                ]);

                Product::query()->whereKey($item->product_id)->decrement('stock', $item->quantity);
            }

            if ($paymentMethod === 'tarjeta_online') {
                $this->paymentService->initiateOnlinePayment($order);
            } else {
                $this->paymentService->createManualPayment($order, $paymentMethod, $paymentProof);
            }

            $this->cartService->clear($cart);

            return $order->fresh(['items', 'payment']);
        });
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh();
    }

    private function generateOrderNumber(): string
    {
        return 'MR-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
    }
}
