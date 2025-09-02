@props(['paginator'])

{{-- DataTables-style pagination info and navigation following consistent module patterns --}}
<div class="dataTables_wrapper">
    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="dataTables_info">
                {{ __('pagination.showing') }} {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} 
                {{ __('pagination.of') }} {{ $paginator->total() }} {{ __('pagination.results') }}
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            @if ($paginator->hasPages())
            <div class="dataTables_paginate paging_simple_numbers">
                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="paginate_button page-item previous disabled">
                            <span class="page-link">
                                {{ __('pagination.previous') }}
                            </span>
                        </li>
                    @else
                        <li class="paginate_button page-item previous">
                            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                                {{ __('pagination.previous') }}
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="paginate_button page-item active">
                                <span class="page-link">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="paginate_button page-item">
                                <a class="page-link" href="{{ $url }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="paginate_button page-item next">
                            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                                {{ __('pagination.next') }}
                            </a>
                        </li>
                    @else
                        <li class="paginate_button page-item next disabled">
                            <span class="page-link">
                                {{ __('pagination.next') }}
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
