@php
    $isPending = ! empty($item['pending']);
    $isActive = ! $isPending && ($current ?? null) === $item['key'];
@endphp

@if ($isPending)
    {{-- Named in the spec, not shipped. Rendered as text so the menu never
         promises a destination it cannot reach. --}}
    <span class="sidebar__link is-pending" title="{{ $item['label'] }} — modul belum tersedia">
        <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
        <span>{{ $item['label'] }}</span>
        <span class="sidebar__soon">soon</span>
    </span>
@else
    <a href="{{ $item['href'] }}"
       class="sidebar__link @if ($isActive) is-active @endif"
       @if ($isActive) aria-current="page" @endif>
        <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
        <span>{{ $item['label'] }}</span>
    </a>
@endif
