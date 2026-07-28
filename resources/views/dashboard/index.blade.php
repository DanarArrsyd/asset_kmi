@extends('layouts.main')

@php($activeMenu = 'dashboard')

@section('title', 'Dashboard')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Dashboard']]" />
@endsection

@section('content')
    <x-page-header title="Dashboard" lede="Ringkasan aset & progres stock opname.">
        @can('create', \App\Models\Asset::class)
            <a href="{{ route('assets.create') }}" class="btn btn--primary">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Asset
            </a>
        @endcan
    </x-page-header>

    <div class="stack">
        <section class="kpi-grid" aria-label="Ringkasan aset">
            <div class="card">
                <div class="kpi-card__label"><i class="bi bi-boxes" aria-hidden="true"></i> Total Assets</div>
                <div class="kpi-card__value">{{ number_format($totalAssets ?? 0) }}</div>
                <div class="kpi-card__delta">Seluruh departemen</div>
            </div>

            <div class="card kpi-card--accent">
                <div class="kpi-card__label"><i class="bi bi-check-circle" aria-hidden="true"></i> Active</div>
                <div class="kpi-card__value">{{ number_format($activeAssets ?? 0) }}</div>
                <div class="kpi-card__delta is-up">
                    <i class="bi bi-arrow-up-short" aria-hidden="true"></i> {{ $activePercent ?? 0 }}% dari total
                </div>
            </div>

            <div class="card kpi-card--warning">
                <div class="kpi-card__label"><i class="bi bi-tools" aria-hidden="true"></i> Maintenance</div>
                <div class="kpi-card__value">{{ number_format($maintenanceAssets ?? 0) }}</div>
                <div class="kpi-card__delta">{{ $maintenancePercent ?? 0 }}% dari total</div>
            </div>

            <div class="card kpi-card--danger">
                <div class="kpi-card__label"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i> Missing</div>
                <div class="kpi-card__value">{{ number_format($missingAssets ?? 0) }}</div>
                <div class="kpi-card__delta is-down">
                    <i class="bi bi-arrow-down-short" aria-hidden="true"></i> perlu tindak lanjut
                </div>
            </div>

            <div class="card">
                <div class="kpi-card__label"><i class="bi bi-clipboard-check" aria-hidden="true"></i> STO Progress</div>
                <div class="kpi-card__value">{{ $stoProgress ?? 0 }}%</div>
                <div class="dist-bar" role="progressbar"
                     aria-valuenow="{{ $stoProgress ?? 0 }}" aria-valuemin="0" aria-valuemax="100"
                     aria-label="Progres stock opname">
                    <div class="dist-bar__fill" style="width: {{ $stoProgress ?? 0 }}%"></div>
                </div>
            </div>
        </section>

        <section class="grid-2col">
            <div class="stack">
                <div class="panel">
                    <div class="panel__head">
                        <div class="panel__head-text"><h2>Assets by Department</h2></div>
                        <a href="{{ route('assets.index') }}" class="panel__head-link">Lihat semua</a>
                    </div>
                    <div class="panel__body">
                        @forelse ($assetsByDepartment ?? [] as $row)
                            <div class="dist-row">
                                <div class="dist-row__meta">
                                    <span>{{ $row['label'] }}</span>
                                    <span class="count">{{ $row['count'] }}</span>
                                </div>
                                <div class="dist-bar">
                                    <div class="dist-bar__fill" style="width: {{ $row['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="dist-empty">Belum ada data.</p>
                        @endforelse
                    </div>
                </div>

                <div class="panel">
                    <div class="panel__head">
                        <div class="panel__head-text"><h2>Assets by Category</h2></div>
                        <a href="{{ route('assets.index') }}" class="panel__head-link">Lihat semua</a>
                    </div>
                    <div class="panel__body">
                        @forelse ($assetsByCategory ?? [] as $row)
                            <div class="dist-row">
                                <div class="dist-row__meta">
                                    <span>{{ $row['label'] }}</span>
                                    <span class="count">{{ $row['count'] }}</span>
                                </div>
                                <div class="dist-bar">
                                    <div class="dist-bar__fill" style="width: {{ $row['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="dist-empty">Belum ada data.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Recent Activities</h2></div>
                    <a href="{{ route('stock-opname.index') }}" class="panel__head-link">Lihat semua</a>
                </div>
                <div class="panel__body">
                    <ul class="activity-list">
                        @forelse ($recentActivities ?? [] as $activity)
                            <li class="activity-item">
                                <div class="activity-icon {{ $activity['iconState'] ?? '' }}">
                                    <i class="bi {{ $activity['icon'] }}" aria-hidden="true"></i>
                                </div>
                                <div class="activity-body">
                                    <p><strong>{{ $activity['assetNumber'] }}</strong> {{ $activity['message'] }}</p>
                                    <time>{{ $activity['time'] }}</time>
                                </div>
                            </li>
                        @empty
                            <li class="activity-item">
                                <div class="activity-body"><p class="dist-empty">Belum ada aktivitas.</p></div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>
    </div>
@endsection
