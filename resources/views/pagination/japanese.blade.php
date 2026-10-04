@if ($paginator->hasPages())
    <nav role="navigation" aria-label="ページ送り" class="flex flex-wrap items-center justify-between gap-2 text-sm">
        <p class="text-slate-600">{{ $paginator->total() }}件中 {{ $paginator->firstItem() }}〜{{ $paginator->lastItem() }}件を表示</p>
        <div class="flex flex-wrap gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded border border-slate-200 bg-slate-50 px-3 py-1 text-slate-400">前へ</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded border border-slate-300 bg-white px-3 py-1 text-slate-700 hover:bg-slate-50">前へ</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-1 text-slate-400">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="rounded bg-blue-600 px-3 py-1 text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="rounded border border-slate-300 bg-white px-3 py-1 text-slate-700 hover:bg-slate-50">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded border border-slate-300 bg-white px-3 py-1 text-slate-700 hover:bg-slate-50">次へ</a>
            @else
                <span class="rounded border border-slate-200 bg-slate-50 px-3 py-1 text-slate-400">次へ</span>
            @endif
        </div>
    </nav>
@endif
