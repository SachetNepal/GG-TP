@if ($paginator->hasPages())
    <nav class="pagination-nav" role="navigation" aria-label="Pagination">
        <p class="pagination-summary">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </p>
        <ul class="pagination-list">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="pagination-link pagination-link--disabled" aria-disabled="true">Previous</span>
                @else
                    <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
                @endif
            </li>

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination-ellipsis" aria-hidden="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-link pagination-link--current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
                @else
                    <span class="pagination-link pagination-link--disabled" aria-disabled="true">Next</span>
                @endif
            </li>
        </ul>
    </nav>
@endif
