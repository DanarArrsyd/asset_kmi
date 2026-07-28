<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print QR — {{ $asset->asset_number }}</title>

    <link rel="icon" type="image/png" sizes="64x64" href="@assetUrl('img/favicon-64.png')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="@assetUrl('css/tokens.css')">

    <style>
        body {
            margin: 0;
            font-family: var(--font-sans);
            color: var(--color-text);
            background: var(--color-surface);
            display: grid;
            place-items: center;
            min-height: 100vh;
        }
        .qr-sheet { text-align: center; padding: var(--space-2xl); }
        .qr-sheet__brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--space-xs);
            font-size: var(--text-xs);
            font-weight: var(--weight-semibold);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--color-text-muted);
            padding-bottom: var(--space-md);
            margin-bottom: var(--space-lg);
            border-bottom: 1px solid var(--color-border);
        }
        .qr-sheet__brand img { width: 20px; height: 20px; object-fit: contain; }
        .qr-sheet img.qr { width: 220px; height: 220px; display: block; margin: 0 auto; }
        .qr-sheet h1 {
            font-size: var(--text-base);
            font-weight: var(--weight-semibold);
            margin: var(--space-sm) 0 0;
            font-variant-numeric: tabular-nums;
        }
        .qr-sheet p { font-size: var(--text-sm); color: var(--color-text-muted); margin: var(--space-2xs) 0 0; }

        @page { margin: 12mm; }
        @media print {
            body { min-height: 0; display: block; }
            .qr-sheet { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="qr-sheet">
        <div class="qr-sheet__brand">
            <img src="@assetUrl('img/logo.png')" alt="">
            <span>{{ config('app.name') }}</span>
        </div>

        <img class="qr" src="{{ Storage::url($asset->qr_path) }}" alt="QR code {{ $asset->asset_number }}">
        <h1>{{ $asset->asset_number }}</h1>
        <p>{{ $asset->name }}</p>
    </div>
</body>
</html>
