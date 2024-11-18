<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation">
            <ul class="pagination" style="flex-wrap: wrap;
            gap: 4px;">
                @if ($paginator->onFirstPage())
                    <li class="page-item prev">
                        <button class="page-link" style="background: rgb(231 231 231);
                        color: #cccccc;" disabled><i
                                class="tf-icon bx bx-chevron-left"></i></button>
                    </li>
                @else
                    <li class="page-item prev">
                        <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="page-link"><i
                                class="tf-icon bx bx-chevron-left"></i></button>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span
                                class="page-link">{{ $element }}</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active" aria-current="page"><span
                                        class="page-link">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><button type="button" class="page-link"
                                        wire:click="gotoPage({{ $page }})">{{ $page }}</button></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach


                @if ($paginator->onLastPage())
                    <li class="page-item next">
                        <button class="page-link" style="background: rgb(231 231 231);
                        color: #cccccc;" disabled><i
                                class="tf-icon bx bx-chevron-right"></i></button>
                    </li>
                @else
                    <li class="page-item next">
                        <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="page-link"><i
                                class="tf-icon bx bx-chevron-right"></i></button>
                    </li>
                @endif
            </ul>


        </nav>
    @endif
</div>
