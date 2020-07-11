@extends('layouts.public')


@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.feedback.index')}}">意见反馈</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" method="get">
                        <div class="form-group">
                            <input type="text" name="username" placeholder="用户名" class="form-control" value="{{request('username')}}">
                        </div>
                        &nbsp;&nbsp;
                        <div class="form-group">
                            <input type="text" name="phone" placeholder="手机号" class="form-control" value="{{request('phone')}}">
                        </div>
                        @if($is_show_reply)
                            &nbsp;&nbsp;
                            <div class="form-group">
                                <label>回复状态</label>
                                <select class="form-control" name="reply_status">
                                    <option value="0">全部</option>
                                    <option value="1" @if(request('reply_status')==1) selected @endif>未回复</option>
                                    <option value="2" @if(request('reply_status')==2) selected @endif>已回复</option>
                                </select>
                            </div>
                        @endif
                        &nbsp;&nbsp;
                        <div class="form-group">
                            <input placeholder="请选择日期范围" class="form-control" id="created_at" type="text" name="created_at" value="{{request('created_at')}}" style="width: 200px;"
                                   autocomplete="off">
                        </div>
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.feedback.index')}}'">重置</button>
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
                            <th width="80"><input type="checkbox" class="checkAll"></th>
                            <th width="160">反馈用户名</th>
                            <th width="160">反馈用户手机号</th>
                            <th width="120">反馈图片</th>
                            <th>反馈内容</th>
                            <th width="160">反馈时间</th>
                            @if($is_show_reply)
                                {{--<th width="100">回复操作员</th>--}}
                                <th>回复内容</th>
                                <th width="160" class="sorting" orderby="reply_datetime">回复时间</th>
                            @endif
                            <th width="120">操作</th>
                        </tr>
                        </thead>
                        @foreach($data as $k=>$v)
                            <tr class="gradeA">
                                <td><input type="checkbox" class="checkItem" value="{{$v['id']}}"></td>
                                <td>{{$v['feedback_username'] ? $v['feedback_username'] : "匿名用户"}}</td>
                                <td>{{$v['feedback_user_phone']}}</td>
                                <td>
                                    @if($v->img)
                                        <a href="{{get_file_url($v->img)}}" title="点击查看大图" target="_blank"><img src="{{get_file_url($v->img)}}" style="width:120px;height:120px;"/></a>
                                    @else

                                    @endif
                                </td>
                                <td>{{$v['feedback_content']}}</td>
                                <td>{{$v['feedback_date_time']}}</td>
                                @if($is_show_reply)
                                    {{--<td>{{$v['reply_username']}}</td>--}}
                                    <td>{{$v['reply_content']}}</td>
                                    <td>{{$v['reply_datetime']}}</td>
                                @endif
                                <td>
                                    @if($is_show_reply)
                                        <a onclick="reply({{$v['id']}})" href="javascript:void(0);">回复</a> |
                                    @endif
                                    <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.feedback.delete', $v['id'])}}" title="删除" msg="是否删除该反馈信息？"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-danger btn-sm checkBtn" uri="{{route('admin.feedback.delete',[''])}}" msg="是否要删除这些反馈信息？删除后不可恢复">删除</button>

                        </div>
                    </div>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $data->links() !!}
                            {{--<span>共 {{ $data->total() }} 条记录</span>--}}
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
                function reply(id) {

                    var url = "{{route('admin.feedback.reply')}}?id=" + id;

                    layer.open({
                        title: "添加回复",
                        type: 2,
                        area: ['800px', '350px'],
                        fix: true, //固定
                        maxmin: true,
                        move: false,
                        content: url
                    });
                }
            </script>
@endsection
