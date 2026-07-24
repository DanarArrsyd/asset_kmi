@php
    $user = auth()->user();
    $initials = $user ? collect(explode(' ', $user->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') : '?';
    $unreadCount = $unreadCount ?? 0;
@endphp

<header class="navbar">
    <button class="navbar__icon-btn navbar__hamburger" id="mobileToggle" aria-label="Buka menu">
        <i class="bi bi-list"></i>
    </button>

    <div class="navbar__title">@yield('title', 'Dashboard')</div>

    <label class="navbar__search">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Cari aset, kategori, lokasi...">
    </label>

    <div class="navbar__actions">
        <button class="navbar__icon-btn" aria-label="Notifikasi">
            <i class="bi bi-bell"></i>
            @if ($unreadCount > 0)
                <span class="navbar__badge"></span>
            @endif
        </button>

        <details class="navbar__user">
            <summary class="navbar__user-trigger">
                <div class="navbar__avatar">{{ $initials }}</div>
                <div class="navbar__user-meta">
                    <div class="navbar__user-name">{{ $user->name ?? 'Guest' }}</div>
                    <div class="navbar__user-role">{{ $user?->role?->label() ?? '-' }}</div>
                </div>
                <i class="bi bi-chevron-down" style="font-size: .75rem; color: var(--color-text-muted);"></i>
            </summary>

            <div class="navbar__dropdown">
                <a href="{{ route('profile.edit') }}" class="navbar__dropdown-item">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
                <div class="navbar__dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="navbar__dropdown-item is-danger">
                        <i class="bi bi-box-arrow-right"></i> Log Out
                    </button>
                </form>
            </div>
        </details>
    </div>
</header>
