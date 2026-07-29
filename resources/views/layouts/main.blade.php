<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" sizes="64x64" href="@assetUrl('img/favicon-64.png')">
    <link rel="apple-touch-icon" href="@assetUrl('img/logo.png')">

    {{-- Tokens first: it declares @font-face and every custom property app.css reads. --}}
    <link rel="stylesheet" href="@assetUrl('css/tokens.css')">
    <link rel="stylesheet" href="@assetUrl('css/app.css')">

    @stack('styles')
</head>
<body>

<div class="shell" id="shell">

    @include('partials.sidebar')

    {{-- Closes the mobile drawer. A real element, not a pseudo-element — an
         ::after overlay cannot receive the tap that dismisses it. --}}
    <button type="button" class="shell__scrim" id="sidebarScrim" aria-label="Tutup menu" tabindex="-1"></button>

    <div class="shell__main">
        @include('partials.navbar')

        <main class="content" id="main-content">
            @yield('breadcrumb')

            @yield('content')
        </main>

        @include('partials.footer')
    </div>
</div>

<script>
    (function () {
        const shell = document.getElementById('shell');
        const railToggle = document.getElementById('sidebarToggle');
        const mobileToggle = document.getElementById('mobileToggle');
        const scrim = document.getElementById('sidebarScrim');
        const STORAGE_KEY = 'sto.sidebar.collapsed';

        if (localStorage.getItem(STORAGE_KEY) === '1') {
            shell.classList.add('is-collapsed');
        }
        syncRail();

        railToggle?.addEventListener('click', () => {
            const collapsed = shell.classList.toggle('is-collapsed');
            localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
            syncRail();
        });

        mobileToggle?.addEventListener('click', () => setDrawer(! shell.classList.contains('is-mobile-open')));
        scrim?.addEventListener('click', () => setDrawer(false));

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && shell.classList.contains('is-mobile-open')) {
                setDrawer(false);
                mobileToggle?.focus();
            }
        });

        function setDrawer(open) {
            shell.classList.toggle('is-mobile-open', open);
            mobileToggle?.setAttribute('aria-expanded', String(open));
            if (scrim) scrim.tabIndex = open ? 0 : -1;
        }

        function syncRail() {
            if (! railToggle) return;
            const collapsed = shell.classList.contains('is-collapsed');
            const icon = railToggle.querySelector('i');
            railToggle.setAttribute('aria-expanded', String(! collapsed));
            railToggle.setAttribute('aria-label', collapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar');
            icon?.classList.toggle('bi-chevron-double-right', collapsed);
            icon?.classList.toggle('bi-chevron-double-left', ! collapsed);
        }
    })();
</script>

@stack('scripts')
</body>
</html>
