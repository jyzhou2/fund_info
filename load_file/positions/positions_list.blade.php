@extends('layouts.public')
@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
@endsection
@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        @foreach ($map_info as $g)
                            <li @if($map_id==$g['map_id'])class="active"@endif ><a href="{{route('admin.positions.positions_list').'/'.$g['map_id']}}">{{$g['title']}}</a></li>
                        @endforeach

                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form role="form" class="form-inline" method="get">
                        <div class="form-group">
                            <input type="text" name="keywords" id="keywords" style="width: 300px" placeholder="租赁者姓名/证件号/用户账号/用户昵称" class="form-control" value="{{$keywords}}">
                        </div>
                        &nbsp;&nbsp;
                        <button type="button" onclick="search(0)" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.positions.positions_list').'/'.$map_id}}'">重置</button>
                    </form>
                    <div name='position' id='position' class="input2" style="margin: 10px 0 0 30px;"></div>
                </div>
                <div id="detail" style="display: block; background:#fff;width:600px;border-radius:4px;position: absolute; left: 40%; top: 20%;display: none; ">
                    <div style="background:#5D9CEC;height:40px;border-radius: 4px 4px 0 0;">
                        <span style="margin-left:10px;color:#fff;line-height:40px;" id="exhibit_name">团队详情</span>
                        <span id="closeSpan" style="float:right;margin-right:10px;color:#fff;cursor:pointer;">x</span>
                    </div>
                    <div style="padding: 10px 20px 10px 20px;max-height:400px;overflow-y:auto;line-height: 30px;">
                        @if($show_type['sd'])
                            <span>第十代智慧导览机用户:</span><span id="dlj1Num"></span>
                            <div class="dlj1Down" title="显示第十代智慧导览机列表" style="display:inline-block;width: 286px;cursor: pointer;"><i class="fa fa-caret-down" style="font-size:20px;float: right;"></i>
                            </div>
                            <div class="dlj1Up" title="隐藏第十代智慧导览机列表" style="display:inline-block;width:286px;cursor: pointer;"><i class="fa fa-caret-up" style="font-size:20px;float: right;"></i></div>
                            <br/>
                            <table id="dlj1Table" class="table table-hover table-list detailTable" style="display:none;"></table>
                        @endif
                        @if($show_type['gq'])
                            <span>智慧国七导览机用户:</span><span id="dlj2Num"></span>
                            <div class="dlj2Down" title="显示智慧国七导览机列表" style="display:inline-block;width: 286px;cursor: pointer;"><i class="fa fa-caret-down" style="font-size:20px;float: right;"></i>
                            </div>
                            <div class="dlj2Up" title="隐藏智慧国七导览机列表" style="display:inline-block;width:286px;cursor: pointer;"><i class="fa fa-caret-up" style="font-size:20px;float: right;"></i></div>
                            <br/>
                            <table id="dlj2Table" class="table table-hover table-list detailTable" style="display:none;"></table>
                        @endif
                        @if($show_type['a'])
                            <span>安卓用户:</span><span id="aNum"></span>
                            <div class="aDown" title="显示安卓列表" style="display:inline-block;width: 286px;cursor: pointer;"><i class="fa fa-caret-down" style="font-size:20px;float: right;"></i>
                            </div>
                            <div class="aUp" title="隐藏安卓列表" style="display:inline-block;width:286px;cursor: pointer;"><i class="fa fa-caret-up" style="font-size:20px;float: right;"></i></div>
                            <br/>
                            <table id="aTable" class="table table-hover table-list detailTable" style="display:none;"></table>
                        @endif
                        @if($show_type['i'])
                            <span>IOS用户:</span><span id="iNum"></span>
                            <div class="iDown" title="显示IOS列表" style="display:inline-block;width: 286px;cursor: pointer;"><i class="fa fa-caret-down" style="font-size:20px;float: right;"></i>
                            </div>
                            <div class="iUp" title="隐藏IOS列表" style="display:inline-block;width:286px;cursor: pointer;"><i class="fa fa-caret-up" style="font-size:20px;float: right;"></i></div>
                            <br/>
                            <table id="iTable" class="table table-hover table-list detailTable" style="display:none;"></table>
                        @endif
                        @if($show_type['w'])
                            <span>微信用户:</span><span id="wNum"></span>
                            <div class="wDown" title="显示微信列表" style="display:inline-block;width: 286px;cursor: pointer;"><i class="fa fa-caret-down" style="font-size:20px;float: right;"></i>
                            </div>
                            <div class="wUp" title="隐藏微信列表" style="display:inline-block;width:286px;cursor: pointer;"><i class="fa fa-caret-up" style="font-size:20px;float: right;"></i></div>
                            <br/>
                            <table id="wTable" class="table table-hover table-list detailTable" style="display:none;"></table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{cdn('js/plugins/echarts/echarts.js')}}"></script>
    <script type="text/javascript">
        $("form input").keydown(function () {
            if (event.keyCode == 13) {
                return false;
            }
        })

        function search(map_id) {
            var keywords = $('#keywords').val();
            if (keywords == '' || keywords == null) {
                layer.msg("请输入查询条件", {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
                return false;
            }
            $.post('{{route('admin.positions.search')}}', {keywords: keywords}, function (data) {
                if (data.status == 'error') {
                    layer.alert(data.msg, {icon: 5, scrollbar: false, shade: [0.3, '#393D49']});
                }
                else {
                    if (map_id != data.arr.map_id) {
                        location.href = "{{route('admin.positions.positions_list')}}" + '/' + data.arr.map_id + '/' + data.arr.x + '/' + data.arr.y + '/' + data.arr.auto_num + '/' + data.arr.keywords;
                    }
                    else {
                        change_map(data.arr.map_id, data.arr.x, data.arr.y, data.arr.auto_num);
                    }
                }
            })
        }


        $(window).resize(function () {
            $("#position").width($(window).width() - 175);
            $("#position").height($(window).height() - 170);
        }).resize();
        //页面初始化加载地图 一会放开
        change_map({{$map_id}},{{$x}},{{$y}},{{$auto_num}});
        // 第一次load标志位
        var firstFlag = true;
        var myChart;

        function map(path, infoArr) {
            if (firstFlag) {
                require.config({
                    paths: {
                        echarts: '{{cdn('js/plugins/echarts')}}'
                    }
                });
                require([
                        'echarts',
                        'echarts/chart/map' // 使用柱状图就加载bar模块，按需加载
                    ],
                    function (ec) {
                        // 基于准备好的dom，初始化echarts图表
                        myChart = ec.init(document.getElementById('position'));
                        require('echarts/util/mapData/params').params.map1024 = {
                            getGeoJson: function (callback) {
                                $.ajax({
                                    url: path,
                                    dataType: 'xml',
                                    success: function (xml) {
                                        callback(xml);
                                    }
                                });
                            }
                        }
                        //marker点击事件
                        var ecConfig = require('echarts/config');
                        myChart.on(ecConfig.EVENT.CLICK, eConsole);

                        function eConsole(data) {
                            //alert(JSON.stringify(data));
                            if (data.name) {
                                //alert(data.name);
                                pos_info(data.name);
                            }

                        }

                        var option = {
                            backgroundColor: '#F8F1E1',
                            title: {
                                //text : dituname,
                                //subtext: '地图SVG扩展',
                                textStyle: {
                                    color: '#000000'
                                }
                            },
                            series: [
                                {
                                    name: '团队:',
                                    type: 'map',
                                    mapType: 'map1024', // 自定义扩展图表类型
                                    roam: true,
                                    itemStyle: {
                                        normal: {label: {show: true}},
                                        emphasis: {label: {show: true}}
                                    },
                                    data: [
                                        {name: '', hoverable: false, itemStyle: {normal: {label: {show: false}}}}
                                    ],
                                    markPoint: {
                                        clickable: true,
                                        symbol: 'pin',
                                        symbolSize: 10,
                                        itemStyle: {
                                            normal: {
                                                color: "#5D9CEC"
                                            }
                                        },
                                        data: infoArr
                                    },
                                    scaleLimit: {max: 10, min: 0.5},
                                }
                            ]
                        };
                        // 为echarts对象加载数据
                        myChart.setOption(option);
                        firstFlag = false;
                    }
                );
            } else {
                var oldPointArr = myChart.getSeries()[0].markPoint.data;
                for (var i = 0; i < oldPointArr.length; i++) {
                    var deleStr = oldPointArr[i]['name'];
                    myChart.delMarkPoint(0, deleStr);
                }
                myChart.addMarkPoint(0, {data: infoArr});
                myChart.refresh();
            }
        }

        //ajax切换地图
        function change_map(map_id, axis_x, axis_y, auto_num) {
            $.post("{{route('admin.positions.ajax_map')}}", {
                    map_id: map_id
                },
                function (data) {
                    console.log(data);
                    var infoArr = [];
                    if (axis_x != 0 && axis_y != 0) {
                        infoArr.push({
                            name: '' + auto_num + '',
                            geoCoord: [axis_x, axis_y],
                            //clickable: false,
                            symbol: "image://{{cdn('img/user.png')}}",
                            symbolSize: 15
                        });
                    }
                    else {
                        for (var i = 0; i < data.count; i++) {
                            infoArr.push({
                                name: '' + data.pos_info[i].auto_num + '',
                                value: data.pos_info[i].num,
                                geoCoord: [data.pos_info[i]['axis_x'], data.pos_info[i]['axis_y']]
                            });
                        }
                    }
                    //console.log(infoArr);
                    map(data.map_path, infoArr);//地图加载初始化

                    if (axis_x != 0 && axis_y != 0) {
                        setTimeout(function () {
                            search(map_id);
                        }, 15000);//有搜索条件120秒后刷新数据
                    }
                    else {
                        setTimeout(function () {
                            change_map(map_id, 0, 0, 0);
                        }, 15000);//没有搜索条件每15秒刷新一下数据
                    }
                });
        }


        //ajax获取详情
        function pos_info(auto_num) {
            $.post("{{route('admin.positions.point')}}", {
                auto_num: auto_num
            }, function (data) {
                /*console.log(data);*/
                $("#exhibit_name").text('展项名称：' + data.exhibit_name);
                @if($show_type['sd'])
                //第十代智慧导览机详情
                var dlj1_num = data.dlj1_num;
                $("#dlj1Num").text(dlj1_num + '人');
                $("#dlj1Table").empty();
                if (dlj1_num == 0) {
                    $('.dlj1Down').hide();
                    $('.dlj1Up').hide();
                    $('#dlj1Table').hide();
                } else {
                    $('.dlj1Down').show();
                    $('.dlj1Up').hide();
                }
                if (dlj1_num > 0) {
                    var dlj1 = data.dlj1;
                    $("#dlj1Table").append("<tr><th>租赁者姓名</th><th>第十代智慧导览机编号</th><th>到达时间</th><</tr>");
                    for (var c = 0; c < dlj1_num; c++) {
                        $("#dlj1Table").append("<tr><td>" + dlj1[c]['rent_name'] + "</td><td>" + dlj1[c]['deviceno'] + "</td><td>" + dlj1[c]['datetime'] + "</td></tr>");
                    }
                }
                @endif
                @if($show_type['gq'])
                //智慧国七导览机详情
                var dlj2_num = data.dlj2_num;
                $("#dlj2Num").text(dlj2_num + '人');
                $("#dlj2Table").empty();
                if (dlj2_num == 0) {
                    $('.dlj2Down').hide();
                    $('.dlj2Up').hide();
                    $('#dlj2Table').hide();
                } else {
                    $('.dlj2Down').show();
                    $('.dlj2Up').hide();
                }
                if (dlj2_num > 0) {
                    var dlj2 = data.dlj2;
                    $("#dlj2Table").append("<tr><th>租赁者姓名</th><th>智慧国七导览机编号</th><th>到达时间</th><</tr>");
                    for (var c = 0; c < dlj2_num; c++) {
                        $("#dlj2Table").append("<tr><td>" + dlj2[c]['rent_name'] + "</td><td>" + dlj2[c]['deviceno'] + "</td><td>" + dlj2[c]['datetime'] + "</td></tr>");
                    }
                }
                @endif
                @if($show_type['a'])
                //安卓详情
                var a_num = data.a_num;
                $("#aNum").text(a_num + '人');
                $("#aTable").empty();
                if (a_num == 0) {
                    $('.aDown').hide();
                    $('.aUp').hide();
                    $('#aTable').hide();
                } else {
                    $('.aDown').show();
                    $('.aUp').hide();
                }
                if (a_num > 0) {
                    var a = data.a;
                    $("#aTable").append("<tr><th>用户昵称</th><th>账号/设备号</th><th>到达时间</th><</tr>");
                    for (var c = 0; c < a_num; c++) {
                        $("#aTable").append("<tr><td>" + a[c]['rent_name'] + "</td><td>" + a[c]['deviceno'] + "</td><td>" + a[c]['datetime'] + "</td></tr>");
                    }
                }
                @endif
                @if($show_type['i'])
                //IOS详情
                var i_num = data.i_num;
                $("#iNum").text(i_num + '人');
                $("#iTable").empty();
                if (i_num == 0) {
                    $('.iDown').hide();
                    $('.iUp').hide();
                    $('#iTable').hide();
                } else {
                    $('.iDown').show();
                    $('.iUp').hide();
                }
                if (i_num > 0) {
                    var i = data.i;
                    $("#iTable").append("<tr><th>用户昵称</th><th>账号/设备号</th><th>到达时间</th><</tr>");
                    for (var c = 0; c < i_num; c++) {
                        $("#iTable").append("<tr><td>" + i[c]['rent_name'] + "</td><td>" + i[c]['deviceno'] + "</td><td>" + i[c]['datetime'] + "</td></tr>");
                    }
                }
                @endif
                @if($show_type['w'])
                //智慧国七导览机详情
                var w_num = data.w_num;
                $("#wNum").text(w_num + '人');
                $("#wTable").empty();
                if (w_num == 0) {
                    $('.wDown').hide();
                    $('.wUp').hide();
                    $('#wTable').hide();
                } else {
                    $('.wDown').show();
                    $('.wUp').hide();
                }
                if (w_num > 0) {
                    var w = data.w;
                    $("#wTable").append("<tr><th>用户昵称</th><th>账号/设备号</th><th>到达时间</th><</tr>");
                    for (var c = 0; c < w_num; c++) {
                        $("#wTable").append("<tr><td>" + w[c]['rent_name'] + "</td><td>" + w[c]['deviceno'] + "</td><td>" + w[c]['datetime'] + "</td></tr>");
                    }
                }
                @endif
                $("#detail").show();
                $("#closeSpan").click(function () {
                    $("#detail").hide();
                });
            });
        }

        @if($show_type['sd'])
        //显示\隐藏第十代智慧导览机详情table
        $('.dlj1Down').click(function () {
            $(this).hide();
            $('#dlj1Table').show();
            $('.dlj1Up').show();
        });
        $('.dlj1Up').click(function () {
            $(this).hide();
            $('#dlj1Table').hide();
            $('.dlj1Down').show();
        });
        @endif
        @if($show_type['gq'])
        //显示\隐藏智慧国七导览机详情table
        $('.dlj2Down').click(function () {
            $(this).hide();
            $('#dlj2Table').show();
            $('.dlj2Up').show();
        });
        $('.dlj2Up').click(function () {
            $(this).hide();
            $('#dlj2Table').hide();
            $('.dlj2Down').show();
        });
        @endif
        @if($show_type['a'])
        //显示\隐藏安卓详情table
        $('.aDown').click(function () {
            $(this).hide();
            $('#aTable').show();
            $('.aUp').show();
        });
        $('.aUp').click(function () {
            $(this).hide();
            $('#aTable').hide();
            $('.aDown').show();
        });
        @endif
        @if($show_type['i'])
        //显示\隐藏IOS详情table
        $('.iDown').click(function () {
            $(this).hide();
            $('#iTable').show();
            $('.iUp').show();
        });
        $('.iUp').click(function () {
            $(this).hide();
            $('#iTable').hide();
            $('.iDown').show();
        });
        @endif
        @if($show_type['w'])
        //显示\隐藏微信详情table
        $('.wDown').click(function () {
            $(this).hide();
            $('#wTable').show();
            $('.wUp').show();
        });
        $('.wUp').click(function () {
            $(this).hide();
            $('#wTable').hide();
            $('.wDown').show();
        });
        @endif
    </script>
@endsection