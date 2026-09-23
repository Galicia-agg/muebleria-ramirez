<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['category', 'search', 'min_price', 'max_price', 'sort']);

        return Inertia::render('Shop/Catalog', [
            'products' => $this->products->paginateForStorefront($filters),
            'categories' => $this->categories->activeForMenu(),
            'filters' => $filters,
        ]);
    }

    public function show(string $slug): Response
    {
        $product = $this->products->findBySlug($slug);

        abort_if(! $product || ! $product->active, 404);

        return Inertia::render('Shop/ProductDetail', [
            'product' => $product,
        ]);
    }
}
