@props(['paginator', 'noun' => 'data'])

<div class="pagination-bar">
    <span>
        Menampilkan {{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}
        dari {{ $paginator->total() }} {{ $noun }}
    </span>

    {{ $paginator->onEachSide(1)->links() }}
</div>
