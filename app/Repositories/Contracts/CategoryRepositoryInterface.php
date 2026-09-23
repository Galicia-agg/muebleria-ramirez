<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function topLevel(): Collection;

    public function activeForMenu(): Collection;

    public function findBySlug(string $slug): ?Category;
}
