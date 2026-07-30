<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Services\AssetService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetController extends Controller
{
    public function __construct(protected AssetService $assets) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Asset::class);

        $assets = $this->filteredQuery($request)->paginate(15)->withQueryString();

        return view('assets.index', [
            'assets' => $assets,
            'departments' => Department::orderBy('name')->get(),
            'statuses' => AssetStatus::cases(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Asset::class);

        $assets = $this->filteredQuery($request)->get();

        $filename = 'assets-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($assets) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No. Asset', 'Nama', 'Kategori', 'Departemen', 'Lokasi', 'Status', 'Kondisi', 'Tanggal Pembelian']);

            foreach ($assets as $asset) {
                fputcsv($handle, [
                    $asset->asset_number,
                    $asset->name,
                    $asset->category->name,
                    $asset->department->name,
                    $asset->location->name,
                    $asset->status->label(),
                    $asset->condition->label(),
                    optional($asset->purchase_date)->format('Y-m-d'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function filteredQuery(Request $request): Builder
    {
        $user = $request->user();

        $query = Asset::query()->with(['category', 'brand', 'department', 'location']);

        if (in_array($user->role, [UserRole::Department, UserRole::User], true)) {
            $query->where('department_id', $user->department_id);
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('asset_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($departmentId = $request->integer('department_id')) {
            $query->where('department_id', $departmentId);
        }

        $sort = $request->string('sort')->value() ?: 'created_at';
        $direction = $request->string('direction')->value() === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['asset_number', 'name', 'status', 'created_at'];
        $query->orderBy(in_array($sort, $allowedSorts, true) ? $sort : 'created_at', $direction);

        return $query;
    }

    public function qrPrint(Asset $asset): View
    {
        $this->authorize('view', $asset);

        return view('assets.qr-print', ['asset' => $asset]);
    }

    public function create(): View
    {
        $this->authorize('create', Asset::class);

        return view('assets.create', $this->formOptions());
    }

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $asset = $this->assets->create($request->validated(), $request->file('photo'));

        return redirect()->route('asset.public', $asset)->with('status', 'asset-created');
    }

    /**
     * The target of every printed QR code, so the URL must never change and the
     * page must answer to a stranger holding a phone as well as to staff.
     *
     * Signed-in users who may see the record get the full detail page. Everyone
     * else — not logged in, or scoped to another department — gets a summary
     * that identifies the asset and says when it was last counted, without the
     * location, PIC, price or specification a passer-by has no business reading
     * off a sticker.
     */
    public function show(Request $request, Asset $asset): View
    {
        $user = $request->user();

        if (! $user || $user->cannot('view', $asset)) {
            return $this->publicSummary($asset);
        }

        $asset->load(['category', 'brand', 'department', 'location', 'stockOpnames.user']);

        return view('assets.show', ['asset' => $asset]);
    }

    protected function publicSummary(Asset $asset): View
    {
        $asset->load('category');

        return view('assets.public-show', [
            'asset' => $asset,
            'lastCheckedAt' => $asset->stockOpnames()->value('checked_at'),
        ]);
    }

    public function edit(Asset $asset): View
    {
        $this->authorize('update', $asset);

        return view('assets.edit', ['asset' => $asset] + $this->formOptions());
    }

    public function update(UpdateAssetRequest $request, Asset $asset): RedirectResponse
    {
        $this->assets->update($asset, $request->validated(), $request->file('photo'));

        return redirect()->route('asset.public', $asset)->with('status', 'asset-updated');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $this->authorize('delete', $asset);

        $this->assets->delete($asset);

        return redirect()->route('assets.index')->with('status', 'asset-deleted');
    }

    protected function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'locations' => Location::orderBy('name')->get(),
            'statuses' => AssetStatus::cases(),
            'conditions' => AssetCondition::cases(),
        ];
    }
}
