<x-guest-layout :title="$asset->asset_number">
    {{-- Deliberately thin. Everything here is what someone standing next to the
         asset can already read off the label; the fields that would help
         somebody remove it are not on this page. --}}
    <div class="public-asset">
        <div class="public-asset__number">{{ $asset->asset_number }}</div>
        <h1 class="public-asset__name">{{ $asset->name }}</h1>

        <div class="public-asset__facts">
            <div class="detail-field">
                <div class="detail-field__label">Kategori</div>
                <div class="detail-field__value">{{ $asset->category->name }}</div>
            </div>

            <div class="detail-field">
                <div class="detail-field__label">Status</div>
                <div class="detail-field__value">
                    <span class="pill {{ $asset->status->badgeClass() }}">{{ $asset->status->label() }}</span>
                </div>
            </div>

            <div class="detail-field">
                <div class="detail-field__label">Kondisi</div>
                <div class="detail-field__value">{{ $asset->condition->label() }}</div>
            </div>

            <div class="detail-field">
                <div class="detail-field__label">Stock Opname Terakhir</div>
                <div class="detail-field__value">
                    {{ $lastCheckedAt ? $lastCheckedAt->format('d M Y') : 'Belum pernah di-STO' }}
                </div>
            </div>
        </div>

        <a href="{{ route('login') }}" class="btn btn--primary public-asset__cta">
            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
            Login untuk detail & mulai STO
        </a>

        <p class="public-asset__note">
            Detail lengkap dan pencatatan stock opname hanya untuk staf {{ config('app.name') }}.
        </p>
    </div>
</x-guest-layout>
