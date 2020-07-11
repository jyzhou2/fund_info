@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.setting.adminusers')}}">管理员列表</a></li>
                        <li><a href="{{route('admin.setting.adminusers.add')}}">添加管理员</a></li>
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
                                <input type="text" name="username" placeholder="用户名" class="form-control" value="{{request('username')}}">
                            </div>
                            &nbsp;&nbsp;
                            <div class="form-group">
                                <input placeholder="请选择注册时间范围" class="form-control" id="created_at" type="text" name="created_at" value="{{request('created_at')}}" style="width: 200px;" autocomplete="off">
                            </div>
                            &nbsp;&nbsp;
                            <div class="form-group">
                                <select name="groupid" class="form-control">
                                    <option value="">请选择管理员角色</option>
                                    @foreach($admingroup as $g)
                                        <option value="{{$g->groupid}}" @if(request('groupid') == $g->groupid) selected @endif>{{$g->groupname}}</option>
                                    @endforeach
                                </select>
                            </div>
                            &nbsp;&nbsp;
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.setting.adminusers')}}'">重置</button>
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
                                <th>姓名</th>
                                <th class="sorting" orderby="created_at">注册时间</th>
                                <th class="sorting" orderby="updated_at">最后登录时间</th>
                                <th>最后登录IP</th>
                                <th>角色</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            @foreach($users as $user)
                                <tr class="gradeA">
                                    <td>{{$user['uid']}}</td>
                                    <td>{{$user['username']}}</td>
                                    <td>{{$user['nickname']}}</td>
                                    <td>{{$user['created_at']}}</td>
                                    <td>{{$user['updated_at']}}</td>
                                    <td>{{$user['lastloginip']}}</td>
                                    <th>{{$user['groupname']}}</th>
                                    <td>
                                        @if($user->uid != 1)
                                            <a href="{{route('admin.setting.adminusers.edit',[$user->uid])}}">编辑</a>
                                            | <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.setting.adminusers.delete',[$user->uid])}}" msg="是否删除该用户？">删除</a>
                                        @endif
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
