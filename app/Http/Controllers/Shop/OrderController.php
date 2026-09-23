<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Shop/Orders/Index', [
            'orders' => $this->orders->paginateForUser($request->user()->id),
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return Inertia::render('Shop/Orders/Show', [
            'order' => $this->orders->findWithDetails($order->id),
        ]);
    }
}
