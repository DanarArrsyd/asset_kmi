@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', 'Asset')

@section('breadcrumb')
    <span>Home</span> / <span class="is-current">Asset</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Asset</h1>
            <p>Daftar seluruh aset perusahaan.</p>
        </div>
        @can('create', \App\Models\Asset::class)
            <a href="{{ route('assets.create') }}" class="btn btn--primary">
                <i class="bi bi-plus-lg"></i> Tambah Asset
            </a>
        @endcan
    </div>

    <form method="GET" class="table-toolbar">
        <label class="table-toolbar__search">
            <i class="bi bi-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor / nama asset...">
        </label>

        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>

        <select name="department_id" class="form-control" onchange="this.form.submit()">
            <option value="">Semua Departemen</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected((int) request('department_id') === $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn--secondary">Filter</button>
        <a href="{{ route('assets.index') }}" class="btn btn--secondary">Reset</a>
        <a href="{{ route('assets.export', request()->query()) }}" class="btn btn--secondary">
            <i class="bi bi-download"></i> Export
        </a>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th><x-sort-link field="asset_number" label="No. Asset" /></th>
                    <th><x-sort-link field="name" label="Nama" /></th>
                    <th>Kategori</th>
                    <th>Departemen</th>
                    <th>Lokasi</th>
                    <th><x-sort-link field="status" label="Status" /></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $asset)
                    <tr>
                        <td><a href="{{ route('asset.public', $asset) }}">{{ $asset->asset_number }}</a></td>
                        <td>{{ $asset->name }}</td>
                        <td class="cell-muted">{{ $asset->category->name }}</td>
                        <td class="cell-muted">{{ $asset->department->name }}</td>
                        <td class="cell-muted">{{ $asset->location->name }}</td>
                        <td><span class="pill {{ $asset->status->badgeClass() }}">{{ $asset->status->label() }}</span></td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('asset.public', $asset) }}" class="btn btn--secondary" title="Detail"><i class="bi bi-eye"></i></a>
                                @can('update', $asset)
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn--secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="table-empty">Belum ada asset. @can('create', \App\Models\Asset::class)<a href="{{ route('assets.create') }}" class="auth-link">Tambah asset pertama</a>@endcan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-bar">
        <span>Menampilkan {{ $assets->firstItem() ?? 0 }}–{{ $assets->lastItem() ?? 0 }} dari {{ $assets->total() }} asset</span>
        {{ $assets->onEachSide(1)->links() }}
    </div>
@endsection
