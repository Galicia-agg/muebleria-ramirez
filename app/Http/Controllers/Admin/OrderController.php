<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Orders/Index', [
            'orders' => $this->orders->paginateForAdmin($request->only('status')),
            'filters' => $request->only('status'),
        ]);
    }

    public function show(Order $order): Response
    {
        return Inertia::render('Admin/Orders/Show', [
            'order' => $this->orders->findWithDetails($order->id),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if (in_array($order->status, ['entregado', 'cancelado'], true)) {
            return back()->withErrors(['status' => 'Este pedido ya está en un estado final y no se puede modificar.']);
        }

        $request->validate([
            'status' => ['required', 'in:pendiente,confirmado,en_preparacion,enviado,entregado,cancelado'],
        ]);

        $this->orderService->updateStatus($order, $request->input('status'));

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
