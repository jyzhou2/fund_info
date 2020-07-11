@extends('layouts.public')
@section('head')
    <style>
        .exhibit_list {
            margin-top: -13px;
        }

        .exhibit_list h1 {
            font-size: 16px;
            font-weight: bold;
            padding: 20px 0 10px;
            clear: both;
        }

        .exhibit_list .exhibit_box {
            color: #959595;
            float: left;
            width: 150px;
            height: 90px;
            line-height: 22px;
            padding: 10px;
            margin-right: 15px;
            margin-bottom: 15px;
            border: 2px dashed #e5e6e7;
            overflow: hidden;
            cursor: pointer;
        }

        .exhibit_list .exhibit_box input {
            display: none;
        }

        .exhibit_list .checked {
            color: #676a6c;
            border: 2px dashed #44b6eb;
        }

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
                        <li><a href="{{route('admin.data.autonum')}}">蓝牙关联列表</a></li>
                        <li class="active"><a href="{{route('admin.data.autonum.edit',$id)}}">@if($id=='add')添加关联@else编辑关联@endif </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="form-horizontal ajaxForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">蓝牙编号</label>
                            <div class="col-sm-4">
                                <input type="number" name="autonum" value="{{$info['autonum'] or ''}}" class="form-control" maxlength="10" required/>
                            </div>
                        </div>

                        @if(Auth::id()==1)
                            <div class="form-group">
                                <label class="col-sm-2 control-label">安卓门限</label>
                                <div class="col-sm-4">
                                    <input type="number" name="mx_and" value="{{$info['mx_and'] or '-68'}}" class="form-control" min="-999" max="9999" required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">导览机门限</label>
                                <div class="col-sm-4">
                                    <input type="number" name="mx_dlj" value="{{$info['mx_dlj'] or '-68'}}" class="form-control" min="-999" max="9999" required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">ios门限</label>
                                <div class="col-sm-4">
                                    <input type="text" name="mx_ios" value="{{$info['mx_ios'] or '10'}}" class="form-control" min="-999" max="9999" required/>
                                </div>
                            </div>
                        @endif
                        @if(config('exhibit_config.is_set_autonum_x_y'))
                            <div class="form-group">
                                <label class="col-sm-2 control-label">分布位置(*)：</label>
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
                                <label class="col-sm-2 control-label">点位标注(*)：</label>
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
                        @endif

                        <div class="layui-tab">
                            <ul class="layui-tab-title">
                                @foreach($exhibit_list as $k=>$g)
                                    <li @if($k==0) class="layui-this" @endif>{{$g['exhibition_name']}}({{$g['check_num']}})</li>
                                @endforeach
                            </ul>
                            <div class="layui-tab-content">
                                @foreach($exhibit_list as $k=>$g)
                                    <div class="layui-tab-item @if($k==0) layui-show @endif">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">展品选择</label>
                                            <div class="col-sm-8 exhibit_list">
                                                <h1>{{$g['exhibition_name']}}</h1>
                                                @foreach($g['exhibit_list'] as $kk=>$gg)
                                                    <div class="exhibit_box">
                                                        <input type="checkbox" name="auto_exhibit_id[]" value="{{$gg['exhibit_id']}}" @if($gg['is_check']==1)checked @endif />{{$gg['exhibiti_name']}}
                                                        <input type="hidden" name="exhibiti_name[{{$gg['exhibit_id']}}]" value="{{$gg['exhibiti_name']}}"/>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

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
    @if(config('exhibit_config.is_set_autonum_x_y'))
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
    @endif
    <script>
        layui.use('element', function () {
            var $ = layui.jquery; //Tab的切换功能，切换事件监听等，需要依赖element模块
        });

        $(".exhibit_box").each(function () {
            if ($(this).find("input").prop('checked')) {
                $(this).addClass("checked");
            }
        }).click(function () {
            var check = $(this).find("input");
            check.prop('checked', !check.prop('checked'));
            $(this).toggleClass("checked");
        });
    </script>

@endsection
