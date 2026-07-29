<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- The QR landing page is reachable without a session; asset numbers
             have no business turning up in a search index. --}}
        <meta name="robots" content="noindex, nofollow">

        <title>{{ $title ?? 'Masuk' }} — {{ config('app.name') }}</title>

        <link rel="icon" type="image/png" sizes="64x64" href="@assetUrl('img/favicon-64.png')">
        <link rel="apple-touch-icon" href="@assetUrl('img/logo.png')">

        {{-- Tokens first: it declares @font-face and every custom property app.css reads. --}}
        <link rel="stylesheet" href="@assetUrl('css/tokens.css')">
        <link rel="stylesheet" href="@assetUrl('css/app.css')">
    </head>
    <body>
        @if ($split)
            <div class="auth-split">
                {{-- Identity only. Nothing here is a call to action — this is an
                     internal system, not a product someone is deciding to buy. --}}
                <aside class="auth-split__brand">
                    <div class="auth-split__mark">
                        <span class="auth-split__chip"><x-application-logo /></span>
                        <span class="auth-split__wordmark">{{ config('app.name') }}</span>
                    </div>

                    <div class="auth-split__copy">
                        <p class="auth-split__tagline">Sistem Manajemen Asset</p>
                        <p class="auth-split__org">{{ config('app.company') }}</p>
                    </div>

                    <p class="auth-split__foot">&copy; {{ date('Y') }} {{ config('app.company') }}</p>
                </aside>

                <main class="auth-split__panel">
                    <div class="auth-split__form">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        @else
            <div class="auth-shell">
                <a href="{{ route('home') }}" class="auth-logo">
                    <x-application-logo />
                    <span>{{ config('app.name') }}</span>
                </a>

                <div class="auth-card">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
