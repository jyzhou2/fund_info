@extends('layouts.public')

@section('bodyattr')class=""@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.svgmap.svgmap_list')}}">地图列表</a></li>
                        <li><a href="{{route('admin.svgmap.edit','add')}}">添加地图</a></li>
                    </ul>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <!--<a href="javascript:edit_map('add')"><input type="button" class="btn-primary" value="添加地图"/></a>-->
                    <table class="table table-striped table-new table-hover dataTables-example dataTable">
                        <thead>
                        <tr role="row">
                            <th>地图ID</th>
                            <th>地图名称</th>
                            <th>编辑时间</th>
                            <th>宽</th>
                            <th>高</th>
                            <th>所在楼层</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        @foreach($map_list as $map)
                            <tr class="gradeA">
                                <td>{{$map->id}}</td>
                                <td>{{$map->map_name}}</td>
                                <td>{{$map->updated_at}}</td>
                                <td>{{$map->width}}</td>
                                <td>{{$map->height}}</td>
                                <td>{{$floor_arr[$map->floor_id]}}</td>
                                <td>
                                    <a href="javascript:view_map({{$map->id}});" title="查看"><i class="fa fa-eye"></i></a>
                                    <a href="{{route('admin.svgmap.edit',$map->id)}}" title="编辑"><i class="fa fa-edit"></i></a>
                                    <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.svgmap.delete', $map->id)}}" title="删除" msg="是否删除该地图？"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="row">
                        <div class="col-sm-12"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        function view_map(id) {
            var url = "{{route('admin.svgmap.view','')}}/" + id;
            layer.open({
                title: '<img src={{cdn("img/map_opr5.png")}}>' + '地图预览',
                type: 2,
                area: ['1000px', '580px'],
                fix: true, //固定
                maxmin: false,
                move: false,
                resize: false,
                zIndex: 1,
                content: url
            });
        }
    </script>
@endsection

