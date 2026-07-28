<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class MasterDataController extends Controller
{
    /** @var class-string<Model> */
    protected string $modelClass;

    protected string $routeBase;

    protected string $pageTitle;

    protected string $activeMenu;

    protected bool $hasCode = true;

    abstract protected function rules(?int $ignoreId = null): array;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', $this->modelClass);

        $items = $this->filteredQuery($request)->paginate(15)->withQueryString();

        return view('master-data.index', [
            'items' => $items,
            'routeBase' => $this->routeBase,
            'pageTitle' => $this->pageTitle,
            'activeMenu' => $this->activeMenu,
            'hasCode' => $this->hasCode,
            'modelClass' => $this->modelClass,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', $this->modelClass);

        $items = $this->filteredQuery($request)->get();
        $hasCode = $this->hasCode;

        $filename = $this->routeBase.'-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($items, $hasCode) {
            $handle = fopen('php://output', 'w');

            $header = $hasCode ? ['Nama', 'Kode', 'Jumlah Asset'] : ['Nama', 'Jumlah Asset'];
            fputcsv($handle, $header);

            foreach ($items as $item) {
                $row = $hasCode
                    ? [$item->name, $item->code ?? '', $item->assets_count]
                    : [$item->name, $item->assets_count];

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    protected function filteredQuery(Request $request): Builder
    {
        $query = $this->modelClass::query()->withCount('assets');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%");

                if ($this->hasCode) {
                    $q->orWhere('code', 'like', "%{$search}%");
                }
            });
        }

        $allowedSorts = array_values(array_filter(['name', $this->hasCode ? 'code' : null, 'assets_count']));

        $sort = $request->string('sort')->value();
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'name';

        $direction = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sort, $direction);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', $this->modelClass);

        $data = $request->validate($this->rules());

        $this->modelClass::create($data);

        return back()->with('status', 'created');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = $this->modelClass::findOrFail($id);

        $this->authorize('update', $item);

        $data = $request->validate($this->rules($id));

        $item->update($data);

        return back()->with('status', 'updated');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = $this->modelClass::findOrFail($id);

        $this->authorize('delete', $item);

        try {
            $item->delete();
        } catch (QueryException) {
            return back()->with('error', 'Tidak bisa dihapus — masih dipakai oleh asset lain.');
        }

        return back()->with('status', 'deleted');
    }
}
