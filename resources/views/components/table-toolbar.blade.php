@props([
    'action',
    'placeholder' => 'Cari...',
    'exportUrl' => null,
])

{{--
    The one toolbar shape every list page uses. The slot carries whatever
    filters that list exposes; everything else is fixed so the four list pages
    read as the same control, not four near-misses.
--}}
<form method="GET" action="{{ $action }}" class="toolbar" role="search">
    <label class="toolbar__search">
        <i class="bi bi-search" aria-hidden="true"></i>
        <span class="sr-only">Cari</span>
        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}">
    </label>

    {{ $slot }}

    {{-- Filtering must not silently discard the column the user sorted by. --}}
    @if (request('sort'))
        <input type="hidden" name="sort" value="{{ request('sort') }}">
    @endif
    @if (request('direction'))
        <input type="hidden" name="direction" value="{{ request('direction') }}">
    @endif

    <div class="toolbar__actions">
        <button type="submit" class="btn btn--secondary">Filter</button>
        <a href="{{ $action }}" class="btn btn--secondary">Reset</a>

        @if ($exportUrl)
            <a href="{{ $exportUrl }}" class="btn btn--secondary">
                <i class="bi bi-download" aria-hidden="true"></i> Export
            </a>
        @endif
    </div>
</form>
