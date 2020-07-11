@extends('layouts.public')
@section('head')
@endsection
@section('bodyattr')class=""@endsection

@section('body')
    <style>
        .map {
            width: 1000px;
            height: 600px;
        }
    </style>
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        @foreach($map_list as $map)
                            <li @if($map->id == $map_id) class="active" @endif><a href="{{route('admin.navigationroad.route_edit')."?map_id=".$map->id}}">{{$map->map_name}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="{{route('admin.navigationroad.route_save')}}" method="post" class="form-horizontal ajaxForm">

                        <div class="form-group">
                            <div id="map" class="col-sm-6 col-md-offset-2 map">

                            </div>
                        </div>
                        <input type="hidden" id="area_data" name="area_data" value="">
                        <input type="hidden" id="point_data" name="point_data" value="">
                        <input type="hidden" id="map_id" name="map_id" value="{{$map_id}}">
                        <input type="hidden" id="road_id" name="road_id" value="{{$road_id or 0}}">
                        <div class="form-group">
                            <div class="col-sm-6 col-md-offset-2">
                                <button class="btn btn-info" type="button" id="del_last" onclick="delLast()">路点清除</button>

                                <button class="btn btn-add" type="button" id="add_new" onclick="addLine()">新建路线</button>
                                <button class="btn btn-primary" id="submit_area" style="display: none" type="submit">保存</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="{{cdn('js/plugins/echarts/echarts.js')}}"></script>
    <script type="text/javascript">
        var pointArr = [], lineArr = [], myChart;

        lineArr = {!! $lineArr !!}
            pointArr = {!! $pointArr !!}
            lineArr = eval(lineArr)
        pointArr = eval(pointArr)
        $('#area_data').val(JSON.stringify(lineArr));
        $('#point_data').val(JSON.stringify(pointArr));

        /* 地图加载初始化 */
        function map() {
            require.config({
                paths: {
                    echarts: '{{cdn('js/plugins/echarts')}}'
                }
            });
            // var ob = eval("(" + str + ")");
            require(
                [
                    'echarts',
                    'echarts/chart/map' // 使用柱状图就加载bar模块，按需加载
                ],
                function (ec) {
                    // 基于准备好的dom，初始化echarts图表
                    myChart = ec.init(document.getElementById('map'));

                    require('echarts/util/mapData/params').params.map1024 = {
                        getGeoJson: function (callback) {
                            $.ajax({
                                url: "{{cdn($map_path)}}",
                                dataType: 'xml',
                                success: function (xml) {
                                    callback(xml)
                                }
                            });
                        }
                    }
                    var option = {
                        backgroundColor: '#F8F1E1',
                        title: {
                            text: '辅助路线',
                            //subtext: '地图SVG扩展',
                            textStyle: {
                                color: '#000000'
                            }
                        },
                        /*tooltip : {
                         trigger: 'item'
                         },*/
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

                                textFixed: {},
                                //圈
                                markPoint: {
                                    clickable: true,
                                    symbol: "image://{{cdn('js/plugins/echarts/dw.png')}}",
                                    symbolSize: 6,
                                    effect: {
                                        show: false,
                                        color: 'lime'
                                    },
                                    data: pointArr
                                },
                                markLine: {
                                    name: "time1",
                                    symbol: "none",
                                    itemStyle: {
                                        normal: {
                                            color: "#5D9CEC",
                                            borderWidth: 1,
                                            lineStyle: {
                                                type: 'solid'
                                            }
                                        }
                                    },
                                    smooth: false,
                                    data: lineArr
                                },
                                scaleLimit: {max: 15, min: 1},
                            }
                        ]
                    };

                    // 为echarts对象加载数据
                    myChart.setOption(option);
                    var ecConfig = require('echarts/config');
                    myChart.on(ecConfig.EVENT.CLICK, focus);
                }
            );
        }

        map();


        // 新建路线标志位
        var newFlag = true;

        function focus(data) {
            /*console.log("点击事件");
            console.log(data);*/
            if (data.name != 'aaa') {
                var new_point = [];
                var pointname = pointArr.length > 0 ? ('' + (parseInt(pointArr[pointArr.length - 1].name) + 1) ) : '1';

                var nowX, nowY;
                if (data.data.name == '') {
                    nowX = data.posx.toFixed(0);
                    nowY = data.posy.toFixed(0);
                } else {
                    nowX = data.data.geoCoord[0];
                    nowY = data.data.geoCoord[1];
                }
                var newpointObj = {
                    name: pointname,
                    geoCoord: [nowX, nowY]
                };
                pointArr.push(newpointObj);
                new_point.push(newpointObj);

                myChart.addMarkPoint(0, {data: new_point});

                // [{name:"2017-11-18 11:38:30",geoCoord:[1988,1801]},{name:"2017-11-18 12:11:00",geoCoord:[2235,1847]}]
                // debugger
                if (newFlag == true) {
                    newFlag = false;
                } else {
                    // if(pointArr.length > 1){
                    var newLine = [];
                    newLine.push([pointArr[pointArr.length - 2], pointArr[pointArr.length - 1]]);
                    lineArr.push([pointArr[pointArr.length - 2], pointArr[pointArr.length - 1]]);

                    myChart.addMarkLine(0, {data: newLine});
                    // }
                }


                myChart.refresh();
                $("#submit_area").show();
                $('#area_data').val(JSON.stringify(lineArr));
                $('#point_data').val(JSON.stringify(pointArr));
            }
        }

        function delLast() {
            var lastPointname = '';
            if (pointArr.length != 0) {
                lastPointname = pointArr[pointArr.length - 1].name;
                myChart.delMarkPoint(0, pointArr[pointArr.length - 1].name);
                myChart.refresh();
                pointArr.pop();
                newFlag = true;
            }

            // 点数组最后一个点是线数组最后一条线的被指向点，则删除此线，证明之前是连着的

            if (lineArr.length != 0) {
                if (lineArr[lineArr.length - 1][1].name == lastPointname) {
                    var nameLast = lineArr[lineArr.length - 1][1].name;
                    var namePrev = lineArr[lineArr.length - 1][0].name;
                    myChart.delMarkLine(0, namePrev + ' > ' + nameLast);
                    myChart.refresh();
                    lineArr.pop();
                }
            }

            $('#area_data').val(JSON.stringify(lineArr));
            $('#point_data').val(JSON.stringify(pointArr));
            $("#submit_area").show();
        }

        function addLine() {
            $("#submit_area").show();
            newFlag = true;
        }
    </script>
@endsection

