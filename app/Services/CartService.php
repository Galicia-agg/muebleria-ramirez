<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $carts,
    ) {
    }

    public function getOrCreateForUser(User $user): Cart
    {
        return $this->carts->findForUser($user->id)
            ?? $this->carts->create(['user_id' => $user->id]);
    }

    public function getOrCreateForSession(string $sessionToken): Cart
    {
        return $this->carts->findBySessionToken($sessionToken)
            ?? $this->carts->create(['session_token' => $sessionToken]);
    }

    public function newSessionToken(): string
    {
        return (string) Str::uuid();
    }

    public function addItem(Cart $cart, Product $product, int $quantity): CartItem
    {
        if ($product->stock < $quantity) {
            throw new InsufficientStockException(
                "Stock insuficiente para \"{$product->name}\": disponible {$product->stock}."
            );
        }

        $item = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = $quantity + ($item?->quantity ?? 0);

        if ($product->stock < $newQuantity) {
            throw new InsufficientStockException(
                "Stock insuficiente para \"{$product->name}\": disponible {$product->stock}."
            );
        }

        if ($item) {
            $item->update(['quantity' => $newQuantity, 'unit_price' => $product->price]);

            return $item->fresh();
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);
    }

    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        if ($item->product->stock < $quantity) {
            throw new InsufficientStockException(
                "Stock insuficiente para \"{$item->product->name}\": disponible {$item->product->stock}."
            );
        }

        $item->update(['quantity' => $quantity]);

        return $item->fresh();
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function mergeGuestCartIntoUser(string $sessionToken, User $user): Cart
    {
        return DB::transaction(function () use ($sessionToken, $user) {
            $guestCart = $this->carts->findBySessionToken($sessionToken);
            $userCart = $this->getOrCreateForUser($user);

            if (! $guestCart || $guestCart->is($userCart)) {
                return $userCart;
            }

            foreach ($guestCart->items as $guestItem) {
                $existing = $userCart->items()->where('product_id', $guestItem->product_id)->first();

                if ($existing) {
                    $existing->update(['quantity' => $existing->quantity + $guestItem->quantity]);
                } else {
                    $userCart->items()->create([
                        'product_id' => $guestItem->product_id,
                        'quantity' => $guestItem->quantity,
                        'unit_price' => $guestItem->unit_price,
                    ]);
                }
            }

            $guestCart->delete();

            return $userCart->fresh('items.product');
        });
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
