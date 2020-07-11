@extends('layouts.public')
@section('head')

@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.data.exhibit.resource_zip')}}">常展资源打包更新</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen">
                        @if($is_need_update)
                            <button type="button" class="btn btn-primary" id="tongji" onclick="tongji_start();">资源更新统计</button>
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
                            <th>文件类型</th>
                            <th>更新资源文件详情</th>
                            <th>完整资源文件详情</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>MP3音频文件</td>
                            <td>数量<span id="mp3_num1">0</span>个,约<span id="mp3_size1">0 Bytes</span></td>
                            <td>数量<span id="mp3_num2">0</span>个,约<span id="mp3_size2">0 Bytes</span></td>
                        </tr>
                        <tr>
                            <td>图片文件数量</td>
                            <td>数量<span id="image_num1">0</span>个,约<span id="image_size1">0 Bytes</span></td>
                            <td>数量<span id="image_num2">0</span>个,约<span id="image_size2">0 Bytes</span></td>
                        </tr>
                        <tr>
                            <td>html文件数量</td>
                            <td>数量<span id="html_num1">0</span>个,约<span id="html_size1">0 Bytes</span></td>
                            <td>数量<span id="html_num2">0</span>个,约<span id="html_size2">0 Bytes</span></td>
                        </tr>
                        <tr>
                            <td>文件夹数量</td>
                            <td>数量<span id="folder_num1">0</span>个</td>
                            <td>数量<span id="folder_num2">0</span>个</td>
                        </tr>
                        <tr>
                            <td>其他文件</td>
                            <td>数量<span id="other_num1">0</span>个,约<span id="other_size1">0 Bytes</span></td>
                            <td>数量<span id="other_num2">0</span>个,约<span id="other_size2">0 Bytes</span></td>
                        </tr>
                        <tr>
                            <td>总计</td>
                            <td>数量<span id="total_num1">0</span>个，约<span id="total_size1">0 Bytes</span></td>
                            <td>数量<span id="total_num2">0</span>个，约<span id="total_size2">0 Bytes</span></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-hover table-bordered" id="update_table" style="display: none">
                        <thead>
                        <tr>
                            <th>增量资源打包进度</th>
                            <th>完整资源打包进度</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th>
                                <div class="layui-progress layui-progress-big">
                                    <div class="layui-progress-bar layui-bg-green" id="update_progress1" style="width: 0%;"><span id="update_progress_text1" class="layui-progress-text">0%</span></div>
                                    <input type="hidden" id="update_num1" value="0"/>
                                </div>
                            </th>
                            <th>
                                <div class="layui-progress layui-progress-big">
                                    <div class="layui-progress-bar layui-bg-blue" id="update_progress2" style="width: 0%;"><span id="update_progress_text2" class="layui-progress-text">0%</span></div>
                                    <input type="hidden" id="update_num2" value="0"/>
                                </div>
                            </th>
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
            var down_url = "{{route('admin.data.exhibit.down_file')}}";
            location.href = down_url;
        }

        //增量统计数量初始化
        var new_path = [];
        new_path.push("{{$new_path}}");
        var arr1 = {};
        arr1['folder_num'] = 0;
        arr1['mp3_num'] = 0;
        arr1['mp3_size'] = 0;
        arr1['image_num'] = 0;
        arr1['image_size'] = 0;
        arr1['html_num'] = 0;
        arr1['html_size'] = 0;
        arr1['other_num'] = 0;
        arr1['other_size'] = 0;
        arr1['total_num'] = 0;
        arr1['total_size'] = 0;
        arr1['path'] = new_path;
        arr1['folder_path'] = new_path;
        arr1['file_path'] = new_path;
        arr1['large_file_path'] = new_path;
        arr1['folder_path_num'] = 0;
        arr1['file_path_num'] = 0;
        arr1['large_file_path_num'] = 0;
        //全量统计数量初始化
        var total_path = [];
        total_path.push("{{$total_path}}");
        var arr2 = {};
        arr2['folder_num'] = 0;
        arr2['mp3_num'] = 0;
        arr2['mp3_size'] = 0;
        arr2['image_num'] = 0;
        arr2['image_size'] = 0;
        arr2['html_num'] = 0;
        arr2['html_size'] = 0;
        arr2['other_num'] = 0;
        arr2['other_size'] = 0;
        arr2['total_num'] = 0;
        arr2['total_size'] = 0;
        arr2['path'] = total_path;
        arr2['folder_path'] = total_path;
        arr2['file_path'] = total_path;
        arr2['large_file_path'] = total_path;
        arr2['folder_path_num'] = 0;
        arr2['file_path_num'] = 0;
        arr2['large_file_path_num'] = 0;
        //增量更新资源列表
        var version_zip_folder_path = {};
        var version_zip_file_path = {};
        var version_large_zip_file_path = {};
        var version_folder_path_num = 0;
        var version_file_path_num = 0;
        var version_large_file_path_num = 0;
        var total_zip_folder_path = {};
        var total_zip_file_path = {};
        var total_large_zip_file_path = {};
        var total_folder_path_num = 0;
        var total_file_path_num = 0;
        var total_large_file_path_num = 0;

        function tongji_start() {
            layer.confirm('您确定要统计资源详情吗？此操作耗时较长请耐心等待！', {
                btn: ['确定', '取消'], title: '资源更新统计', move: false, cancel: function () {
                }
            }, function () {
                $("#tongji").hide();
                layer.msg('资源文件详情统计中...', {icon: 16, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                upload_file(arr1, 0, 1);
            }, function () {

            });
        }

        /*
         * type=0 计算增量更新包
         * type=1 计算全量更新包，屏蔽version_*文件夹
         * type=2 计算下级文件夹
         * kind=1 更新增量包数据， 2更新全量包数据
         * */
        function upload_file(arr, type, kind) {
            $.ajax({
                cache: true,
                type: "POST",
                url: '{{route('admin.data.exhibit.resource_zip')}}',
                data: {arr: arr, type: type},// 你的formid
                async: false,
                error: function (request) {
                    setTimeout(function () {
                            upload_file(arr, type, kind);
                        },
                        1000);//加载出错暂停一秒后再次请求
                },
                success: function (data) {
                    $('#mp3_num' + kind).text(data.mp3_num);
                    $('#mp3_size' + kind).text(data.mp3_size_count);
                    $('#image_num' + kind).text(data.image_num);
                    $('#image_size' + kind).text(data.image_size_count);
                    $('#html_num' + kind).text(data.html_num);
                    $('#html_size' + kind).text(data.html_size_count);
                    $('#other_num' + kind).text(data.other_num);
                    $('#other_size' + kind).text(data.other_size_count);
                    $('#folder_num' + kind).text(data.folder_num);
                    $('#total_num' + kind).text(data.total_num);
                    $('#total_size' + kind).text(data.total_size_count);
                    if (data.type == 'end') {
                        if (kind == 1) {
                            if (data.total_num == 0) {
                                $("#update_zip").hide();
                            }
                            else {
                                $("#update_zip").show();
                            }
                            layer.closeAll('dialog');
                            version_zip_folder_path[version_folder_path_num] = data.folder_path;
                            version_zip_file_path[version_file_path_num] = data.file_path;
                            version_large_zip_file_path[version_large_file_path_num] = data.large_file_path;
                            layer.msg('完整资源文件详情统计中...', {icon: 16, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                            upload_file(arr2, 1, 2);
                        }
                        else {
                            total_zip_folder_path[total_folder_path_num] = data.folder_path;
                            total_zip_file_path[total_file_path_num] = data.file_path;
                            total_large_zip_file_path[total_large_file_path_num] = data.large_file_path;
                            layer.closeAll('dialog');
                        }
                    }
                    else {
                        if (kind == 1) {
                            if (data.folder_path_num == 1) {
                                version_zip_folder_path[version_folder_path_num] = data.folder_path;
                                version_folder_path_num = version_folder_path_num + 1;
                                data.folder_path = new_path;
                                data.folder_path_num = 0;
                            }
                            if (data.file_path_num == 1) {
                                version_zip_file_path[version_file_path_num] = data.file_path;
                                version_file_path_num = version_file_path_num + 1;
                                data.file_path = new_path;
                                data.file_path_num = 0;
                            }
                            if (data.large_file_path_num == 1) {
                                version_large_zip_file_path[version_large_file_path_num] = data.large_file_path;
                                version_large_file_path_num = version_large_file_path_num + 1;
                                data.large_file_path = new_path;
                                data.large_file_path_num = 0;
                            }
                        }
                        else {
                            if (data.folder_path_num == 1) {
                                total_zip_folder_path[total_folder_path_num] = data.folder_path;
                                total_folder_path_num = total_folder_path_num + 1;
                                data.folder_path = total_path;
                                data.folder_path_num = 0;
                            }
                            if (data.file_path_num == 1) {
                                total_zip_file_path[total_file_path_num] = data.file_path;
                                total_file_path_num = total_file_path_num + 1;
                                data.file_path = total_path;
                                data.file_path_num = 0;
                            }
                            if (data.large_file_path_num == 1) {
                                total_large_zip_file_path[total_large_file_path_num] = data.large_file_path;
                                total_large_file_path_num = total_large_file_path_num + 1;
                                data.large_file_path = total_path;
                                data.large_file_path_num = 0;
                            }
                        }
                        setTimeout(function () {
                            upload_file(data, 2, kind);
                        }, 10);
                    }
                }
            });
        }

        function update_totalzy() {
            layer.confirm('您确定要更新资源版本吗', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $("#update_table").show();
                $('#update_num1').val(0);
                $('#update_num2').val(0);
                $('#update_progress_text1').text(0);
                $('#update_progress_text2').text(0);
                $('#update_progress1').attr("style", 'width:100%');
                $('#update_progress2').attr("style", 'width:100%');
                layer.msg('增量资源打包中，请稍等...', {icon: 16, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                update_zip(1, 1, 0);
            }, function () {

            });
        }

        /*
         *
         *kind=1 更新增量包数据， 2更新全量包数据
         *type 类别 1增量文件夹 2增量资源 3全量文件夹 4全量资源 5增量大资源 6全量大资源
         *key 数组键值
         */
        function update_zip(kind, type, key) {
            var path = '';
            if (type == 1) {
                if (key <= version_folder_path_num) {
                    path = version_zip_folder_path[key];
                    update_zip2(path, kind, type, key);
                }
                else {
                    update_zip(1, 2, 0);
                }
            }
            else if (type == 2) {
                if (key <= version_file_path_num) {
                    path = version_zip_file_path[key];
                    update_zip2(path, kind, type, key);
                }
                else {
                    update_zip(1, 5, 0);
                }
            }
            else if (type == 5) {
                if (key <= version_large_file_path_num) {
                    path = version_large_zip_file_path[key];
                    update_zip2(path, kind, type, key);
                }
                else {
                    $('#update_progress_text' + kind).text('100%');
                    $("#update_progress" + kind).attr("style", "width:100%");
                    layer.closeAll('dialog');
                    layer.msg('完整资源打包中，请稍等...', {icon: 16, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                    update_zip(2, 3, 0);
                }
            }
            else if (type == 3) {
                if (key <= total_folder_path_num) {
                    path = total_zip_folder_path[key];
                    update_zip2(path, kind, type, key);
                }
                else {
                    update_zip(2, 4, 0);
                }

            }
            else if (type == 4) {
                if (key <= total_file_path_num) {
                    path = total_zip_file_path[key];
                    update_zip2(path, kind, type, key);
                }
                else {
                    update_zip(2, 6, 0);
                }
            }
            else if (type == 6) {
                if (key <= total_large_file_path_num) {
                    path = total_large_zip_file_path[key];
                    update_zip2(path, kind, type, key);
                }
                else {
                    $('#update_progress_text' + kind).text('100%');
                    $("#update_progress" + kind).attr("style", "width:100%");
                    layer.closeAll('dialog');
                    layer.msg('资源包校验中，请稍等...', {icon: 16, scrollbar: false, time: 0, shade: [0.3, '#393D49']});
                    zip_end();
                }
            }
            else {
                return false;
            }

        }

        function update_zip2(path, kind, type, key) {
            $.post('{{route('admin.data.exhibit.update_zip')}}', {path: path, kind: kind}, function (data) {
                key = parseInt(key) + parseInt(1);
                $('#update_num' + kind).val(parseInt($('#update_num' + kind).val()) + parseInt(data.update_num));
                var progress = (parseInt($('#update_num' + kind).val()) / parseInt($('#total_num' + kind).text()) * 100).toFixed(2);
                if (progress > 100) {
                    progress = 100 + '%'
                }
                else {
                    progress = progress + '%'
                }
                $('#update_progress_text' + kind).text(progress);
                $("#update_progress" + kind).attr("style", 'width:' + progress);
                update_zip(kind, type, key);
            })
        }

        function zip_end() {
            $.post('{{route('admin.data.exhibit.end_zip')}}', {type: 1}, function (data) {
                layer.closeAll('dialog');
                if (data.status) {
                    layer.msg(data.msg, {icon: 6, scrollbar: false, time: 1500, shade: [0.3, '#393D49']});
                    setTimeout(function () {
                        location.href = "{{route('admin.data.exhibit.resource_zip')}}";
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

