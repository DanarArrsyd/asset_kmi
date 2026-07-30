@extends('layouts.main')

@php($activeMenu = 'sto')

@section('title', 'Stock Opname')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Stock Opname']]" />
@endsection

@section('content')
    <x-page-header title="Stock Opname" lede="Riwayat verifikasi kondisi asset." />

    <x-table-toolbar :action="route('stock-opname.index')"
                     placeholder="Cari nomor / nama asset..."
                     :export-url="route('stock-opname.export', request()->query())">
        <label class="sr-only" for="filterCondition">Kondisi</label>
        <select id="filterCondition" name="condition" class="form-control">
            <option value="">Semua Kondisi</option>
            @foreach ($conditions as $condition)
                <option value="{{ $condition->value }}" @selected(request('condition') === $condition->value)>{{ $condition->label() }}</option>
            @endforeach
        </select>
    </x-table-toolbar>

    <div class="panel">
        <div class="panel__body panel__body--flush">
            <div class="table-scroll">
                <table class="table">
                    <caption class="sr-only">Riwayat stock opname</caption>
                    <thead>
                        <tr>
                            <x-th-sort field="checked_at" label="Tanggal" />
                            <th>Asset</th>
                            <th>Diperiksa Oleh</th>
                            <x-th-sort field="condition" label="Kondisi" />
                            <x-th-sort field="status" label="Status" />
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockOpnames as $sto)
                            <tr>
                                <td class="cell-muted">{{ $sto->checked_at->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('asset.public', $sto->asset) }}">{{ $sto->asset->asset_number }}</a>
                                    — {{ $sto->asset->name }}
                                </td>
                                <td class="cell-muted">{{ $sto->user->name }}</td>
                                <td>{{ $sto->condition->label() }}</td>
                                <td><span class="pill {{ $sto->status->badgeClass() }}">{{ $sto->status->label() }}</span></td>
                                <td class="cell-muted">{{ $sto->notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty">
                                    Belum ada riwayat stock opname. Buka detail asset untuk mulai STO.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel__foot">
            <x-pagination-bar :paginator="$stockOpnames" noun="riwayat" />
        </div>
    </div>
@endsection
