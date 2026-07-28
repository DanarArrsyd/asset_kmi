@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', 'Asset')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Asset']]" />
@endsection

@section('content')
    <x-page-header title="Asset" lede="Daftar seluruh aset perusahaan.">
        @can('create', \App\Models\Asset::class)
            <a href="{{ route('assets.create') }}" class="btn btn--primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Asset
            </a>
        @endcan
    </x-page-header>

    <x-table-toolbar :action="route('assets.index')"
                     placeholder="Cari nomor / nama asset..."
                     :export-url="route('assets.export', request()->query())">
        <label class="sr-only" for="filterStatus">Status</label>
        <select id="filterStatus" name="status" class="form-control">
            <option value="">Semua Status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>

        <label class="sr-only" for="filterDepartment">Departemen</label>
        <select id="filterDepartment" name="department_id" class="form-control">
            <option value="">Semua Departemen</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected((int) request('department_id') === $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
    </x-table-toolbar>

    <div class="panel">
        <div class="panel__body panel__body--flush">
            <div class="table-scroll">
                <table class="table">
                    <caption class="sr-only">Daftar asset</caption>
                    <thead>
                        <tr>
                            <x-th-sort field="asset_number" label="No. Asset" />
                            <x-th-sort field="name" label="Nama" />
                            <th>Kategori</th>
                            <th>Departemen</th>
                            <th>Lokasi</th>
                            <x-th-sort field="status" label="Status" />
                            <th><span class="sr-only">Aksi</span></th>
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
                                <td class="cell-actions">
                                    <div class="row-actions">
                                        <a href="{{ route('asset.public', $asset) }}" class="btn btn--secondary btn--icon"
                                           title="Detail {{ $asset->asset_number }}" aria-label="Detail {{ $asset->asset_number }}">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </a>
                                        @can('update', $asset)
                                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn--secondary btn--icon"
                                               title="Edit {{ $asset->asset_number }}" aria-label="Edit {{ $asset->asset_number }}">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="table-empty">
                                    Belum ada asset.
                                    @can('create', \App\Models\Asset::class)
                                        <a href="{{ route('assets.create') }}" class="auth-link">Tambah asset pertama</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel__foot">
            <x-pagination-bar :paginator="$assets" noun="asset" />
        </div>
    </div>
@endsection
