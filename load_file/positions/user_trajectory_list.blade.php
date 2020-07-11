@extends('layouts.public')


@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.positions.rent_trajectory_list')}}">租赁中的轨迹列表</a></li>
                        <li><a href="{{route('admin.positions.backup_trajectory_list')}}">已归还的轨迹列表</a></li>
                        <li class="active"><a href="{{route('admin.positions.user_trajectory_list')}}">用户轨迹列表</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" method="get">
                        <div class="form-group">
                            <label class="sr-only">账号/昵称</label>
                            <input type="text" name="deviceno" placeholder="账号/昵称" class="form-control" value="{{request('deviceno')}}">
                        </div>
                        <div class="form-group">
                            <input placeholder="请选择日期范围" class="form-control" id="created_at" type="text" name="created_at" value="{{request('created_at')}}" style="width: 200px;"
                                   autocomplete="off">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.positions.user_trajectory_list')}}'">重置</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <table class="table table-striped table-new table-hover dataTables-example dataTable">
                        <thead>
                        <tr role="row">
                            <th>账号</th>
                            <th>昵称</th>
                            <th>头像</th>
                            <th>游览日期</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @foreach($info as $g)
                            <tr class="gradeA">
                                <td>{{$g->username}}</td>
                                <td>{{$g->nickname}}</td>
                                <td>@if(!empty($g->avatar))<img src="{{$g->avatar}}" width="80px">@endif</td>
                                <td>{{$g->look_date}}</td>
                                <td>
                                    <a href="{{route('admin.positions.user_trajectory_info',[$g->uid,$g->look_date])}}?rent_name={{$g->nickname}}">查看轨迹</a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $info->links() !!}
                            {{--<span>共 {{ $info->total() }} 条记录</span>--}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            laydate.render({
                elem: '#created_at',
                range: '~',
                max: 0
            });
        });
    </script>
@endsection
