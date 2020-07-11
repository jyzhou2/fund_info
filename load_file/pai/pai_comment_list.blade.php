@extends('layouts.public')

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.paicheck.pai_list')}}">现场留念审核</a></li>
                        <li class="active"><a href="{{route('admin.paicheck.pai_comment_list')}}">现场留念评论审核</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" id="form_submit" method="get">
                        <div class="form-group">
                            类别
                            <select name="is_check" id="select_calss">
                                <option @if(request('is_check')==1) selected @endif value="1">待审核</option>
                                <option @if(request('is_check')==2) selected @endif value="2">已通过审核</option>
                                <option @if(request('is_check')==3) selected @endif value="3">未通过审核</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <input placeholder="请选择日期范围" class="form-control" id="created_at" type="text" name="created_at" value="{{request('created_at')}}" style="width: 200px;"
                                   autocomplete="off">
                        </div>
                        &nbsp;&nbsp;
                        <div class="form-group">
                            <input type="text" name="comment" placeholder="评论内容" class="form-control" value="{{request('comment')}}" style="width: 200px;" maxlength="20">
                        </div>
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.paicheck.pai_comment_list')}}'">重置</button>
                    </form>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="row" style="margin-bottom: 10px">
                        <div class="col-sm-12">
                            @if(request('is_check')==1||empty(request('is_check')))
                                <button type="button" class="btn btn-success btn-sm checkBtn" uri="{{route('admin.paicheck.pass_check',[4,''])}}" msg="是否要通过审核">通过审核</button>
                                <button type="button" class="btn btn-success btn-sm checkBtn" uri="{{route('admin.paicheck.unpass_check',[4,''])}}" msg="是否要不通过审核">不通过审核</button>
                            @endif
                            <button type="button" class="btn btn-danger btn-sm checkBtn" uri="{{route('admin.paicheck.del_check',[4,''])}}" msg="是否要删除？删除后不可恢复">删除</button>
                        </div>
                    </div>
                    <table class="table table-striped table-new table-hover infoTables-example infoTable">
                        <thead>
                        <tr role="row">
                            <th width="10"><input type="checkbox" class="checkAll"></th>
                            <th width="100">用户账号</th>
                            <th width="100">用户昵称</th>
                            <th width="100">发布时间</th>
                            <th width="250">发布内容</th>
                            <th width="100">操作</th>
                        </tr>
                        </thead>
                        @foreach($info as $k=>$v)
                            <tr class="gradeA">
                                <td><input type="checkbox" class="checkItem" value="{{$v['id']}}"></td>
                                <td>{{$v->username}}</td>
                                <td>{!! $v->nickname !!}</td>
                                <td>{{$v->created_at}}</td>
                                <td>{!! $v->comment !!}</td>
                                <td>
                                    @if(request('is_check')==1||empty(request('is_check')))
                                        <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.paicheck.pass_check' ,[4, $v['id']])}}" msg="是否要通过审核？"><i class="fa fa-check" title="通过"></i></a>
                                        <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.paicheck.unpass_check',[4, $v['id']])}}" msg="是否要不通过审核？"><i class="fa fa-ban" title="不通过"></i></a>
                                    @endif
                                    <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.paicheck.del_check',[4, $v['id']])}}" msg="是否删除？"><i class="fa fa-trash" title="删除"></i></a>
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
            <script>
                $('#select_calss').change(function () {
                    $('#form_submit').submit();
                })
            </script>
@endsection
