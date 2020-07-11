@extends('layouts.public')

@section('bodyattr')class=""@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.servicepoint.service_point_list')}}">服务设施列表</a></li>
                        <li><a href="{{route('admin.servicepoint.service_point_edit',['add'])}}">添加服务设施</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" id="form_submit" method="get">
                        <div class="form-group">
                            所在地区
                            <select name="map_id" id="select_calss1">
                                <option value="0">全部</option>
                                @foreach($map_info as $k=>$v)
                                    <option value="{{$v->id}}" title="{{$v->map_path}}" @if(request('map_id')==$v->id) selected @endif >{{$v->map_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <input type="text" name="service_name" placeholder="服务设施名称" class="form-control" value="{{request('service_name')}}" style=" width: 240px;" maxlength="20">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.servicepoint.service_point_list')}}'">重置</button>
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
                            <th width="170">服务设施名称</th>
                            <th width="250">设施图片</th>
                            <th width="180">编辑时间</th>
                            <th width="150">操作</th>
                        </tr>
                        </thead>
                        @foreach($info as $g)
                            <tr class="gradeA">
                                <td>{{$g['service_name']}}</td>
                                <td><img src="{{$g['img']}}" width="100px"></td>
                                <td>{{$g['updated_at']}}</td>
                                <td>
                                    <a href="{{route('admin.servicepoint.service_point_edit',[ $g['id']])}}" title="编辑"><i class="fa fa-edit"></i></a>
                                    <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.servicepoint.service_point_delete' ,[$g['id']])}}?img_path={{$g['img']}}" title="删除"
                                       msg="是否删除该设施？"><i class="fa fa-trash"></i></a>
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
    <script>
        $('#select_calss1,#select_calss2,#select_calss3').change(function () {
            $('#form_submit').submit();
        })
    </script>
@endsection
