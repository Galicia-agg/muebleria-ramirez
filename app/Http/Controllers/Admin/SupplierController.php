<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Suppliers/Index', [
            'suppliers' => Supplier::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::query()->create($request->validated());

        return back()->with('success', 'Proveedor creado correctamente.');
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return back()->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->stockEntries()->exists()) {
            $supplier->update(['active' => false]);

            return back()->with('success', 'Proveedor desactivado (tiene ingresos registrados).');
        }

        $supplier->delete();

        return back()->with('success', 'Proveedor eliminado.');
    }
}
