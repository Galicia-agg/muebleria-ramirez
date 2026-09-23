<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function findOrFail(int $id): Category
    {
        return parent::findOrFail($id);
    }

    public function topLevel(): Collection
    {
        return $this->model->newQuery()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    public function activeForMenu(): Collection
    {
        return $this->model->newQuery()
            ->where('active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($query) => $query->where('active', true)->orderBy('position')])
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }
}
