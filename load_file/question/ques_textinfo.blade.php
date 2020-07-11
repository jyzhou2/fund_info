@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection
@section('body')
    <div class="js-check-wrap">

        <form class="js-ajax-form" action="" method="post">

            <table class="table table-hover table-bordered table-list">
                <thead>
                <tr>
                    <th>作答详情</th>
                    <th>提交时间</th>
                </tr>
                </thead>
                @foreach($info as $k=>$g)
                    <tr>
                        <td>{{$g['text_info']}}</td>
                        <td>{{$g['date_time']}}</td>
                    </tr>
                @endforeach
            </table>
        </form>
        <div class="row" style="margin-left: 20px">
            <div class="col-sm-12">
               {{-- <div>共 {{ $info->total() }} 条记录</div>--}}
                {!! $info->links() !!}
            </div>
        </div>

    </div>

@endsection