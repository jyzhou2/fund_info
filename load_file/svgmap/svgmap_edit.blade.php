@extends('layouts.public')
@section('head')
@endsection
@section('bodyattr')class=""@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.svgmap.svgmap_list')}}">地图列表</a></li>
                        <li class="active"><a href="{{route('admin.svgmap.edit',$map['id'])}}">@if($map['id']=='add')添加地图@else编辑地图 @endif </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="form-horizontal ajaxForm">

                        @if($is_more_language==false)
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地图名称</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="map_name" value="{{$map['map_name'] or ''}}" maxlength="20" required/>
                                </div>
                            </div>
                        @else
                            <div class="layui-tab">
                                <ul class="layui-tab-title">
                                    @foreach(config('language') as $k=>$g)
                                        <li @if($k==1) class="layui-this" @endif>{{$g['name']}}</li>
                                    @endforeach
                                </ul>
                                <div class="layui-tab-content">
                                    @foreach(config('language') as $k=>$g)
                                        <div class="layui-tab-item @if($k==1) layui-show @endif">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">地图名称({{$g['name']}})</label>
                                                <div class="col-sm-4">
                                                    <input type="text" @if($k==1) name="map_name" @else name="map_name_{{$g['dir']}}"
                                                           @endif value="@if(!empty($map['map_name_json'])){{json_decode($map['map_name_json'],true)[$k]}}@endif" class="form-control"
                                                           @if($k==1) maxlength="20" @else maxlength="255" @endif />
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-sm-2 control-label">所在楼层</label>
                            <div class="col-sm-4">
                                <select name="floor_id" class="form-control" style="width: 100px">
                                    @foreach($floor_arr as $k=>$g)
                                        <option @if(isset($map['floor_id'])&&$map['floor_id']==$k) selected @endif value="{{$k}}">{{$g}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">宽</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="{{$map['width'] or ''}}" name="width" maxlength="5" required/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">高</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="{{$map['height'] or ''}}" name="height" maxlength="5" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">地图比例(px/m)</label>
                            <div class="col-sm-4">
                                <input type="number" min="0" max="999999.9999" step="0.0001" class="form-control" value="{{$map['map_size'] or 0}}" name="map_size" maxlength="11" required/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">实际夹角</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" value="{{$map['map_angle'] or 0}}" name="map_angle" maxlength="5" required/>
                            </div>
                            <span>手机朝向地图y轴正方向时与正北方向的夹角</span>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-10">
                                <label class="col-sm-2 control-label" style=" width: 20.666667%;">地图上传</label>
                                <div class="webuploader-pick" onclick="upload_resource('地图上传','FT_SVGMAP','map_path',1);" style=" float: left; display: inline-block; width: auto;">点击上传图片</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-4">
                                <div id="map_path">
                                    @if(!empty($map['map_path']))
                                        <div class="img-div">
                                            <img src="{{get_file_url($map['map_path'])}}">
                                            <span onclick="del_img($(this))">×</span>
                                            <input type="hidden" name="map_path" value="{{$map['map_path']}}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                            <div class="form-group">
                                <div class="col-sm-10">
                                    <label class="col-sm-2 control-label" style=" width: 20.666667%;">PNG地图上传</label>
                                    <div class="webuploader-pick" onclick="upload_resource('地图上传','FT_PNGMAP','png_map_path',1);" style=" float: left; display: inline-block; width: auto;">点击上传图片</div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-4">
                                    <div id="png_map_path">
                                        @if(!empty($map['png_map_path']))
                                            <div class="img-div">
                                                <img src="{{get_file_url($map['png_map_path'])}}">
                                                <span onclick="del_img($(this))">×</span>
                                                <input type="hidden" name="png_map_path" value="{{$map['png_map_path']}}">
                                            </div>
                                        @endif
                                    </div>
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

    @if($is_more_language==true)
        <script>
            layui.use('element', function () {
                var $ = layui.jquery; //Tab的切换功能，切换事件监听等，需要依赖element模块
            });
        </script>
    @endif
@endsection

