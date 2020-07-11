@extends('layouts.public')
@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
    <script src="{{cdn('js/plugins/upload_resource/upload_resource.js')}}"></script>
    <style>
        #position {
            width: 500px;
            height: 245px;
            overflow: hidden;
            border-radius: 3px;
            border: 1px solid #dcdcdc;
            background-color: #fbf8f1;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            cursor: default;
        }
    </style>
@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.servicepoint.service_point_list')}}">服务设施列表</a></li>
                        <li class="active"><a href="{{route('admin.servicepoint.service_point_edit',[$id])}}">@if($id=='add')添加服务设施@else编辑服务设施 @endif </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="form-horizontal ajaxForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">服务设施名称</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="service_name" value="{{$info['service_name'] or ''}}" maxlength="20" required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-10">
                                <label class="col-sm-2 control-label" style=" width: 20.666667%;">设施图片上传</label>
                                <div class="webuploader-pick" onclick="upload_resource('设施图片上传','FT_SERVICE_POINT','img',1);" style=" float: left; display: inline-block; width: auto;">点击上传图片</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-4">
                                <div id="img">
                                    @if(!empty($info['img']))
                                        <div class="img-div">
                                            <img src="{{$info['img']}}">
                                            <span onclick="del_img($(this))">×</span>
                                            <input type="hidden" name="img" value="{{$info['img']}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">分布位置：</label>
                            <div class="col-sm-4">
                                <select id="select1" name="map_id" class="form-control" required style=" width: 240px;">
                                    <option value="">请选择地图</option>
                                    @foreach($map_info as $k=>$v)
                                        <option value="{{$v->id}}" title="{{$v->map_path}}" @if(isset($info['map_id'])&&$info['map_id']==$v->id) selected @endif >{{$v->map_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">点位标注：</label>
                            <div class="col-sm-4 mapshow">
                                <div name="position" id="position" class="input2" _echarts_instance_="1482218387246">
                                    <div style=" width: 500px; height: 245px; position: relative; overflow: hidden;">
                                        <div data-zr-dom-id="bg" class="zr-element" style=" width: 500px; height: 245px;position: absolute; left: 0px; top: 0px; user-select: none;"></div>
                                        <canvas width="500" height="245" data-zr-dom-id="0" class="zr-element"
                                                style=" width: 500px; height: 245px; position: absolute; left: 0px; top: 0px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></canvas>
                                        <canvas width="500" height="245" data-zr-dom-id="_zrender_hover_" class="zr-element"
                                                style=" width: 500px; height: 245px; position: absolute; left: 0px; top: 0px; user-select: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="x" id="x" value="{{$info['x'] or ''}}" class="input" placeholder="">
                        <input type="hidden" name="y" id="y" value="{{$info['y'] or ''}}" class="input" placeholder="">

                        <div class="form-group">
                            <div class="col-sm-6 col-md-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <button class="btn btn-white" type="button" onclick="window.history.back()">返回</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('script')
    <script src="{{cdn('js/plugins/svg_map/echarts_mfy.js')}}"></script>
    <script src="{{cdn('js/plugins/svg_map/custom_map_echarts.js')}}"></script>
    <script>
        $('#select1').change(function () {
            map_id = $('#select1').val();
            if (map_id) {
                show_map();
            }
        });

        //      function show_map(str='[]') {
        function show_map(str) {
            if (str == null) {
                str = '[]';
            }
            map_id = $('#select1').val();
            if (map_id) {
                //进行地图展示
                var path = $('#select1 option:selected').attr("title");
                map("{{cdn('img/dw.png')}}", "{{cdn('js/plugins/echarts')}}", path, 'position', str, '');
            }
        }

        @if(isset($info['x'])&&isset($info['y'])&&$info['x'] && $info['y'])
            str = "[{name:'aaa',geoCoord:[{{$info['x']}},{{$info['y']}}]}]";
        @else
            str = [];
        @endif
        show_map(str);
    </script>

@endsection
