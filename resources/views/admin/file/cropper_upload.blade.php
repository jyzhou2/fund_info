@extends('layouts.public')

@section('head')
    <link rel="stylesheet" type="text/css" href="{{cdn('js/plugins/cropper_upload/cropper.css')}}"/>
@endsection

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div id="wrapper">
        <div id="container">
            <!--背景图片显示区域-->
            <div onclick="jsvascript:$('#inputImage').click();" style="height:70%;">
                <img title="点击更换背景图片" class="logo left" style="top:12%;left:42%;width:100%;height:100%" src="">
            </div>
            <input style="display: none;" id="inputImage" name="file" type="file" accept="image/*">
            点击上面背景图片更换图片
            <!--图片裁剪以及智能识别区域-->
            <div id="showEdit" class="containerImage Hide cliper_wrapper">
                <img id="EditImg" src="">
                <div class="footer-btn" style="height: 55px;">
                    <button type="button" style="width: 130px;font-size: 20px;height: 40px;margin-top: 8px;" class="ReselectPhoto" onclick="jsvascript:$('#inputImage').click();">重选照片</button>
                    <button id="cut" type="button" style="width: 100px;font-size: 20px;height: 40px;margin-top: 8px;background-color: #3A9E9E;border: 1px solid #3A9E9E;" class="Cut">剪切</button>
                    <button id="submit" type="button" style="width: 100px;font-size: 20px;height: 40px;margin-top: 8px;" class="Cut">提交</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var html_info='';
        /*
        * 整体上传成功
        * file_id    文件id
        * num        文件允许数量
        * type       类型1图片2资源
        * */
        function upload_end(file_id){
            var index = parent.layer.getFrameIndex(window.name);
            //单图片直接覆盖
            parent.$('#'+file_id).html(html_info);
            layer.msg('上传成功', {icon: 6, scrollbar: false, time: 1500, shade: [0.3, '#393D49']});
            setTimeout(function(){
                parent.layer.close(index);
            },1500);
        }

        /*
        * 单文件上传成功
        * file_path  文件存储路径
        * num        文件允许数量
        * type       类型1图片2资源
        * */
        function upload_success(file_path,file_id,post_name){
            html_info='<div class="img-div"><img src="'+file_path+'" /><span onclick=del_img($(this))>×</span><input type="hidden" name="'+post_name+'" value="'+file_path+'" /></div>';
            upload_end(file_id)
        }

    </script>
    <script src="{{cdn('js/plugins/cropper_upload/cropper.js')}}"></script>
    <script>
        $(function () {
            'use strict';//表示强规则
            var Cropper = window.Cropper;
            var console = window.console || { log: function () {} };
            var URL = window.URL || window.webkitURL;
            var $image = $('#EditImg');
            //获取图片截取的位置
            var screenWidth = $(window).width();
            var screenHeight =  $(window).height();
            var $dataX = $('#dataX');
            var $dataY = $('#dataY');
            var $dataHeight = $('#dataHeight');
            var $dataWidth = $('#dataWidth');
            var $dataRotate = $('#dataRotate');
            var $dataScaleX = $('#dataScaleX');
            var $dataScaleY = $('#dataScaleY');
            var cropper_width={{$width}};
            var cropper_height={{$height}};
            var options = {
                containerHeight :  screenWidth,
                containerWidth : screenHeight,
                guides :true,//裁剪框虚线 默认true有
                aspectRatio: cropper_width / cropper_height, //裁剪框比例1:1
                responsive : true,// 是否在窗口尺寸改变的时候重置cropper
                background : true,// 容器是否显示网格背景
                zoomable : true,//是否允许放大缩小图片
                movable : true,//是否允许移动剪裁框
                resizable : true,//是否允许改变剪裁框的大小
                cropBoxMovable :true,//是否允许拖动裁剪框
                cropBoxResizable :true,//是否允许拖动 改变裁剪框大小
                crop: function (e) {
                    $dataX.val(Math.round(e.x));
                    $dataY.val(Math.round(e.y));
                    $dataHeight.val(Math.round(e.height));
                    $dataWidth.val(Math.round(e.width));
                    $dataRotate.val(e.rotate);
                    $dataScaleX.val(e.scaleX);
                    $dataScaleY.val(e.scaleY);
                }
            };

            $('#EditImg').cropper(options);

            // Options
            var originalImageURL = $image.attr('src');
            var uploadedImageURL;
            var $inputImage = $('#inputImage');
            URL = window.URL || window.webkitURL;
            var blobURL;
            if (URL) {
                $inputImage.change(function () {
                    var files = this.files,
                        file;
                    if (files && files.length) {
                        file = files[0];
                        if (/^image\/\w+$/.test(file.type)) {
                            blobURL = URL.createObjectURL(file);
                            $image.one('built.cropper', function () {
                                URL.revokeObjectURL(blobURL); // Revoke when load complete
                            }).cropper('reset', true).cropper('replace', blobURL);
                            //$inputImage.val('');
                            $("#showEdit").removeClass('Hide');
                        } else {
                            //alert('Please choose an image file.');
                        }
                    }
                });
            } else {
                $inputImage.parent().remove();
            }
            //裁剪图片
            $("#cut").on("click", function () {
                var dataURL = $image.cropper("getCroppedCanvas");
                var imgurl = dataURL.toDataURL("image/*", 0.5);
                $image.cropper('destroy').attr('src', imgurl).cropper(options);
            });
            //提交图片
            $("#submit").on("click", function () {
                var accessory = $('#inputImage').val();
                if (typeof accessory == "null"){
                    alert("is null");
                    return ;
                }
                var accessoryName = accessory.substring(accessory.lastIndexOf("\\")+1,accessory.length);//截取原文件名
                var dataURL = $image.cropper("getCroppedCanvas");//拿到剪裁后的数据
                var data = dataURL.toDataURL("image/*", 0.5);//转成base64
                if (typeof data == "null"){
                    alert("is null");
                    return ;
                }
                $.ajax({
                    url: "{{route('admin.cropper_base64_upload')}}",
                    dataType:'json',
                    type: "POST",
                    data: {
                        fileName : accessoryName,
                        imgBase64 : data.toString()
                    },
                    timeout : 10000, //超时时间设置，单位毫秒
                    async: true,
                    success: function (result) {
                        upload_success(result.data.file_path,'{{$file_id}}','{{$post_name}}');
                    },
                    error: function (returndata) {
                        var index = parent.layer.getFrameIndex(window.name);
                        parent.layer.msg('图片上传失败，请重试', {icon: 5, scrollbar: false, time: 1500, shade: [0.3, '#393D49']});
                        parent.layer.close(index);
                    }
                });
            });
        });
    </script>
@endsection