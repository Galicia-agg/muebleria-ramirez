<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly CategoryRepositoryInterface $categories,
        private readonly CatalogService $catalogService,
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'stock']);

        return Inertia::render('Admin/Products/Index', [
            'products' => $this->products->paginateForAdmin($filters),
            'filters' => $filters,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'categories' => $this->categories->all(),
            'product' => null,
            'maxImages' => Product::MAX_IMAGES,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('images');
        $images = $request->file('images', []);

        try {
            $this->catalogService->createProduct($data, $images);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['images' => $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'categories' => $this->categories->all(),
            'product' => $product->load('images'),
            'maxImages' => Product::MAX_IMAGES,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except('images');
        $images = $request->file('images', []);

        try {
            $this->catalogService->updateProduct($product, $data, $images);
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['images' => $e->getMessage()])->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->update($product, ['active' => false]);

        return back()->with('success', 'Producto desactivado.');
    }

    public function destroyImage(ProductImage $productImage): RedirectResponse
    {
        $this->catalogService->removeImage($productImage);

        return back()->with('success', 'Imagen eliminada.');
    }

    public function reorderImages(Request $request, Product $product): RedirectResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        $this->catalogService->reorderImages($product, $request->input('order'));

        return back()->with('success', 'Orden de imágenes actualizado.');
    }
}
