@if ($paginator->hasPages())
    <nav class="flex flex-column align-items-center gap-3 mt-5 w-full" role="navigation" aria-label="Paginación">
        <p class="m-0 text-600 text-xs uppercase" style="letter-spacing: 0.2em;">
            Mostrando {{ $paginator->firstItem() }} a {{ $paginator->lastItem() }} de {{ $paginator->total() }} resultados
        </p>

        <div class="flex align-items-center gap-2 flex-wrap justify-content-center">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 border-1 surface-border text-500 uppercase text-xs border-round-sm">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 border-1 surface-border text-900 uppercase text-xs border-round-sm" rel="prev">
                    Anterior
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-2 surface-100 text-600 border-round-sm">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-2 surface-900 text-0 border-round-sm font-bold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 border-1 surface-border text-900 border-round-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 border-1 surface-border text-900 uppercase text-xs border-round-sm" rel="next">
                    Siguiente
                </a>
            @else
                <span class="px-3 py-2 border-1 surface-border text-500 uppercase text-xs border-round-sm">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
