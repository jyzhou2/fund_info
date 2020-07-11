@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
        @endif
    </ul>

    <ul class="pagination">
        <li class="page-item disabled">
            <span style="border: none;cursor: text;">跳转至</span>
            <span style="border: none;cursor: text;padding:0px;">
                <input class="form-control" type="text" id="pagination_jump_input" style="width: 50px;padding: 2px;height: 27px;"/>
            </span>
            <span style="border: none;cursor: text;">页</span>
        </li>
    </ul>

    <script type="text/javascript">
        jQuery(function ($) {
            $('#pagination_jump_input').on('keyup', function (event) {
                if (event.keyCode === 13) {
                    let jumppage = $('#pagination_jump_input').val();
                    let l_url = location.href.toString();
                    let j_url;
                    if (!isNaN(jumppage)) {
                        if (l_url.indexOf('page=') > -1) {
                            j_url = l_url.replace(eval('/(page=)([^&]*)/gi'), 'page=' + jumppage);
                        } else {
                            if (l_url.indexOf('?') > -1) {
                                j_url = l_url + '&page=' + jumppage;
                            } else {
                                j_url = l_url + '?page=' + jumppage;
                            }
                        }
                        location.href = j_url;
                    }
                }
            });
        });
    </script>
@endif

<ul class="pagination">
    <li class="page-item disabled">
        <span style="border: none;cursor: text;">共{{$paginator->total()}}条记录</span>
    </li>
</ul>

<ul class="pagination">
    <li class="page-item disabled">
        <span style="border: none;cursor: text;">每页显示</span>
        <span style="border: none;cursor: text;padding:0px;">
            <select class="form-control" style="height: 27px;width:60px;padding: 2px;" id="pagination_perpage">
                <option value="10" @if($page_admin_perpage == 10) selected @endif>10</option>
                <option value="15" @if($page_admin_perpage == 15) selected @endif>15</option>
                <option value="30" @if($page_admin_perpage == 30) selected @endif>30</option>
                <option value="50" @if($page_admin_perpage == 50) selected @endif>50</option>
            </select>
        </span>
        <span style="border: none;cursor: text;">条</span>
    </li>
</ul>
<script type="text/javascript">
    jQuery(function ($) {
        $('#pagination_perpage').on('change', function () {
            let p_perpage = $('#pagination_perpage').val();
            if (!isNaN(p_perpage)) {
                let exp = new Date();
                exp.setTime(exp.getTime() + 30 * 24 * 60 * 60 * 1000);
                document.cookie = "page_admin_perpage=" + p_perpage + ";expires=" + exp.toUTCString();

                location.reload();
            }
        });
    });
</script>
