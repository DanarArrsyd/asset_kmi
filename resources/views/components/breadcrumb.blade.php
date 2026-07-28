@props(['items' => []])

<nav class="breadcrumb" aria-label="Breadcrumb">
    <ol>
        <li><a href="{{ route('dashboard') }}">Home</a></li>

        @foreach ($items as $item)
            <li @class(['is-current' => $loop->last]) @if ($loop->last) aria-current="page" @endif>
                @if (! empty($item['href']) && ! $loop->last)
                    <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                @else
                    {{ $item['label'] }}
                @endif
            </li>
        @endforeach
    </ol>
</nav>
