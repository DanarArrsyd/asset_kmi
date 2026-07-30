<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryController extends MasterDataController
{
    protected string $modelClass = Category::class;

    protected string $routeBase = 'categories';

    protected string $pageTitle = 'Kategori';

    protected string $activeMenu = 'category';

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('categories', 'code')->ignore($ignoreId)],
        ];
    }
}
