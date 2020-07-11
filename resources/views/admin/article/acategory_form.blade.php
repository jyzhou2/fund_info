@extends('layouts.public')

@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
@endsection

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="{{route('admin.article.acategory')}}">分类列表</a></li>
                        <li @if (!isset($category))class="active"@endif><a href="{{route('admin.article.acategory.add')}}">添加分类</a></li>
                        @if (isset($category))
                            <li class="active"><a href="#">编辑分类</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form action="{{route('admin.article.acategory.save')}}" method="post" class="form-horizontal ajaxForm">
                            <input type="hidden" name="cate_id" value="{{$category->cate_id or 0}}"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">分类名称</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="cate_name" value="{{$category->cate_name or ''}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">上级分类</label>
                                <div class="col-sm-4">
                                    <select name="parent_id" class="form-control">
                                        <option value="0">一级分类</option>
                                        @foreach ($cates as $cate)
                                            @if(!isset($category)||(isset($category)&&$category->cate_id!=$cate['cate_id']))
                                                <option value="{{$cate['cate_id']}}"
                                                        @if ((isset($category->parent_id) ? $category->parent_id : '') == $cate['cate_id'] || request('cate_id') == $cate['cate_id']) selected="selected" @endif>
                                                    &nbsp;&nbsp;
                                                    @if($cate['layer'] == 1)
                                                    @elseif($cate['layer'] == 2)
                                                        &nbsp;&nbsp;
                                                    @elseif($cate['layer'] == 3)
                                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                                    @endif
                                                    {{$cate['cate_name']}}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">排序</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control" name="sort_order" value="{{$category->sort_order or '255'}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">图标</label>
                                <div class="col-sm-10" id="icon_box">
                                    <div id="icon_picker">选择图片</div>
                                    @if(isset($category) && $category->icon != '')
                                        <div class="img-div">
                                            <img src="{{get_file_url($category->icon)}}"/>
                                            <span class="cancel">×</span>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="icon" id="icon" value="{{$category->icon or ''}}"/>
                                <input type="hidden" name="icon_file_id" id="icon_file_id" value=""/>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10">
                                    <div class="input-group m-t-xs-2">
                                        <input type="radio" name="is_show" value="1"
                                               @if ((isset($category->is_show) ? $category->is_show : '') != '0') checked="checked"@endif/>显示
                                        <input type="radio" name="is_show" value="0"
                                               @if ((isset($category->is_show) ? $category->is_show : '') == '0') checked="checked"@endif/>不显示
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <button class="btn btn-white" type="button" id="backBtn">返回</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{cdn('js/plugins/webuploader/webuploader.nolog.min.js')}}"></script>
    <script src="{{cdn('js/plugins/webuploader/webuploader_public.js')}}"></script>
    <script type="text/javascript">
        jQuery(function ($) {
            singleUpload({
                _token: '{{csrf_token()}}',
                type_key: 'FT_ARTICLE_CATE',
                item_id: '{{$category->cate_id or 0}}',
                pick: 'icon_picker',
                boxid: 'icon_box',
                file_path: 'icon',
                file_id: 'icon_file_id'
            });
            $('#icon_box').find('.img-div>span').click(function () {
                sUploadDel($(this), 'icon');
            });
        });
    </script>
@endsection
