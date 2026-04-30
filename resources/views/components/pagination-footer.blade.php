@props(['paginator'])

<div class="px-6 py-4 bg-white border-t border-gray-200 flex items-center justify-between rounded-b-[10px]">
    @if(method_exists($paginator, 'hasPages'))
        @php
            $start = $paginator->firstItem() ?? 0;
            $end = $paginator->lastItem() ?? 0;
            $total = $paginator->total();
        @endphp
        <span class="text-[12px] font-medium text-black/50">{{ $start }} - {{ $end }} dari {{ $total }} baris</span>
        @if ($paginator->hasPages())
            <div class="flex items-center gap-2">
                @if ($paginator->onFirstPage())
                    <button disabled class="px-3 py-1 border border-gray-300 rounded text-[12px] opacity-30 cursor-not-allowed">Previous</button>
                @else
                    <a href="{{ $paginator->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 transition-colors">Previous</a>
                @endif
                
                <div class="flex items-center gap-1">
                @php
                    $startPage = max($paginator->currentPage() - 2, 1);
                    $endPage = min($startPage + 4, $paginator->lastPage());
                    if ($endPage - $startPage < 4) {
                        $startPage = max($endPage - 4, 1);
                    }
                @endphp
                @for ($i = $startPage; $i <= $endPage; $i++)
                    @if ($i == $paginator->currentPage())
                        <span class="w-8 h-8 rounded text-[12px] font-bold bg-blue-600 text-white shadow-md flex items-center justify-center">{{ $i }}</span>
                    @else
                        <a href="{{ $paginator->appends(request()->query())->url($i) }}" class="w-8 h-8 rounded text-[12px] font-bold text-black hover:bg-gray-100 flex items-center justify-center transition-all">{{ $i }}</a>
                    @endif
                @endfor
                </div>

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded text-[12px] hover:bg-gray-50 transition-colors">Next</a>
                @else
                    <button disabled class="px-3 py-1 border border-gray-300 rounded text-[12px] opacity-30 cursor-not-allowed">Next</button>
                @endif
            </div>
        @endif
    @else
        <span class="text-[12px] font-medium text-black/50">0 - 0 dari 0 baris</span>
    @endif
</div>
