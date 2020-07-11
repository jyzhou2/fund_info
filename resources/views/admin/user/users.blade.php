@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.user.users')}}">用户列表</a></li>
                        <li><a href="{{route('admin.user.users.add')}}">添加用户</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <form role="form" class="form-inline" method="get">
                            <div class="form-group">
                                <label class="sr-only">用户名</label>
                                <input type="text" name="username" placeholder="用户名/邮箱/手机" class="form-control" value="{{request('username')}}">
                            </div>
                            &nbsp;&nbsp;
                            <div class="form-group">
                                <input placeholder="请选择注册时间范围" class="form-control" id="created_at" type="text" name="created_at" value="{{request('created_at')}}" style="width: 200px;"
                                       autocomplete="off">
                            </div>
                            {{--&nbsp;&nbsp;
                            <div class="form-group">
                                <input type="checkbox" name="is_test" value="1" @if(request('is_test') == 1) checked @endif />测试用户
                            </div>
                            &nbsp;&nbsp;--}}
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.user.users')}}'">重置</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <table class="table table-striped table-bordered table-hover dataTables-example dataTable">
                            <thead>
                            <tr role="row">
                                <th>用户ID</th>
                                <th>用户名</th>
                                <th>邮箱</th>
                                <th>手机号</th>
                                <th>昵称</th>
                                <th class="sorting" orderby="created_at">注册时间</th>
                                <th class="sorting" orderby="updated_at">最后登录时间</th>
                                <th>最后登录IP</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            @foreach($users as $user)
                                <tr class="gradeA">
                                    <td>{{$user['uid']}}</td>
                                    <td>{{$user['username']}}</td>
                                    <td>{{$user['email']}}</td>
                                    <td>{{$user['phone']}}</td>
                                    <td>{{$user['nickname']}}</td>
                                    <td>{{$user['created_at']}}</td>
                                    <td>{{$user['updated_at']}}</td>
                                    <td>{{$user['lastloginip']}}</td>
                                    <td>
                                        <a href="{{route('admin.user.users.edit',[$user->uid])}}">编辑</a>
                                        | <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.user.users.delete',[$user->uid])}}" msg="是否删除该用户？">删除</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                {{--<div>共 {{ $users->total() }} 条记录</div>--}}
                                {!! $users->links() !!}
                            </div>
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
