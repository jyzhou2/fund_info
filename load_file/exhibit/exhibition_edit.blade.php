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
                        <li><a href="{{route('admin.data.exhibition')}}">展厅列表</a></li>
                        <li class="active"><a href="{{route('admin.data.exhibition.edit',$id)}}">@if($id=='add')添加展厅@else编辑展厅 @endif </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="form-horizontal ajaxForm">

                        @foreach(config('exhibit_config.exhibition.imgs') as $k=>$g)
                            <div class="form-group">
                                <div class="col-sm-10">
                                    <label class="col-sm-2 control-label" style=" width: 20.666667%;">{{$g['name']}}上传@if($g['required'])(@if($g['is_more']) 多图 @endif*)@endif</label>
                                    @if($g['is_more'])
                                        <div class="webuploader-pick" onclick="upload_resource('{{$g['name']}}上传','{{$g['upload_key']}}','sortable-{{$g['key']}}',1,'{{$g['key']}}',1);"
                                             style=" float: left; display: inline-block; width: auto;">点击上传图片
                                        </div>
                                    @else
                                        <div class="webuploader-pick" onclick="upload_resource('{{$g['name']}}上传','{{$g['upload_key']}}','{{$g['key']}}',1,'{{$g['key']}}',1);"
                                             style=" float: left; display: inline-block; width: auto;">点击上传图片
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-4" style="overflow: auto;width: 80%;">
                                    <div id="{{$g['key']}}">
                                        @if(!empty($info['exhibition_img'])&&isset($info['exhibition_img'][$g['key']])&&!empty($info['exhibition_img'][$g['key']]))
                                            @if($g['is_more'])
                                                <ul id="sortable-{{$g['key']}}" style="list-style-type: none; margin: 0; padding: 0; width: 60%;">
                                                    @foreach($info['exhibition_img'][$g['key']] as $kk=>$gg)
                                                        <div class="img-div">
                                                            <img src="{{get_file_url($gg)}}">
                                                            <span onclick="del_img($(this))">×</span>
                                                            <input type="hidden" name="{{$g['key']}}[]" value="{{$gg}}">
                                                        </div>
                                                    @endforeach
                                                </ul>
                                                <script>
                                                    $(function () {
                                                        $("#sortable-{{$g['key']}}").sortable();
                                                    });
                                                </script>
                                            @else
                                                <div class="img-div">
                                                    <img src="{{get_file_url($info['exhibition_img'][$g['key']])}}">
                                                    <span onclick="del_img($(this))">×</span>
                                                    <input type="hidden" name="{{$g['key']}}" value="{{$info['exhibition_img'][$g['key']]}}">
                                                </div>
                                            @endif
                                        @else
                                            @if($g['is_more'])
                                                <ul id="sortable-{{$g['key']}}" style="list-style-type: none; margin: 0; padding: 0; width: 60%;">
                                                </ul>
                                                <script>
                                                    $(function () {
                                                        $("#sortable-{{$g['key']}}").sortable();
                                                    });
                                                </script>
                                            @endif
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group">
                            <label class="col-sm-2 control-label">所在楼层(*)</label>
                            <div class="col-sm-4">
                                <select name="floor_id" class="form-control" style="width: 100px">
                                    @foreach(config('floor') as $k=>$g)
                                        <option @if(isset($info['floor_id'])&&$info['floor_id']==$k) selected @endif value="{{$k}}">{{$g}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if(config('exhibit_config.exhibition.is_linzhan'))
                            <div class="form-group">
                                <label class="col-sm-2 control-label">展厅类别(*)</label>
                                <div class="col-sm-4">
                                    <input type="radio" @if(empty($info)||(isset($info['type'])&&$info['type']==1)) checked @endif name="type" value="1">主题展厅
                                    <input type="radio" @if(isset($info['type'])&&$info['type']==2) checked @endif name="type" value="2">临时展厅
                                </div>
                            </div>
                        @endif
                        @if(config('exhibit_config.exhibition.is_lb'))
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否首页轮播(*)</label>
                                <div class="col-sm-4">
                                    <input type="radio" @if(empty($info)||(isset($info['is_lb'])&&$info['is_lb']==2)) checked @endif name="is_lb" value="2">不轮播
                                    <input type="radio" @if(isset($info['is_lb'])&&$info['is_lb']==1) checked @endif name="is_lb" value="1">轮播
                                </div>
                            </div>
                        @endif
                        @if(config('exhibit_config.exhibition.is_show'))
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否在app列表显示(*)</label>
                                <div class="col-sm-4">
                                    <input type="radio" @if(empty($info)||isset($info['is_show_list'])&&$info['is_show_list']==1) checked @endif name="is_show_list" value="1">显示
                                    <input type="radio" @if(isset($info['is_show_list'])&&$info['is_show_list']==2) checked @endif name="is_show_list" value="2">不显示
                                </div>
                            </div>
                        @endif
                        @if(config('exhibit_config.exhibition.is_near_exhibition'))
                            @if(!empty($exhibition_list))
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">附近展厅</label>
                                    <div class="col-sm-4">
                                        @foreach($exhibition_list as $k=>$g)
                                            <input type="checkbox"
                                                   @if(!empty($info['near_exhibition'])&&(isset($info['near_exhibition'])&&is_array(json_decode($info['near_exhibition'],true))&&in_array($k,json_decode($info['near_exhibition'],true)))) checked
                                                   @endif name="near_exhibition[]" value="{{$k}}">{{$g}}
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                        {{--<div class="form-group">
                            <label class="col-sm-2 control-label">排序编号</label>
                            <div class="col-sm-4">
                                <input type="number" min="0" max="9999" name="order_id" value="{{$info['order_id'] or '10'}}" class="form-control" required />
                            </div>
                        </div>--}}
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
                                            <label class="col-sm-2 control-label">展厅名称({{$g['name']}}@if($k==1)* @endif)</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="exhibition_name_{{$k}}" value="{{$info['language'][$k]['exhibition_name'] or ''}}" class="form-control" maxlength="500">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">展厅地址({{$g['name']}})</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="exhibition_address_{{$k}}" value="{{$info['language'][$k]['exhibition_address'] or ''}}" class="form-control" maxlength="500">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">展厅简介({{$g['name']}})</label>
                                            <div class="col-sm-4">
                                                <script type="text/plain" id="{{$k}}_content" name="content_{{$k}}">{!! $info['language'][$k]['content'] or '' !!}</script>
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
    <script src="{{cdn('js/plugins/ueditor/ueditor.config.js')}}"></script>
    <script src="{{cdn('js/plugins/ueditor/ueditor.all.min.js')}}"></script>
    <script src="{{cdn('js/plugins/ueditor/lang/zh-cn/zh-cn.js')}}"></script>
    <script>
        layui.use('element', function () {
            var $ = layui.jquery; //Tab的切换功能，切换事件监听等，需要依赖element模块
        });
        //编辑器路径定义
        var initialWidth = $(window).width() > 1366 ? 950 : 705;
        var initialHeight = $(window).width() > 1366 ? 350 : 200;
        @foreach(config('language') as $k=>$g)
            editorcontent_{{$k}}= new baidu.editor.ui.Editor({
            pasteplain: true,
            initialFrameWidth: initialWidth,
            initialFrameHeight: initialHeight,
            wordCount: false,
            elementPathEnabled: false,
            autoHeightEnabled: false,
            initialStyle: 'img{width:20%;}',
            @if($k==10)iframeCssUrl: '{{cdn('js/plugins/ueditor/themes/vertical_mengyu.css')}}',
            @endif toolbars: [[
                'fullscreen', 'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'directionalityltr', 'directionalityrtl', 'indent', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                'simpleupload', 'emotion', '|',
                'horizontal', 'date', 'time', 'spechars', 'wordimage', '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts'
            ]]
        });
        editorcontent_{{$k}}.render('{{$k}}_content');
        editorcontent_{{$k}}.ready(function () {
            editorcontent_{{$k}}.execCommand('serverparam', {
                '_token': '{{csrf_token()}}',
                'filetype': 'FT_EXHIBIT_ONE',
                'itemid': '0'
            });
        });
        @endforeach
    </script>
@endsection
