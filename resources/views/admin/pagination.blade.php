@if ($paginator->hasPages())
    @if ($paginator->onFirstPage())
        <span class="dots">&laquo;</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">&laquo;</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="dots">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">&raquo;</a>
    @else
        <span class="dots">&raquo;</span>
    @endif
@endif
