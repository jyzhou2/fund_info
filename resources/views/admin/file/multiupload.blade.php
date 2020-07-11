@extends('layouts.public')

@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
    <style type="text/css">
        .progress {
            margin-bottom: 0px;
            height: 14px;
        }
    </style>
@endsection

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.file.file')}}">文件列表</a></li>
                        <li><a href="{{route('admin.file.file.upload')}}">上传文件</a></li>
                        <li class="active"><a href="{{route('admin.file.file.multiupload')}}">上传大文件</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal ajaxForm">
                            <input type="hidden" name="file_id" id="file_id" value=""/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-8">
                                    <div id="picker">选择文件</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">文件名</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="f_name"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">大小</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="f_size"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">类型</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="f_type"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">MD5</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="f_md5"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static" id="f_status"></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-8">
                                    <button id="ctlBtn" class="btn btn-success" style="display: none;">开始上传</button>
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
    <script type="text/javascript">
        jQuery(function ($) {
            var $btn = $('#ctlBtn'), state = 'pending';
            var md5Array = [];
            var partMd5Array = [];

            WebUploader.Uploader.register({
                'before-send': 'checkChunk'
            }, {
                checkChunk: function (block) {
                    var file = block.file;
                    var deferred = $.Deferred();

                    if (block.chunks > 1) {
                        uploader.md5File(block.blob).then(function (blobmd5) {
                            md5Array[file.id + '_' + block.chunk] = blobmd5;
                            if ($.inArray(blobmd5, partMd5Array) > -1) {
                                deferred.reject();
                            } else {
                                deferred.resolve();
                            }
                        });
                    } else {
                        deferred.resolve();
                    }
                    return deferred.promise();
                }
            });

            var uploader = WebUploader.create({
                swf: '/js/plugins/webuploader/Uploader.swf',
                server: '{{route('admin.file.file.multiupload')}}',
                pick: '#picker',
                compress: false,
                chunked: true,
                chunkSize: 1024 * 1024,
                threads: 2,
                formData: {
                    '_token': '{{csrf_token()}}'
                }
            });

            uploader.on('fileQueued', function (file) {
                file.setStatus('cancelled');
                $('#file_id').val(file.id);
                $('#f_name').html(file.name);
                $('#f_size').html(WebUploader.formatSize(file.size));
                $('#f_type').html(file.type);
                $('#f_md5').html('计算MD5中...<div class="progress progress-striped active"><div style="width: 0%" role="progressbar" class="progress-bar progress-bar-success"></div></div>');
                $('#f_status').html('<span class="state">等待上传...</span>');

                uploader.md5File(file).progress(function (percentage) {
                    $('#f_md5 .progress-bar').css('width', percentage.toFixed(2) * 100 + '%');
                }).then(function (filemd5) {
                    $('#f_md5').html(filemd5);
                    md5Array[file.id] = filemd5;
                    $.ajax({
                        url: '{{route('admin.file.file.checkmfile')}}',
                        type: 'get',
                        data: 'name=' + encodeURIComponent(file.name) + '&md5=' + filemd5,
                        async: false,
                        success: function (data) {
                            partMd5Array = data;
                        }
                    });
                    file.setStatus('queued');
                    $btn.show();
                });
            });

            uploader.on('uploadProgress', function (file, percentage) {
                var $status_div = $('#f_status'),
                    $percent = $status_div.find('.progress .progress-bar');

                if (!$percent.length) {
                    $percent = $('<div class="progress progress-striped active">' +
                        '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                        '</div>' +
                        '</div>').appendTo($status_div).find('.progress-bar');
                }

                $status_div.find('.state').text('上传中');
                $percent.css('width', percentage * 100 + '%');
            });

            uploader.on('uploadBeforeSend', function (block, data, headers) {
                data.chunkmd5 = md5Array[block.file.id + '_' + block.chunk];
                data.md5 = md5Array[block.file.id];
            });

            uploader.on('uploadSuccess', function (file) {
                $('#f_status').find('.state').text('已上传');
            });

            uploader.on('uploadError', function (file) {
                $('#f_status').find('.state').text('上传出错');
            });

            uploader.on('uploadComplete', function (file) {
                $('#f_status').find('.progress').fadeOut();
            });

            uploader.on('all', function (type) {
                if (type === 'startUpload') {
                    state = 'uploading';
                    $btn.prop('class', 'btn btn-danger').text('暂停上传');
                } else if (type === 'stopUpload') {
                    state = 'paused';
                    $btn.prop('class', 'btn btn-primary').text('恢复上传');
                } else if (type === 'uploadFinished') {
                    state = 'done';
                    $btn.prop('class', 'btn btn-white').text('上传成功').prop('disabled', true);
                }
            });

            $btn.on('click', function () {
                if (state === 'uploading') {
                    uploader.stop(true);
                } else {
                    uploader.upload();
                }
            });
        });
    </script>
@endsection