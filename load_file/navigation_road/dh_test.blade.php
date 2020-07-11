@extends('layouts.public')
@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
@endsection
@section('bodyattr')class="gray-bg"@endsection
@section('style')
    <style>
        .nav {
            margin-bottom: 0;
        }

        .nav-map {
            margin-left: 40px;
            margin-right: 40px;
            margin-top: 20px;
            background-color: #F1F2F7;
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
        }

        .form-horizontal .form-group {
            margin-right: -210px;
        }

        .nav-tabs > li {
            margin-bottom: 0px;
        }

        .nav-map li a {
            /*border: 1px solid #C2E3F9;*/
            color: #9b9999;
            /*background-color: #C2E3F9;*/
            -webkit- transition: all .3s;
            -moz- transition: all .3s;;
            -o- transition: all .3s;
            transition: all .3s;
            cursor: pointer;
        }

        .nav-map li a:hover {
            /*background-color: #48ACEE;*/
            border-bottom: 2px solid #5D9CEC;
            color: #555;
        }

        .nav-map li.backblue a {
            /*background-color: #48ACEE;*/
            border-bottom: 2px solid #5D9CEC;
            color: #555;
        }

        .nav-tabs {
            border-bottom: none
        }

        #show-msg {
            margin-left: 40px;
            margin-left: 40px;
            border: 1px solid #5D9CEC;
            width: 94.5%;
            height: 30px;
            padding: 13px 0px 0 10px;
        }

        #show-msg label {
            margin: auto 10px auto 0;
            float: left;
            color: #555;
            font-family: "Microsoft YaHei";
        }

        .nav-map {
            height: 52px;
        }

        .nav-map li:hover {
            border-bottom: none;
        }
    </style>
@endsection
@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        @foreach($map_list as $map)
                            @if($map_info['id'] == $map['id'])
                                <li class="active"><a href="{{route('api.navigation.dh_test')."?p=a&map_id=".$map['id']}}">{{$map['map_name']}}</a></li>
                            @else
                                <li><a href="{{route('api.navigation.dh_test')."?p=a&map_id=".$map['id']}}">{{$map['map_name']}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
                        <div name='position' id='position' class="input2" style="margin-left:40px;margin-top:10px;float:left;border: 2px dashed #8A8A8A;background-color: #F8F1E1;"></div>
                    </div>
                    <div class="add-point layer-div">
                        <input type="radio" name="navigation" checked value="1">设置起点<br/>
                        <input type="radio" name="navigation" value="2">设置终点<br/>
                        起点X(start_x)<input type="text" id="start_x" style="width: 100px"/><br/>
                        起点Y(start_y)<input type="text" id="start_y" style="width: 100px"/><br/>
                        终点X(end_x)<input type="text" id="end_x" style="width: 100px"/><br/>
                        终点Y(end_y)<input type="text" id="end_y" style="width: 100px"/><br/>
                        地图编号(map_id)<input type="text" id="map_id" value="{{$map_info['id']}}" style="width: 100px" readonly/><br/>
                        <input type="button" value="查询" onclick="search()"/>
                        <input type="button" value="重置" onclick="location.href='{{route('api.navigation.dh_test')."?p=a&map_id=".$map['id']}}'"/>
                        <br/>
                        <div id="link_time"></div>
                        <div id="user_time"></div>
                        <textarea readonly id="result" style="width: 400px;height: 300px;resize: none"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')
    <script src="{{cdn('js/plugins/echarts/echarts.js')}}"></script>
    <script>
        $('.nav-map').find('li:first').addClass("backblue");
        $(".nav-map").on("click", "li", function () {
            $(this).addClass("backblue").siblings().removeClass("backblue");
        });
        $(window).resize(function () {
            $("#position").width($(window).width() - 640);
            $("#position").height($(window).height() - 170);
        }).resize();
        map([], []);

        // 第一次load标志位
        function map(infoArr, lineObject) {
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
                                url: "{{$map_info['map_path']}}",
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
                        /*console.log("点击事件");
                        console.log(data);*/
                        var v = $('input:radio[name="navigation"]:checked').val();
                        console.log(v);
                        if (v == 1) {
                            var new_point = [];
                            myChart.delMarkPoint(0, 'aaa');
                            new_point.push({
                                name: 'aaa',
                                value: '起点',
                                itemStyle: {
                                    normal: {
                                        color: '#57c8f2'
                                    }
                                },
                                geoCoord: [data.posx.toFixed(0), data.posy.toFixed(0)]
                            });
                            myChart.addMarkPoint(0, {data: new_point});
                            myChart.refresh();
                            $("#start_x").val(data.posx.toFixed(0));
                            $("#start_y").val(data.posy.toFixed(0));
                        }
                        else {
                            var new_point = [];
                            myChart.delMarkPoint(0, 'bbb');
                            new_point.push({
                                name: 'bbb',
                                value: '终点',
                                itemStyle: {
                                    normal: {
                                        color: '#ffb400'
                                    }
                                },
                                geoCoord: [data.posx.toFixed(0), data.posy.toFixed(0)]
                            });
                            myChart.addMarkPoint(0, {data: new_point});
                            myChart.refresh();
                            $("#end_x").val(data.posx.toFixed(0));
                            $("#end_y").val(data.posy.toFixed(0));
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
                                //点
                                markPoint: {
                                    clickable: true,
                                    symbol: 'pin',
                                    symbolSize: 10,
                                    itemStyle: {
                                        normal: {
                                            color: "#FFB400"
                                        }
                                    },
                                    data: infoArr
                                },
                                //轨迹路线
                                markLine: {
                                    name: "time1",
                                    itemStyle: {
                                        normal: {
                                            color: "#FFB400",
                                            borderWidth: 1,
                                            lineStyle: {
                                                type: 'solid'
                                            }
                                        }
                                    },
                                    smooth: true,
                                    data: lineObject
                                },
                                scaleLimit: {max: 10, min: 0.5},
                            }
                        ]
                    };
                    // 为echarts对象加载数据
                    myChart.setOption(option);
                }
            );
        }


        //ajax切换地图
        function search() {
            var starttime = new Date().getTime();
            $.post("{{route('api.navigation.dh_test')}}", {
                    p: 'a',
                    map_id: $("#map_id").val(),
                    start_x: $("#start_x").val(),
                    start_y: $("#start_y").val(),
                    end_x: $("#end_x").val(),
                    end_y: $("#end_y").val(),
                    type: 2
                },
                function (data) {
                    var endtime = new Date().getTime();
                    var link_time = Math.round((Number(endtime) - Number(starttime) - Number(data.status.user_time)) * 100) / 100;
                    $("#result").val(JSON.stringify(data.status.json_info));
                    $("#user_time").html('<h2>路径计算耗时：' + data.status.user_time + '毫秒</h2>');
                    $("#link_time").html('<h2>网络连接耗时：' + link_time + '毫秒</h2>');
                    var lineObject = [];
                    var infoArr = [];
                    console.log("以下为查询数据");
                    console.log(data.status);
                    for (var i = 0; i < data.status.road_num; i++) {
                        lineObject.push([{name: "11", geoCoord: [data.status.road_info[i][0]['x'], data.status.road_info[i][0]['y']]}, {
                            name: "11",
                            geoCoord: [data.status.road_info[i][1]['x'], data.status.road_info[i][1]['y']]
                        }]);
                    }
                    map(infoArr, lineObject);//地图加载初始化*/
                });
        }


    </script>
@endsection
