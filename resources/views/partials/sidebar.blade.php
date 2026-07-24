@php
    $menu = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'bi-grid-1x2', 'href' => route('dashboard')],
    ];

    $masterData = [
        ['key' => 'asset', 'label' => 'Asset', 'icon' => 'bi-box', 'href' => route('assets.index')],
    ];

    if (auth()->user()?->can('viewAny', \App\Models\Category::class)) {
        $masterData[] = ['key' => 'category', 'label' => 'Category', 'icon' => 'bi-tags', 'href' => route('categories.index')];
        $masterData[] = ['key' => 'department', 'label' => 'Department', 'icon' => 'bi-diagram-3', 'href' => route('departments.index')];
        $masterData[] = ['key' => 'location', 'label' => 'Location', 'icon' => 'bi-geo-alt', 'href' => route('locations.index')];
        $masterData[] = ['key' => 'brand', 'label' => 'Brand', 'icon' => 'bi-bookmark', 'href' => route('brands.index')];
    }

    $transactions = [
        ['key' => 'sto', 'label' => 'Stock Opname', 'icon' => 'bi-qr-code-scan', 'href' => route('stock-opname.index')],
        ['key' => 'maintenance', 'label' => 'Maintenance', 'icon' => 'bi-tools', 'href' => '#'],
        ['key' => 'transfer', 'label' => 'Asset Transfer', 'icon' => 'bi-arrow-left-right', 'href' => '#'],
    ];

    $other = [
        ['key' => 'reports', 'label' => 'Reports', 'icon' => 'bi-bar-chart', 'href' => '#'],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'bi-gear', 'href' => '#'],
        ['key' => 'profile', 'label' => 'Profile', 'icon' => 'bi-person-circle', 'href' => route('profile.edit')],
    ];

    if (auth()->user()?->can('viewAny', \App\Models\User::class)) {
        array_splice($other, 2, 0, [[
            'key' => 'users', 'label' => 'Users', 'icon' => 'bi-people', 'href' => route('users.index'),
        ]]);
    }

    $current = $activeMenu ?? null;
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar__brand">
        <i class="bi bi-box-seam"></i>
        <span class="brand-text">STO Asset</span>
    </div>

    <nav>
        @foreach ($menu as $item)
            <a href="{{ $item['href'] }}" class="sidebar__link {{ $current === $item['key'] ? 'is-active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="sidebar__group-label">Master Data</div>
        @foreach ($masterData as $item)
            <a href="{{ $item['href'] }}" class="sidebar__link {{ $current === $item['key'] ? 'is-active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="sidebar__group-label">Transactions</div>
        @foreach ($transactions as $item)
            <a href="{{ $item['href'] }}" class="sidebar__link {{ $current === $item['key'] ? 'is-active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <div class="sidebar__group-label">&nbsp;</div>
        @foreach ($other as $item)
            <a href="{{ $item['href'] }}" class="sidebar__link {{ $current === $item['key'] ? 'is-active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <button class="sidebar__toggle" id="sidebarToggle" aria-label="Collapse sidebar">
        <i class="bi bi-chevron-double-left"></i>
    </button>
</aside>
