{{-- DataTables-style pagination component following consistent module patterns --}}
@if ($paginator->hasPages())
    <div class="dataTables_wrapper">
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
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="paginate_button page-item disabled">
                            <span class="page-link">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
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
    </div>
@endif
