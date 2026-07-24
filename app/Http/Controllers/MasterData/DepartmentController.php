<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Department;
use Illuminate\Validation\Rule;

class DepartmentController extends MasterDataController
{
    protected string $modelClass = Department::class;

    protected string $routeBase = 'departments';

    protected string $pageTitle = 'Department';

    protected string $activeMenu = 'department';

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('departments', 'code')->ignore($ignoreId)],
        ];
    }
}
