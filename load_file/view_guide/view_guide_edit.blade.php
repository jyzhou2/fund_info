@extends('layouts.public')
@section('head')
    <script src="{{cdn('js/plugins/jquery-ui.min.js')}}"></script>
@endsection
@section('bodyattr')class=""@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.viewguide.view_guide_list')}}">实景导览列表</a></li>
                        <li class="active"><a href="{{route('admin.viewguide.view_guide_edit',[$id])}}">@if($id=='add')添加实景导览@else编辑实景导览 @endif </a></li>
                        <li><a href="{{route('admin.viewguide.resource_zip')}}">资源打包</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="layui-form form-horizontal ajaxForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">展品关联选择</label>
                            <div class="col-sm-4">
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="exhibit_id" lay-verify="required" lay-search="">
                                            <option value="">直接选择或搜索选择</option>
                                            @foreach($list_info as $k=>$g)
                                                <option value="{{$g['id']}}" @if($info['exhibit_id']==$g['id']) selected @endif>{{$g['exhibit_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">实景导览图片上传( 多图 *)</label>
                            <div class="col-sm-4">
                                <div class="webuploader-pick" onclick="upload_resource('实景导览图片上传','FT_VIEWGUIDE','sortable-imgs',1,'imgs',1);"
                                     style=" float: left; display: inline-block; width: auto;">点击上传图片
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-4" style="overflow: auto;width: 80%;">
                                <div id="imgs">
                                    @if(!empty($info['img'])&&is_array($info['img']))
                                        <ul id="sortable-imgs" style="list-style-type: none; margin: 0; padding: 0; width: 60%;">
                                            @foreach($info['img'] as $kk=>$gg)
                                                <div class="img-div">
                                                    <img src="{{get_file_url($gg)}}">
                                                    <span onclick="del_img($(this))">×</span>
                                                    <input type="hidden" name="imgs[]" value="{{$gg}}">
                                                </div>
                                            @endforeach
                                        </ul>
                                        <script>
                                            $(function () {
                                                $("#sortable-imgs").sortable();
                                            });
                                        </script>
                                    @else
                                        <ul id="sortable-imgs" style="list-style-type: none; margin: 0; padding: 0; width: 60%;">
                                        </ul>
                                        <script>
                                            $(function () {
                                                $("#sortable-imgs").sortable();
                                            });
                                        </script>
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


@endsection
@section('script')
    <script>
        layui.use(['form'], function () {
            var form = layui.form
        });
    </script>
@endsection
