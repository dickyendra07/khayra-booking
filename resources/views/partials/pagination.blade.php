@if ($paginator->hasPages())
    <nav class="kx-pagination" role="navigation" aria-label="Paginasi">
        <style>
            .kx-pagination { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-top: 16px; }
            .kx-pagination__info { font-size: 13px; color: #64748b; font-weight: 700; }
            .kx-pagination__links { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
            .kx-pagination__gap { padding: 0 4px; color: #94a3b8; font-weight: 800; }
            .kx-pagination .kx-btn[aria-current="page"] { pointer-events: none; }
            .kx-pagination .is-disabled { opacity: .45; pointer-events: none; }
        </style>

        <div class="kx-pagination__info">Menampilkan {{ number_format($paginator->firstItem()) }}–{{ number_format($paginator->lastItem()) }} dari {{ number_format($paginator->total()) }} data</div>

        <div class="kx-pagination__links">
            @if ($paginator->onFirstPage())
                <span class="kx-btn kx-btn--outline kx-btn--sm is-disabled" aria-disabled="true">‹ Sebelumnya</span>
            @else
                <a class="kx-btn kx-btn--outline kx-btn--sm" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="kx-pagination__gap">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="kx-btn kx-btn--primary kx-btn--sm" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="kx-btn kx-btn--outline kx-btn--sm" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="kx-btn kx-btn--outline kx-btn--sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya ›</a>
            @else
                <span class="kx-btn kx-btn--outline kx-btn--sm is-disabled" aria-disabled="true">Berikutnya ›</span>
            @endif
        </div>
    </nav>
@endif
