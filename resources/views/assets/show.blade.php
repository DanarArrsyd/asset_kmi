@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', $asset->asset_number)

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Asset', 'href' => route('assets.index')],
        ['label' => $asset->asset_number],
    ]" />
@endsection

@section('content')
    <x-page-header :title="$asset->name" :lede="$asset->asset_number">
        @can('recordStockOpname', $asset)
            <a href="{{ route('stock-opname.create', $asset) }}" class="btn btn--primary">
                <i class="bi bi-qr-code-scan" aria-hidden="true"></i> Mulai STO
            </a>
        @endcan
        @can('update', $asset)
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn--secondary">
                <i class="bi bi-pencil" aria-hidden="true"></i> Edit
            </a>
        @endcan
        @can('delete', $asset)
            <button type="button" class="btn btn--danger" id="deleteAssetTrigger">
                <i class="bi bi-trash" aria-hidden="true"></i> Hapus
            </button>
        @endcan
    </x-page-header>

    @if (session('status') === 'asset-created')
        <div class="form-status form-status--success">Asset berhasil dibuat, QR code sudah digenerate.</div>
    @elseif (session('status') === 'asset-updated')
        <div class="form-status form-status--success">Perubahan tersimpan.</div>
    @elseif (session('status') === 'sto-saved')
        <div class="form-status form-status--success">Stock opname tersimpan, kondisi asset diperbarui.</div>
    @endif

    <div class="stack">
        <div class="detail-grid">
            <div class="detail-aside">
                <div class="panel">
                    <div class="panel__body">
                        <div class="detail-photo">
                            @if ($asset->photo_path)
                                <img src="{{ Storage::url($asset->photo_path) }}" alt="Foto {{ $asset->name }}">
                            @else
                                <i class="bi bi-image" aria-hidden="true"></i>
                                <span class="sr-only">Belum ada foto</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <div class="panel__head-text"><h2>QR Code</h2></div>
                    </div>
                    <div class="panel__body">
                        <div class="qr-box">
                            @if ($asset->qr_path)
                                <img src="{{ Storage::url($asset->qr_path) }}" alt="QR code {{ $asset->asset_number }}">
                            @endif
                            <div class="row-actions">
                                <a href="{{ route('assets.qr-print', $asset) }}" target="_blank" rel="noopener"
                                   class="btn btn--secondary btn--sm">
                                    <i class="bi bi-printer" aria-hidden="true"></i> Cetak
                                </a>
                                @if ($asset->qr_path)
                                    <a href="{{ Storage::url($asset->qr_path) }}" download="{{ $asset->asset_number }}.png"
                                       class="btn btn--secondary btn--sm">
                                        <i class="bi bi-download" aria-hidden="true"></i> Unduh
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Detail Asset</h2></div>
                    <span class="pill {{ $asset->status->badgeClass() }}">{{ $asset->status->label() }}</span>
                </div>
                <div class="panel__body">
                    <div class="detail-fields">
                        <div class="detail-field">
                            <div class="detail-field__label">Kategori</div>
                            <div class="detail-field__value">{{ $asset->category->name }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Brand</div>
                            <div class="detail-field__value">{{ $asset->brand->name ?? '—' }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Model</div>
                            <div class="detail-field__value">{{ $asset->model ?? '—' }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Departemen</div>
                            <div class="detail-field__value">{{ $asset->department->name }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Lokasi</div>
                            <div class="detail-field__value">{{ $asset->location->name }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">PIC</div>
                            <div class="detail-field__value">{{ $asset->pic ?? '—' }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Kondisi</div>
                            <div class="detail-field__value">{{ $asset->condition->label() }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Tanggal Pembelian</div>
                            <div class="detail-field__value">{{ $asset->purchase_date?->format('d M Y') ?? '—' }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Dibuat</div>
                            <div class="detail-field__value">{{ $asset->created_at->format('d M Y H:i') }}</div>
                        </div>

                        @if ($asset->specification)
                            <div class="detail-field detail-field--full">
                                <div class="detail-field__label">Spesifikasi</div>
                                <div class="detail-field__value">{{ $asset->specification }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head">
                <div class="panel__head-text"><h2>Riwayat Stock Opname</h2></div>
                <span class="pill pill--neutral">{{ $asset->stockOpnames->count() }} entri</span>
            </div>
            <div class="panel__body">
                @forelse ($asset->stockOpnames as $sto)
                    <div class="activity-item">
                        <div class="activity-icon is-success"><i class="bi bi-qr-code-scan" aria-hidden="true"></i></div>
                        <div class="activity-body">
                            <p>
                                <strong>{{ $sto->auditorName() }}</strong> memeriksa — condition
                                <strong>{{ $sto->condition->label() }}</strong>, status
                                <strong>{{ $sto->status->label() }}</strong>@if ($sto->notes) — {{ $sto->notes }}@endif
                            </p>
                            <time datetime="{{ $sto->checked_at->toIso8601String() }}">{{ $sto->checked_at->format('d M Y H:i') }}</time>
                        </div>
                    </div>
                @empty
                    <p class="dist-empty">Belum pernah di-STO.</p>
                @endforelse
            </div>
        </div>
    </div>

    @can('delete', $asset)
        <dialog class="confirm-dialog" id="deleteAssetDialog">
            <form method="post" action="{{ route('assets.destroy', $asset) }}">
                @csrf
                @method('delete')
                <h2>Hapus asset ini?</h2>
                <p>{{ $asset->asset_number }} — {{ $asset->name }} akan dihapus permanen beserta foto &amp; QR-nya.</p>
                <div class="form-row form-row--end">
                    <button type="button" class="btn btn--secondary" id="deleteAssetCancel">Batal</button>
                    <button type="submit" class="btn btn--danger">Hapus</button>
                </div>
            </form>
        </dialog>

        @push('scripts')
        <script>
            (function () {
                const dialog = document.getElementById('deleteAssetDialog');
                document.getElementById('deleteAssetTrigger')?.addEventListener('click', () => dialog.showModal());
                document.getElementById('deleteAssetCancel')?.addEventListener('click', () => dialog.close());
            })();
        </script>
        @endpush
    @endcan
@endsection
