@extends('layouts.main')

@php($activeMenu = 'sto')

@section('title', 'Stock Opname')

@section('breadcrumb')
    <span>Home</span> / <span class="is-current">Stock Opname</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Stock Opname</h1>
            <p>Riwayat verifikasi kondisi asset.</p>
        </div>
    </div>

    <form method="GET" class="table-toolbar">
        <label class="table-toolbar__search">
            <i class="bi bi-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nomor / nama asset...">
        </label>
        <button type="submit" class="btn btn--secondary">Filter</button>
        <a href="{{ route('stock-opname.index') }}" class="btn btn--secondary">Reset</a>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Asset</th>
                    <th>Diperiksa Oleh</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockOpnames as $sto)
                    <tr>
                        <td class="cell-muted">{{ $sto->checked_at->format('d M Y H:i') }}</td>
                        <td><a href="{{ route('asset.public', $sto->asset) }}">{{ $sto->asset->asset_number }}</a> — {{ $sto->asset->name }}</td>
                        <td class="cell-muted">{{ $sto->user->name }}</td>
                        <td>{{ $sto->condition->label() }}</td>
                        <td><span class="pill {{ $sto->status->badgeClass() }}">{{ $sto->status->label() }}</span></td>
                        <td class="cell-muted">{{ $sto->notes ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="table-empty">Belum ada riwayat stock opname. Buka detail asset untuk mulai STO.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-bar">
        <span>Menampilkan {{ $stockOpnames->firstItem() ?? 0 }}–{{ $stockOpnames->lastItem() ?? 0 }} dari {{ $stockOpnames->total() }} riwayat</span>
        {{ $stockOpnames->onEachSide(1)->links() }}
    </div>
@endsection
