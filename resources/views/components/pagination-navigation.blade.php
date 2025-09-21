@props(['paginator'])

@php
    // Helper function to preserve all query parameters
    function getPageUrl($paginator, $page) {
        $currentParams = request()->query();
        unset($currentParams['page']); // Remove page parameter as it will be set by paginator
        
        // Build URL with all current parameters
        $url = request()->url() . '?' . http_build_query(array_merge($currentParams, ['page' => $page]));
        
        return $url;
    }
@endphp

{{-- Compact pagination info and navigation --}}
<div class="d-flex justify-content-between align-items-center">
    <div class="pagination-info">
        <span class="text-muted">
            {{ __('pagination.showing') }} <strong>{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }}</strong> 
            {{ __('pagination.of') }} <strong>{{ $paginator->total() }}</strong> {{ __('pagination.results') }}
            @if($paginator->lastPage() > 1)
                <span class="ms-2 text-muted">
                    (صفحه {{ $paginator->currentPage() }} از {{ $paginator->lastPage() }})
                </span>
            @endif
        </span>
    </div>
    
    @if ($paginator->hasPages() || $paginator->lastPage() > 1)
    <div class="pagination-controls">
        <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="ti-angle-right"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ getPageUrl($paginator, $paginator->currentPage() - 1) }}" rel="prev">
                                <i class="ti-angle-right"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $currentPage = $paginator->currentPage();
                        $lastPage = $paginator->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp
                    
                    {{-- Show first page if not in range --}}
                    @if($start > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ getPageUrl($paginator, 1) }}">1</a>
                        </li>
                        @if($start > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif
                    
                    {{-- Show page range --}}
                    @for($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active">
                                <span class="page-link">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ getPageUrl($paginator, $page) }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endfor
                    
                    {{-- Show last page if not in range --}}
                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ getPageUrl($paginator, $lastPage) }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ getPageUrl($paginator, $paginator->currentPage() + 1) }}" rel="next">
                                <i class="ti-angle-left"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">
                                <i class="ti-angle-left"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
            @endif
</div>

