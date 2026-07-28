@php
    $canManageMasterData = auth()->user()?->can('viewAny', \App\Models\Category::class);
    $canManageUsers = auth()->user()?->can('viewAny', \App\Models\User::class);

    $groups = [
        [
            'label' => null,
            'items' => [
                ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid-1x2', 'href' => route('dashboard')],
            ],
        ],
        [
            'label' => 'Master Data',
            'items' => array_values(array_filter([
                ['key' => 'asset', 'label' => 'Asset', 'icon' => 'bi-box', 'href' => route('assets.index')],
                $canManageMasterData ? ['key' => 'category', 'label' => 'Category', 'icon' => 'bi-tags', 'href' => route('categories.index')] : null,
                $canManageMasterData ? ['key' => 'department', 'label' => 'Department', 'icon' => 'bi-diagram-3', 'href' => route('departments.index')] : null,
                $canManageMasterData ? ['key' => 'location', 'label' => 'Location', 'icon' => 'bi-geo-alt', 'href' => route('locations.index')] : null,
                $canManageMasterData ? ['key' => 'brand', 'label' => 'Brand', 'icon' => 'bi-bookmark', 'href' => route('brands.index')] : null,
            ])),
        ],
        [
            'label' => 'Transaksi',
            'items' => [
                ['key' => 'sto', 'label' => 'Stock Opname', 'icon' => 'bi-qr-code-scan', 'href' => route('stock-opname.index')],
                ['key' => 'maintenance', 'label' => 'Maintenance', 'icon' => 'bi-tools', 'pending' => true],
                ['key' => 'transfer', 'label' => 'Asset Transfer', 'icon' => 'bi-arrow-left-right', 'pending' => true],
            ],
        ],
        [
            'label' => 'Laporan',
            'items' => [
                ['key' => 'reports', 'label' => 'Reports', 'icon' => 'bi-bar-chart', 'pending' => true],
            ],
        ],
    ];

    $footItems = array_values(array_filter([
        $canManageUsers ? ['key' => 'users', 'label' => 'Users', 'icon' => 'bi-people', 'href' => route('users.index')] : null,
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'bi-gear', 'pending' => true],
        ['key' => 'profile', 'label' => 'Profile', 'icon' => 'bi-person-circle', 'href' => route('profile.edit')],
    ]));

    $current = $activeMenu ?? null;
@endphp

<aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar__brand">
        <span class="sidebar__brand-chip"><x-application-logo /></span>
        <span class="brand-text">{{ config('app.name') }}</span>
    </a>

    <nav class="sidebar__nav" aria-label="Menu utama">
        @foreach ($groups as $group)
            <div class="sidebar__group">
                @if ($group['label'])
                    <div class="sidebar__group-label">{{ $group['label'] }}</div>
                @endif

                @foreach ($group['items'] as $item)
                    @include('partials.sidebar-item', ['item' => $item, 'current' => $current])
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="sidebar__foot">
        @foreach ($footItems as $item)
            @include('partials.sidebar-item', ['item' => $item, 'current' => $current])
        @endforeach

        <button class="sidebar__toggle" id="sidebarToggle" type="button" aria-expanded="true" aria-label="Ciutkan sidebar">
            <i class="bi bi-chevron-double-left" aria-hidden="true"></i>
        </button>
    </div>
</aside>
