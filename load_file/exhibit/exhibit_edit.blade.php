@extends('layouts.public')
@section('head')
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

        #sortable {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 80%;
        }
    </style>
    <script src="{{cdn('js/plugins/jquery-ui.min.js')}}"></script>
@endsection
@section('bodyattr')class=""@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.data.exhibit')}}">展品列表</a></li>
                        <li class="active"><a href="{{route('admin.data.exhibit.edit',$id)}}">@if($id=='add')添加展品@else编辑展品 @endif </a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="form-horizontal ajaxForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">展品编号(*)</label>
                            <div class="col-sm-4">
                                <input type="text" name="exhibit_num" value="{{$info['exhibit_num'] or ''}}" class="form-control" maxlength="10" required/>
                            </div>
                        </div>

                        @if(config('exhibit_config.exhibit.is_lb'))
                            <div class="form-group">
                                <label class="col-sm-2 control-label">是否首页轮播(*)</label>
                                <div class="col-sm-4">
                                    <div class="col-sm-4">
                                        <input type="radio" @if(empty($info)||(isset($info['is_lb'])&&$info['is_lb']==2)) checked @endif name="is_lb" value="2">不轮播
                                        <input type="radio" @if((isset($info['is_lb'])&&$info['is_lb']==1)) checked @endif name="is_lb" value="1">轮播
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="col-sm-2 control-label">是否在地图显示(*)</label>
                            <div class="col-sm-4">
                                <div class="col-sm-4">
                                    <input type="radio" @if(empty($info)||(isset($info['is_show_map'])&&$info['is_show_map']==1)) checked @endif name="is_show_map" value="1">显示
                                    <input type="radio" @if(isset($info['is_show_map'])&&$info['is_show_map']==2) checked @endif name="is_show_map" value="2">不显示
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">是否在列表显示(*)</label>
                            <div class="col-sm-4">
                                <div class="col-sm-4">
                                    <input type="radio" @if(empty($info)||(isset($info['is_show_list'])&&$info['is_show_list']==1)) checked @endif name="is_show_list" value="1">显示
                                    <input type="radio" @if(isset($info['is_show_list'])&&$info['is_show_list']==2) checked @endif name="is_show_list" value="2">不显示
                                </div>
                            </div>
                        </div>

                        @foreach(config('exhibit_config.exhibit.imgs') as $k=>$g)
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{$g['name']}}上传@if($g['required'])(@if($g['is_more']) 多图 @endif*)@endif</label>
                                <div class="col-sm-4">
                                    @if($g['is_more'])
                                        <div class="webuploader-pick" onclick="upload_resource('{{$g['name']}}上传','{{$g['upload_key']}}','sortable-{{$g['key']}}',1,'{{$g['key']}}',2);"
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
                                        @if(!empty($info['exhibit_img'])&&isset($info['exhibit_img'][$g['key']])&&!empty($info['exhibit_img'][$g['key']]))
                                            @if($g['is_more'])
                                                <ul id="sortable-{{$g['key']}}" style="list-style-type: none; margin: 0; padding: 0; width: 60%;">
                                                    @foreach($info['exhibit_img'][$g['key']] as $kk=>$gg)
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
                                                    <img src="{{get_file_url($info['exhibit_img'][$g['key']])}}">
                                                    <span onclick="del_img($(this))">×</span>
                                                    <input type="hidden" name="{{$g['key']}}" value="{{$info['exhibit_img'][$g['key']]}}">
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
                            <label class="col-sm-2 control-label">所属展厅(*)：</label>
                            <div class="col-sm-4">
                                <select name="exhibition_id" class="form-control" required style=" width: 240px;">
                                    <option value="">请选择展厅</option>
                                    @foreach($exhibition_info as $k=>$v)
                                        <option value="{{$v->id}}" @if(isset($info['exhibition_id'])&&$info['exhibition_id']==$v->id) selected @endif >{{$v->exhibition_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


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

                        {{--<div class="form-group">
                            <label class="col-sm-2 control-label">排序编号</label>
                            <div class="col-sm-4">
                                <input type="number" min="0" max="9999" name="order_id" value="{{$info['order_id'] or '100'}}" class="form-control" required />
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
                                            <label class="col-sm-2 control-label">展品名称({{$g['name']}}@if($k==1) * @endif )</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="exhibit_name_{{$k}}" value="{{$info['language'][$k]['exhibit_name'] or ''}}" class="form-control" @if($k==1) maxlength="50"
                                                       @else maxlength="500" @endif />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">展品音频({{$g['name']}})</label>
                                            <div class="col-sm-4">
                                                <input type="text" name="exhibit_audio_{{$k}}" value="{{$info['language'][$k]['audio'] or ''}}" readonly id="exhibit_audio_{{$k}}" class="form-control"
                                                       style="width:400px;float: left"/>
                                                <button type="button" onclick="upload_resource('音频上传','FT_EXHIBIT_MP3','exhibit_audio_{{$k}}',2);" class="btn btn-white">音频上传</button>
                                            </div>
                                        </div>

                                        @foreach(config('exhibit_config.exhibit.content_arr') as $kkk=>$ggg)
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">{{$ggg['name']}}({{$g['name']}})</label>
                                                <div class="col-sm-4">
                                                    <script type="text/plain" id="{{$k}}_{{$ggg['key']}}" name="{{$ggg['key']}}_{{$k}}">{!! $info['language'][$k][$ggg['key']] or '' !!}</script>
                                                </div>
                                            </div>

                                        @endforeach

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
                @foreach(config('exhibit_config.exhibit.content_arr') as $kkk=>$ggg)
            editor{{$ggg['key']}}_{{$k}}= new baidu.editor.ui.Editor({
            pasteplain: true,
            initialFrameWidth: 950,
            initialFrameHeight: 300,
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
        editor{{$ggg['key']}}_{{$k}}.render('{{$k}}_{{$ggg['key']}}');
        editor{{$ggg['key']}}_{{$k}}.ready(function () {
            editor{{$ggg['key']}}_{{$k}}.execCommand('serverparam', {
                '_token': '{{csrf_token()}}',
                'filetype': 'FT_EXHIBIT_ONE',
                'itemid': '{{$article->article_id or 0}}'
            });
        });
        @endforeach
        @endforeach

    </script>
@endsection
