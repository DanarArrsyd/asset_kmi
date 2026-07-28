@php
    $user = auth()->user();
    $initials = $user
        ? collect(explode(' ', $user->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('')
        : '?';
@endphp

<header class="navbar">
    <button class="navbar__icon-btn navbar__hamburger" id="mobileToggle" type="button"
            aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    <div class="navbar__title">@yield('title', 'Dashboard')</div>

    {{-- Global search resolves to the asset list, which is the only searchable
         index in the app today. --}}
    <form class="navbar__search" method="GET" action="{{ route('assets.index') }}" role="search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label for="globalSearch" class="sr-only">Cari aset</label>
        <input id="globalSearch" type="search" name="q" value="{{ request('q') }}"
               placeholder="Cari nomor / nama aset...">
    </form>

    <div class="navbar__actions">
        {{-- Notifications are a future module (see CLAUDE.md). The slot stays so
             the chrome is stable, but the control is disabled rather than inert. --}}
        <button class="navbar__icon-btn" type="button" disabled
                aria-label="Notifikasi — belum tersedia" title="Notifikasi — belum tersedia">
            <i class="bi bi-bell" aria-hidden="true"></i>
        </button>

        <details class="navbar__user">
            <summary class="navbar__user-trigger">
                <div class="navbar__avatar" aria-hidden="true">{{ $initials }}</div>
                <div class="navbar__user-meta">
                    <div class="navbar__user-name">{{ $user->name ?? 'Guest' }}</div>
                    <div class="navbar__user-role">{{ $user?->role?->label() ?? '-' }}</div>
                </div>
                <i class="bi bi-chevron-down navbar__user-caret" aria-hidden="true"></i>
            </summary>

            <div class="navbar__dropdown">
                <a href="{{ route('profile.edit') }}" class="navbar__dropdown-item">
                    <i class="bi bi-person-circle" aria-hidden="true"></i> Profile
                </a>
                <div class="navbar__dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="navbar__dropdown-item is-danger">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Log Out
                    </button>
                </form>
            </div>
        </details>
    </div>
</header>
