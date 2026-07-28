@props(['title', 'lede' => null])

<div class="page-header">
    <div class="page-header__text">
        <h1>{{ $title }}</h1>
        @if ($lede)
            <p>{{ $lede }}</p>
        @endif
    </div>

    @if (trim($slot) !== '')
        <div class="page-header__actions">{{ $slot }}</div>
    @endif
</div>
