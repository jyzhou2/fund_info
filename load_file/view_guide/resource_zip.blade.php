@extends('layouts.public')
@section('head')

@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.viewguide.view_guide_list')}}">实景导览列表</a></li>
                        <li><a href="{{route('admin.viewguide.view_guide_edit',['add'])}}">添加实景导览</a></li>
                        <li class="active"><a href="{{route('admin.viewguide.resource_zip')}}">资源打包</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen">
                        @if($is_need_update)
                            <button type="button" class="btn btn-primary" id="tongji" onclick="tongji_start();">资源打包更新</button>
                        @else
                            <div class="form-group" id="no_update">
                                资源没有变化，无需更新
                            </div>
                            <button type="button" class="btn btn-primary" id="download_zip" onclick="download_resource_zip();">资源打包下载</button>
                        @endif
                        <button type="button" class="btn btn-primary" id="update_zip" style="display: none" onclick="update_totalzy();">资源打包更新</button>

                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <table class="table table-striped table-new table-hover infoTables-example infoTable">
                        <thead>
                        <tr>
                            <th>地图名称</th>
                            <th>图片数量</th>
                            <th>打包进度</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($map_info as $k=>$g)
                            <tr>
                                <td>{{$g['map_name']}}</td>
                                <td>图片数量<span id="img_num{{$k}}">0</span>张,约<span id="img_size{{$k}}">0 Bytes</span></td>
                                <td>
                                    <div class="layui-progress layui-progress-big">
                                        <div class="layui-progress-bar layui-bg-green" id="update_progress{{$k}}" style="width: 0%;"><span id="update_progress_text{{$k}}" class="layui-progress-text">0%</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>总计</td>
                            <td>图片数量<span id="total_num">0</span>张，约<span id="total_size">0 Bytes</span></td>
                            <td>
                                <div class="layui-progress layui-progress-big">
                                    <div class="layui-progress-bar layui-bg-green" id="update_progress_total" style="width: 0%;"><span id="update_progress_text_total"
                                                                                                                                       class="layui-progress-text">0%</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function download_resource_zip() {
            var down_url = "{{route('admin.viewguide.down_file')}}";
            location.href = down_url;
        }

        function tongji_start() {
            layer.confirm('您确定要资源打包更新吗？此操作耗时较长请耐心等待！', {
                btn: ['确定', '取消'], title: '资源更新统计', move: false, cancel: function () {
                }
            }, function () {
                $("#tongji").hide();
                layer.msg('资源文件详情统计中...', {icon: 16, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                upload_file(0, 0, 0);
            }, function () {

            });
        }


        function upload_file(id, total_num, total_size) {
            $.ajax({
                cache: true,
                type: "POST",
                url: '{{route('admin.viewguide.resource_zip')}}',
                data: {arr_id: id, total_num: total_num, total_size: total_size},// 你的formid
                async: false,
                error: function (request) {
                    setTimeout(function () {
                            upload_file(id, total_num, total_size);
                        },
                        1000);//加载出错暂停一秒后再次请求
                },
                success: function (data) {

                    $('#img_num' + id).text(data.num);
                    $('#img_size' + id).text(data.size);
                    $('#total_num').text(data.total_num);
                    $('#total_size').text(data.total_size_info);
                    if (data.type == 'end') {
                        layer.msg('资源打包中，请稍等...', {icon: 16, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                        update_totalzy(0, 0, 0);
                    }
                    else {
                        setTimeout(function () {
                            upload_file(data.id, data.total_num, data.total_size);
                        }, 100);
                    }
                }
            });
        }

        function update_totalzy(id, img_num, total_num) {
            $.ajax({
                cache: true,
                type: "POST",
                url: '{{route('admin.viewguide.update_zip')}}',
                data: {arr_id: id, arr_img_mun: img_num, arr_total_num: total_num},// 你的formid
                async: false,
                error: function (request) {
                    setTimeout(function () {
                            update_totalzy(id);
                        },
                        1000);//加载出错暂停一秒后再次请求
                },
                success: function (data) {
                    if (data.type == 'error') {
                        layer.closeAll('dialog');
                        layer.alert('未知错误，请刷新页面后重新打包', {icon: 5, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                        return false;
                    }
                    else if (data.type == 'end') {
                        layer.alert('资源打包完毕', {icon: 6, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                    }
                    else {
                        //单地图文件打包进度
                        var progress = (parseInt(data.img_mun) / parseInt($('#img_num' + id).text()) * 100).toFixed(2);
                        if (progress > 100) {
                            progress = 100 + '%'
                        }
                        else {
                            progress = progress + '%'
                        }
                        $('#update_progress_text' + id).text(progress);
                        $("#update_progress" + id).attr("style", 'width:' + progress);
                        //总体进度
                        var progress_total = (parseInt(data.total_num) / parseInt($('#total_num').text()) * 100).toFixed(2);
                        if (progress_total > 100) {
                            progress_total = 100 + '%'
                        }
                        else {
                            progress_total = progress_total + '%'
                        }
                        $('#update_progress_text_total').text(progress_total);
                        $("#update_progress_total").attr("style", 'width:' + progress_total);

                        setTimeout(function () {
                            update_totalzy(data.id, data.img_mun, data.total_num);
                        }, 100);
                    }
                }
            });
        }

        function zip_end() {
            $.post('{{route('admin.viewguide.end_zip')}}', {type: 1}, function (data) {
                layer.closeAll('dialog');
                if (data.status) {
                    layer.msg(data.msg, {icon: 6, scrollbar: false, time: 1500, shade: [0.3, '#393D49']});
                    setTimeout(function () {
                        location.href = "{{route('admin.viewguide.resource_zip')}}";
                    }, 1500)
                }
                else {
                    $("#update_table").hide();
                    $('#update_num1').val(0);
                    $('#update_num2').val(0);
                    $('#update_progress_text1').text(0);
                    $('#update_progress_text2').text(0);
                    $('#update_progress1').attr("style", 'width:100%');
                    $('#update_progress2').attr("style", 'width:100%');
                    layer.alert(data.msg, {icon: 5});
                }
            })
        }
    </script>
@endsection

