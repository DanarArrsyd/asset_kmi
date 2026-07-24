<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreStockOpnameRequest;
use App\Models\Asset;
use App\Models\StockOpname;
use App\Services\StockOpnameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function __construct(protected StockOpnameService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', StockOpname::class);

        $user = $request->user();

        $query = StockOpname::query()->with(['asset', 'user']);

        if (in_array($user->role, [UserRole::Department, UserRole::User], true)) {
            $query->whereHas('asset', fn ($q) => $q->where('department_id', $user->department_id));
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->whereHas('asset', fn ($q) => $q->where('asset_number', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%"));
        }

        $stockOpnames = $query->latest('checked_at')->paginate(15)->withQueryString();

        return view('stock-opname.index', ['stockOpnames' => $stockOpnames]);
    }

    public function create(Asset $asset): View
    {
        $this->authorize('create', StockOpname::class);

        $asset->load(['category', 'brand', 'department', 'location']);

        return view('stock-opname.create', ['asset' => $asset]);
    }

    public function store(StoreStockOpnameRequest $request, Asset $asset): RedirectResponse
    {
        $this->service->record($asset, $request->user(), $request->validated(), $request->file('photo'));

        return redirect()->route('asset.public', $asset)->with('status', 'sto-saved');
    }
}
