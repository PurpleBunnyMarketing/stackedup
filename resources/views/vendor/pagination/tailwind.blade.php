@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <a  class="btn btn-icon btn-sm btn-light-primary mr-2 my-1 page-item disabled" disabled="disabled" aria-label="@lang('pagination.previous')" aria-disabled="true"><i class="ki ki-bold-double-arrow-back icon-xs"></i></a>
               {{--  <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li> --}}
            @else
                 <a  href="{{ $paginator->previousPageUrl() }}" class="btn btn-icon btn-sm btn-light-primary mr-2 my-1 page-item" rel="prev" aria-label="@lang('pagination.previous')"><i class="ki ki-bold-double-arrow-back icon-xs"></i></a>
              {{--   <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li> --}}
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
           
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <a  class="btn btn-icon btn-sm border-0 btn-hover-primary mr-2 my-1 page-item disabled">{{ $element }}</a>
                    {{-- <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li> --}}
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <a  class="btn btn-icon btn-sm border-0 btn-hover-primary  mr-2 my-1 page-item active" aria-current="page">{{ $page }}</a>
                            {{-- <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li> --}}
                        @else
                            <a href="{{ $url }}" class="btn btn-icon btn-sm border-0 btn-hover-primary  mr-2 my-1 page-item " aria-current="page">{{ $page }}</a>
                            {{-- <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li> --}}
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-icon btn-sm btn-light-primary mr-2 my-1 page-link" rel="next" aria-label="@lang('pagination.next')"><i class="ki ki-bold-double-arrow-next icon-xs"></i></a>
               {{--  <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li> --}}
            @else
              <a class="btn btn-icon btn-sm btn-light-primary mr-2 my-1 page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')"><i class="ki ki-bold-double-arrow-next icon-xs"></i></a>
              {{--   <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li> --}}
            @endif
        </ul>
    </nav>
@endif
