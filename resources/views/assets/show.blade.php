@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', $asset->asset_number)

@section('breadcrumb')
    <span>Home</span> / <a href="{{ route('assets.index') }}">Asset</a> / <span class="is-current">{{ $asset->asset_number }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $asset->name }}</h1>
            <p>{{ $asset->asset_number }}</p>
        </div>
        <div class="row-actions">
            @can('create', \App\Models\StockOpname::class)
                <a href="{{ route('stock-opname.create', $asset) }}" class="btn btn--primary"><i class="bi bi-qr-code-scan"></i> Start STO</a>
            @endcan
            @can('update', $asset)
                <a href="{{ route('assets.edit', $asset) }}" class="btn btn--secondary"><i class="bi bi-pencil"></i> Edit Asset</a>
            @endcan
            @can('delete', $asset)
                <button type="button" class="btn btn--danger" id="deleteAssetTrigger"><i class="bi bi-trash"></i> Delete</button>
            @endcan
        </div>
    </div>

    @if (session('status') === 'asset-created')
        <div class="form-status form-status--success">Asset berhasil dibuat, QR code sudah digenerate.</div>
    @elseif (session('status') === 'asset-updated')
        <div class="form-status form-status--success">Perubahan tersimpan.</div>
    @elseif (session('status') === 'sto-saved')
        <div class="form-status form-status--success">Stock opname tersimpan, kondisi asset diperbarui.</div>
    @endif

    <div class="detail-grid">
        <div>
            <div class="detail-photo">
                @if ($asset->photo_path)
                    <img src="{{ Storage::url($asset->photo_path) }}" alt="{{ $asset->name }}">
                @else
                    <i class="bi bi-image"></i>
                @endif
            </div>

            <div class="qr-box" style="margin-top: var(--space-md);">
                @if ($asset->qr_path)
                    <img src="{{ Storage::url($asset->qr_path) }}" alt="QR {{ $asset->asset_number }}">
                @endif
                <div class="row-actions" style="justify-content: center; margin-top: var(--space-sm);">
                    <a href="{{ route('assets.qr-print', $asset) }}" target="_blank" class="btn btn--secondary"><i class="bi bi-printer"></i> Print QR</a>
                    <a href="{{ Storage::url($asset->qr_path) }}" download="{{ $asset->asset_number }}.png" class="btn btn--secondary"><i class="bi bi-download"></i> Download QR</a>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="section-card__title" style="margin-bottom: var(--space-lg);">Asset Detail</h2>

            <div class="detail-fields">
                <div>
                    <div class="detail-field__label">Category</div>
                    <div class="detail-field__value">{{ $asset->category->name }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Brand</div>
                    <div class="detail-field__value">{{ $asset->brand->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Model</div>
                    <div class="detail-field__value">{{ $asset->model ?? '—' }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Department</div>
                    <div class="detail-field__value">{{ $asset->department->name }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Location</div>
                    <div class="detail-field__value">{{ $asset->location->name }}</div>
                </div>
                <div>
                    <div class="detail-field__label">PIC</div>
                    <div class="detail-field__value">{{ $asset->pic ?? '—' }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Status</div>
                    <div class="detail-field__value"><span class="pill {{ $asset->status->badgeClass() }}">{{ $asset->status->label() }}</span></div>
                </div>
                <div>
                    <div class="detail-field__label">Condition</div>
                    <div class="detail-field__value">{{ $asset->condition->label() }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Purchase Date</div>
                    <div class="detail-field__value">{{ $asset->purchase_date?->format('d M Y') ?? '—' }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Created</div>
                    <div class="detail-field__value">{{ $asset->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>

            @if ($asset->specification)
                <div style="margin-top: var(--space-lg);">
                    <div class="detail-field__label">Specification</div>
                    <div class="detail-field__value">{{ $asset->specification }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top: var(--space-md);">
        <h2 class="section-card__title" style="margin-bottom: var(--space-md);">History Stock Opname</h2>

        @forelse ($asset->stockOpnames as $sto)
            <div class="activity-item">
                <div class="activity-icon is-success"><i class="bi bi-qr-code-scan"></i></div>
                <div class="activity-body">
                    <p>
                        <strong>{{ $sto->user->name }}</strong> memeriksa — condition <strong>{{ $sto->condition->label() }}</strong>,
                        status <strong>{{ $sto->status->label() }}</strong>
                        @if ($sto->notes) — {{ $sto->notes }} @endif
                    </p>
                    <time>{{ $sto->checked_at->format('d M Y H:i') }}</time>
                </div>
            </div>
        @empty
            <p class="section-card__desc" style="margin: 0;">Belum pernah di-STO.</p>
        @endforelse
    </div>

    @can('delete', $asset)
        <dialog class="confirm-dialog" id="deleteAssetDialog">
            <form method="post" action="{{ route('assets.destroy', $asset) }}">
                @csrf
                @method('delete')
                <h2>Hapus asset ini?</h2>
                <p>{{ $asset->asset_number }} — {{ $asset->name }} akan dihapus permanen beserta foto & QR-nya.</p>
                <div class="form-row" style="justify-content: flex-end;">
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
