<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isScoped = in_array($user->role, [UserRole::Department, UserRole::User], true);

        $assetQuery = Asset::query();
        if ($isScoped) {
            $assetQuery->where('department_id', $user->department_id);
        }

        $totalAssets = (clone $assetQuery)->count();
        $activeAssets = (clone $assetQuery)->where('status', 'active')->count();
        $maintenanceAssets = (clone $assetQuery)->where('status', 'maintenance')->count();
        $missingAssets = (clone $assetQuery)->where('status', 'missing')->count();

        $checkedAssets = (clone $assetQuery)->whereHas('stockOpnames')->count();

        $departments = $isScoped
            ? Department::where('id', $user->department_id)->get()
            : Department::orderBy('name')->get();

        $assetsByDepartment = $departments->map(function ($department) use ($assetQuery, $totalAssets) {
            $count = (clone $assetQuery)->where('department_id', $department->id)->count();

            return [
                'label' => $department->name,
                'count' => $count,
                'percent' => $totalAssets > 0 ? round($count / $totalAssets * 100) : 0,
            ];
        })->filter(fn ($row) => $row['count'] > 0)->values();

        $assetsByCategory = Category::orderBy('name')->get()->map(function ($category) use ($assetQuery, $totalAssets) {
            $count = (clone $assetQuery)->where('category_id', $category->id)->count();

            return [
                'label' => $category->name,
                'count' => $count,
                'percent' => $totalAssets > 0 ? round($count / $totalAssets * 100) : 0,
            ];
        })->filter(fn ($row) => $row['count'] > 0)->values();

        $recentActivitiesQuery = StockOpname::query()->with(['asset', 'user'])->latest('checked_at');
        if ($isScoped) {
            $recentActivitiesQuery->whereHas('asset', fn ($q) => $q->where('department_id', $user->department_id));
        }

        $recentActivities = $recentActivitiesQuery->limit(5)->get()->map(fn (StockOpname $sto) => [
            'icon' => 'bi-qr-code-scan',
            'iconState' => match ($sto->status->value) {
                'active' => 'is-success',
                'maintenance' => 'is-warning',
                default => '',
            },
            'assetNumber' => $sto->asset->asset_number,
            'message' => "diverifikasi STO oleh {$sto->auditorName()} — {$sto->condition->label()}",
            'time' => $sto->checked_at->diffForHumans(),
        ]);

        return view('dashboard.index', [
            'totalAssets' => $totalAssets,
            'activeAssets' => $activeAssets,
            'activePercent' => $totalAssets > 0 ? round($activeAssets / $totalAssets * 100) : 0,
            'maintenanceAssets' => $maintenanceAssets,
            'maintenancePercent' => $totalAssets > 0 ? round($maintenanceAssets / $totalAssets * 100) : 0,
            'missingAssets' => $missingAssets,
            'stoProgress' => $totalAssets > 0 ? round($checkedAssets / $totalAssets * 100) : 0,
            'assetsByDepartment' => $assetsByDepartment,
            'assetsByCategory' => $assetsByCategory,
            'recentActivities' => $recentActivities,
        ]);
    }
}
