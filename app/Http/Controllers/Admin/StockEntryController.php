<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStockEntryRequest;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StockEntryController extends Controller
{
    public function __construct(
        private readonly StockService $stock,
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Stock/Index', [
            'entries' => StockEntry::query()
                ->with(['supplier', 'items', 'receivedByUser'])
                ->latest('received_at')
                ->latest('id')
                ->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Stock/Create', [
            'suppliers' => Supplier::query()->where('active', true)->orderBy('name')->get(),
            'products' => Product::query()
                ->orderBy('name')
                ->get(['id', 'name', 'sku', 'color', 'material', 'stock']),
        ]);
    }

    public function store(StoreStockEntryRequest $request): RedirectResponse
    {
        $entry = $this->stock->registerEntry($request->validated(), $request->user()->id);

        return redirect()->route('admin.stock-entries.index')
            ->with('success', "Ingreso #{$entry->id} registrado y stock actualizado.");
    }
}
