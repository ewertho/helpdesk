@if ($paginator->hasPages())
    <nav class="pages" aria-label="Paginação">
        @if ($paginator->onFirstPage())
            <span>Anteriores</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">Anteriores</a>
        @endif
        <span>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Próximos</a>
        @else
            <span>Próximos</span>
        @endif
    </nav>
@endif
