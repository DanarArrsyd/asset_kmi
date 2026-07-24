<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

abstract class MasterDataController extends Controller
{
    /** @var class-string<\Illuminate\Database\Eloquent\Model> */
    protected string $modelClass;

    protected string $routeBase;

    protected string $pageTitle;

    protected string $activeMenu;

    protected bool $hasCode = true;

    abstract protected function rules(?int $ignoreId = null): array;

    public function index(): View
    {
        $this->authorize('viewAny', $this->modelClass);

        $items = $this->modelClass::orderBy('name')->withCount('assets')->get();

        return view('master-data.index', [
            'items' => $items,
            'routeBase' => $this->routeBase,
            'pageTitle' => $this->pageTitle,
            'activeMenu' => $this->activeMenu,
            'hasCode' => $this->hasCode,
            'modelClass' => $this->modelClass,
        ]);
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
            return back()->with('error', "Tidak bisa dihapus — masih dipakai oleh asset lain.");
        }

        return back()->with('status', 'deleted');
    }
}
