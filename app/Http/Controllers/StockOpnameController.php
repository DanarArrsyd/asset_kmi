<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\UserRole;
use App\Http\Requests\StoreStockOpnameRequest;
use App\Models\Asset;
use App\Models\StockOpname;
use App\Services\StockOpnameService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockOpnameController extends Controller
{
    public function __construct(protected StockOpnameService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', StockOpname::class);

        $stockOpnames = $this->filteredQuery($request)->paginate(15)->withQueryString();

        return view('stock-opname.index', [
            'stockOpnames' => $stockOpnames,
            'conditions' => AssetCondition::cases(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', StockOpname::class);

        $stockOpnames = $this->filteredQuery($request)->get();

        $filename = 'stock-opname-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($stockOpnames) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'No. Asset', 'Nama Asset', 'Diperiksa Oleh', 'Kondisi', 'Status', 'Catatan']);

            foreach ($stockOpnames as $sto) {
                fputcsv($handle, [
                    $sto->checked_at->format('Y-m-d H:i'),
                    $sto->asset->asset_number,
                    $sto->asset->name,
                    $sto->auditorName(),
                    $sto->condition->label(),
                    $sto->status->label(),
                    $sto->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function filteredQuery(Request $request): Builder
    {
        $user = $request->user();

        $query = StockOpname::query()->with(['asset', 'user']);

        if (in_array($user->role, [UserRole::Department, UserRole::User], true)) {
            $query->whereHas('asset', fn ($q) => $q->where('department_id', $user->department_id));
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->whereHas('asset', fn ($q) => $q->where('asset_number', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%"));
        }

        if ($condition = $request->string('condition')->value()) {
            $query->where('condition', $condition);
        }

        $sort = $request->string('sort')->value();
        $sort = in_array($sort, ['checked_at', 'condition', 'status'], true) ? $sort : 'checked_at';

        $direction = $request->string('direction')->value() === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sort, $direction);
    }

    public function create(Asset $asset): View
    {
        $this->authorize('recordStockOpname', $asset);

        $asset->load(['category', 'brand', 'department', 'location']);

        return view('stock-opname.create', ['asset' => $asset]);
    }

    public function store(StoreStockOpnameRequest $request, Asset $asset): RedirectResponse
    {
        $this->service->record($asset, $request->user(), $request->validated(), $request->file('photo'));

        return redirect()->route('asset.public', $asset)->with('status', 'sto-saved');
    }
}
