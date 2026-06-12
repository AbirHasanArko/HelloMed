@if ($paginator->hasPages())
    <nav class="pagination-nav" aria-label="Pagination Navigation">
        <div class="pagination-info">
            Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong> results
        </div>
        <div class="pages">
            @if ($paginator->onFirstPage())
                <span class="page-item disabled">&laquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-item disabled">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-item active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
            @else
                <span class="page-item disabled">&raquo;</span>
            @endif
        </div>
    </nav>
@endif
