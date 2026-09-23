<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
        private readonly CategoryRepositoryInterface $categories,
    ) {
    }

    public function index(): Response
    {
        return Inertia::render('Shop/Home', [
            'featuredProducts' => $this->products->featured(),
            'categories' => $this->categories->activeForMenu(),
        ]);
    }
}
