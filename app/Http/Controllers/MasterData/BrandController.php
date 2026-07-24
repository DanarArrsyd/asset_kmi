<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Brand;

class BrandController extends MasterDataController
{
    protected string $modelClass = Brand::class;

    protected string $routeBase = 'brands';

    protected string $pageTitle = 'Brand';

    protected string $activeMenu = 'brand';

    protected bool $hasCode = false;

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
