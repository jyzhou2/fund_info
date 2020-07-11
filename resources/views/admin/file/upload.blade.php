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
                        <li><a href="{{route('admin.file.file')}}">文件列表</a></li>
                        <li class="active"><a href="{{route('admin.file.file.upload')}}">上传文件</a></li>
                        <li><a href="{{route('admin.file.file.multiupload')}}">上传大文件</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal ajaxForm">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">单张图片上传</label>
                                <div class="col-sm-10" id="file_1_box">
                                    <div id="file_1_picker">选择图片</div>
                                </div>
                                <input type="hidden" name="file_1" id="file_1" value=""/>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">多张图片上传</label>
                                <div class="col-sm-10" id="file_2_box">
                                    <div id="file_2_picker">选择图片</div>
                                </div>
                                <input type="hidden" name="file_2" id="file_2" value=""/>
                            </div>
                            <div class="form-group">
                                <div class="text-center">
                                    <button class="btn btn-primary" type="submit">保存</button>
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
                type_key: 'FT_COMMON',
                pick: 'file_1_picker',
                boxid: 'file_1_box',
                file_path: 'file_1'
            });

            singleUpload({
                _token: '{{csrf_token()}}',
                type_key: 'FT_COMMON',
                pick: 'file_2_picker',
                boxid: 'file_2_box',
                file_path: 'file_2',
                multi: true,
                maximg: 3
            });
        });
    </script>
@endsection