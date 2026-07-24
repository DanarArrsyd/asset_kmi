@extends('layouts.main')

@php($activeMenu = 'dashboard')

@section('title', 'Dashboard')

@section('breadcrumb')
    <span>Home</span> / <span class="is-current">Dashboard</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Ringkasan aset & progres stock opname.</p>
        </div>
        @can('create', \App\Models\Asset::class)
            <a href="{{ route('assets.create') }}" class="btn btn--primary"><i class="bi bi-plus-lg"></i> Tambah Asset</a>
        @endcan
    </div>

    <section class="kpi-grid">
        <div class="card">
            <div class="kpi-card__label"><i class="bi bi-boxes"></i> Total Assets</div>
            <div class="kpi-card__value">{{ number_format($totalAssets ?? 0) }}</div>
            <div class="kpi-card__delta">Seluruh departemen</div>
        </div>
        <div class="card kpi-card--accent">
            <div class="kpi-card__label"><i class="bi bi-check-circle"></i> Active</div>
            <div class="kpi-card__value">{{ number_format($activeAssets ?? 0) }}</div>
            <div class="kpi-card__delta is-up">
                <i class="bi bi-arrow-up-short"></i> {{ $activePercent ?? 0 }}% dari total
            </div>
        </div>
        <div class="card kpi-card--warning">
            <div class="kpi-card__label"><i class="bi bi-tools"></i> Maintenance</div>
            <div class="kpi-card__value">{{ number_format($maintenanceAssets ?? 0) }}</div>
            <div class="kpi-card__delta">{{ $maintenancePercent ?? 0 }}% dari total</div>
        </div>
        <div class="card kpi-card--danger">
            <div class="kpi-card__label"><i class="bi bi-exclamation-triangle"></i> Missing</div>
            <div class="kpi-card__value">{{ number_format($missingAssets ?? 0) }}</div>
            <div class="kpi-card__delta is-down">
                <i class="bi bi-arrow-down-short"></i> perlu tindak lanjut
            </div>
        </div>
        <div class="card">
            <div class="kpi-card__label"><i class="bi bi-clipboard-check"></i> STO Progress</div>
            <div class="kpi-card__value">{{ $stoProgress ?? 0 }}%</div>
            <div class="dist-bar" style="margin-top:.5rem">
                <div class="dist-bar__fill" style="width: {{ $stoProgress ?? 0 }}%"></div>
            </div>
        </div>
    </section>

    <section class="grid-2col">
        <div>
            <div class="card" style="margin-bottom: var(--space-md)">
                <div class="card__head">
                    <h2>Assets by Department</h2>
                    <a href="{{ route('assets.index') }}">Lihat semua</a>
                </div>
                @forelse ($assetsByDepartment ?? [] as $row)
                    <div class="dist-row">
                        <div class="dist-row__meta">
                            <span>{{ $row['label'] }}</span>
                            <span class="count">{{ $row['count'] }}</span>
                        </div>
                        <div class="dist-bar"><div class="dist-bar__fill" style="width: {{ $row['percent'] }}%"></div></div>
                    </div>
                @empty
                    <p class="dist-row__meta">Belum ada data.</p>
                @endforelse
            </div>

            <div class="card">
                <div class="card__head">
                    <h2>Assets by Category</h2>
                    <a href="{{ route('assets.index') }}">Lihat semua</a>
                </div>
                @forelse ($assetsByCategory ?? [] as $row)
                    <div class="dist-row">
                        <div class="dist-row__meta">
                            <span>{{ $row['label'] }}</span>
                            <span class="count">{{ $row['count'] }}</span>
                        </div>
                        <div class="dist-bar"><div class="dist-bar__fill" style="width: {{ $row['percent'] }}%"></div></div>
                    </div>
                @empty
                    <p class="dist-row__meta">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card__head">
                <h2>Recent Activities</h2>
                <a href="{{ route('stock-opname.index') }}">Lihat semua</a>
            </div>
            <ul class="activity-list">
                @forelse ($recentActivities ?? [] as $activity)
                    <li class="activity-item">
                        <div class="activity-icon {{ $activity['iconState'] ?? '' }}">
                            <i class="bi {{ $activity['icon'] }}"></i>
                        </div>
                        <div class="activity-body">
                            <p><strong>{{ $activity['assetNumber'] }}</strong> {{ $activity['message'] }}</p>
                            <time>{{ $activity['time'] }}</time>
                        </div>
                    </li>
                @empty
                    <li class="activity-item">
                        <div class="activity-body"><p>Belum ada aktivitas.</p></div>
                    </li>
                @endforelse
            </ul>
        </div>
    </section>
@endsection
