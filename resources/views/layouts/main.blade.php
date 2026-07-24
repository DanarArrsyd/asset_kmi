<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — STO Asset Inventory</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

<div class="shell" id="shell">

    @include('partials.sidebar')

    <div>
        @include('partials.navbar')

        <main class="content">
            <div class="breadcrumb">
                @yield('breadcrumb')
            </div>

            @yield('content')
        </main>

        @include('partials.footer')
    </div>
</div>

<script>
    const shell = document.getElementById('shell');
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        shell.classList.toggle('is-collapsed');
    });
    document.getElementById('mobileToggle')?.addEventListener('click', () => {
        shell.classList.toggle('is-mobile-open');
    });
</script>

@stack('scripts')
</body>
</html>
