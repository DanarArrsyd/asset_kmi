<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print QR — {{ $asset->asset_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; text-align: center; padding: 2rem; color: #1F2937; }
        img { width: 220px; height: 220px; }
        h1 { font-size: 1rem; margin: .75rem 0 0; }
        p { font-size: .8rem; color: #6B7280; margin: .25rem 0 0; }
    </style>
</head>
<body onload="window.print()">
    <img src="{{ Storage::url($asset->qr_path) }}" alt="QR {{ $asset->asset_number }}">
    <h1>{{ $asset->asset_number }}</h1>
    <p>{{ $asset->name }}</p>
</body>
</html>
