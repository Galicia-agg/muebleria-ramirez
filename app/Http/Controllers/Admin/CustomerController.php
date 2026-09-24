<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Customers/Index', [
            'customers' => User::query()
                ->role('cliente')
                ->withCount('orders')
                ->orderByDesc('created_at')
                ->paginate(20, ['id', 'name', 'email', 'created_at']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Customers/Form', [
            'customer' => null,
        ]);
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $customer = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            // Customers set their own password; admins never handle customer credentials.
            'password' => Hash::make(Str::random(40)),
        ]);

        $customer->assignRole('cliente');

        return redirect()->route('admin.customers.index')
            ->with('success', 'Cliente creado correctamente. Puede establecer su contraseña desde "¿Olvidaste tu contraseña?" en el inicio de sesión.');
    }

    public function edit(User $customer): Response
    {
        return Inertia::render('Admin/Customers/Form', [
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, User $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(User $customer): RedirectResponse
    {
        $customer->delete();

        return back()->with('success', 'Cliente eliminado.');
    }

    public function orders(User $customer): Response
    {
        return Inertia::render('Admin/Customers/Orders', [
            'customer' => $customer->only('id', 'name', 'email'),
            'orders' => $this->orders->paginateForUser($customer->id),
        ]);
    }
}
