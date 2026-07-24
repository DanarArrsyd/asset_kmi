<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Location;
use Illuminate\Validation\Rule;

class LocationController extends MasterDataController
{
    protected string $modelClass = Location::class;

    protected string $routeBase = 'locations';

    protected string $pageTitle = 'Location';

    protected string $activeMenu = 'location';

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('locations', 'code')->ignore($ignoreId)],
        ];
    }
}
