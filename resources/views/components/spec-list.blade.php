@props(['text'])

@php
    // One item per line, as typed into the form. A line reading "Label : value"
    // becomes a labelled row; anything else stays a plain line, so free-form
    // notes are not mangled into a table that does not fit them.
    $lines = array_filter(array_map('trim', preg_split('/\R/', (string) $text) ?: []), fn ($line) => $line !== '');
@endphp

<dl class="spec-list">
    @foreach ($lines as $line)
        @php
            [$term, $value] = array_pad(preg_split('/\s*:\s*/', $line, 2), 2, null);
        @endphp

        @if ($value !== null && $value !== '')
            <dt class="spec-list__term">{{ $term }}</dt>
            <dd class="spec-list__value">{{ $value }}</dd>
        @else
            <dd class="spec-list__note">{{ $line }}</dd>
        @endif
    @endforeach
</dl>
