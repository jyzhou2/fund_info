<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Set render engine for 360 browser -->
    <meta name="renderer" content="webkit">

    <title>@yield('title')</title>

    <link rel="stylesheet" href="{{cdn('css/bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{cdn('css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{cdn('css/animate.min.css')}}">
    <link rel="stylesheet" href="{{cdn('css/style.min.css')}}">
    <link rel="stylesheet" href="{{cdn('css/common.css'.$_static_update)}}">
    <link rel="stylesheet" href="{{cdn('js/plugins/layui/css/layui.css')}}">
    <link rel="stylesheet" href="{{cdn('css/iconfont.css')}}">
    <link rel="stylesheet" href="{{cdn('css/add/base.css')}}">
    <!--[if lte IE 9]>
    <style type="text/css">
        input, textarea {
            color: #000;
        }

        .placeholder {
            color: #aaa;
        }
    </style>
    <![endif]-->

    <script src="{{cdn('js/jquery-1.12.4.min.js')}}"></script>
    <script src="{{cdn('js/bootstrap.min.js')}}"></script>
    <script src="{{cdn('js/plugins/layer/layer.js')}}"></script>
    <script src="{{cdn('js/plugins/layui/layui.js')}}"></script>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script type="text/javascript">
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });

        var UPLOAD_URL = '{{route('admin.upload')}}';
        var UPLOAD_RESOURCE_URL = '{{route('admin.file.file.upload_resource_html',['','','',''])}}';
        var CROPPER_RESOURCE_URL = '{{route('admin.file.file.cropper_upload',['','','',''])}}';
    </script>
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">

    <script src="{{cdn('js/plugins/upload_resource/upload_resource.js')}}"></script>
    @yield('head')
</head>
<body @yield('bodyattr')>

@yield('body')

<!--[if lte IE 9]>
<script src="{{cdn('js/jquery.placeholder.min.js')}}"></script>
<script type="text/javascript">jQuery('input, textarea').placeholder();</script>
<![endif]-->
<script src="{{cdn('js/public.js'.$_static_update)}}"></script>
@yield('script')
</body>
</html>
