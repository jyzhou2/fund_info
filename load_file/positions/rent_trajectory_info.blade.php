@extends('layouts.public')
@section('head')
    <link rel="stylesheet" href="{{cdn('js/plugins/webuploader/single.css')}}">
    <style>
        .layui-this a {
            color: #fff;
        }

        #show-msg {
            margin: 15px 30px 0;
        }

        #show-msg label {
            font-size: 16px;
            margin-right: 30px;
        }
    </style>
@endsection
@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.positions.rent_trajectory_list')}}">租赁中的轨迹列表</a></li>
                        <li class="active"><a href="{{route('admin.positions.rent_trajectory_info',[$rent_id])}}">轨迹详情</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <ul class="layui-tab-title">
                        @foreach ($map_info as $g)
                            <li @if($map_id==$g['map_id'])class="layui-this"@endif ><a href="{{route('admin.positions.rent_trajectory_info',[$rent_id,$g['map_id']])}}">{{$g['map_name']}}</a></li>
                        @endforeach
                    </ul>
                    <div id="show-msg">
                        <label>租赁者姓名：{{$rent_info->RENT_NAME}}</label>
                        <label>租赁时间：{{$rent_info->RENT_STARTTIME}}</label>
                        <label>设备编号：{{$rent_info->RENT_DEVICENO}}</label>
                    </div>
                    <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
                    <div name='position' id='position' class="input2"
                         style="margin-left:30px;margin-top:10px;margin-bottom:10px;float:left;border: 2px dashed #8A8A8A;background-color: #F8F1E1;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{cdn('js/plugins/echarts/echarts.js')}}"></script>
    <script>
        $(window).resize(function () {
            $("#position").width($(window).width() - 175);
            $("#position").height($(window).height() - 230);
        }).resize();
        //页面初始化加载地图
        map("{{$gj_info['path']}}",{!! $gj_info['map_json'] !!});

        function map(path, lineObject) {
            require.config({
                paths: {
                    echarts: '{{cdn('js/plugins/echarts')}}'
                }
            });
            require(
                [
                    'echarts',
                    'echarts/chart/map' // 使用柱状图就加载bar模块，按需加载
                ],
                function (ec) {
                    // 基于准备好的dom，初始化echarts图表
                    var myChart = ec.init(document.getElementById('position'));

                    require('echarts/util/mapData/params').params.map1024 = {
                        getGeoJson: function (callback) {
                            $.ajax({
                                url: path,
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
                            //text : dituname,
                            //subtext: '地图SVG扩展',
                            textStyle: {
                                color: '#000000'
                            }
                        },
                        tooltip: {
                            show: false,
                            trigger: 'item',
                            formatter: function (param) {
                                if (param[1]) {
                                    return param[1].replace(">", "-");
                                } else {
                                    return null;
                                }
                            }
                        },
                        series: [
                            {
                                name: "时间",
                                type: 'map',
                                mapType: 'map1024', // 自定义扩展图表类型
                                roam: true,
                                tooltip: {
                                    show: true,
                                    trigger: 'item',
                                    formatter: function (param) {
                                        if (param[1]) {
                                            return "参观时间<br/>" + param[1].replace(">", "-");
                                        } else {
                                            return "导览区";
                                        }
                                    }
                                },
                                itemStyle: {
                                    normal: {label: {show: true}},
                                    emphasis: {label: {show: true}}
                                },
                                data: [
                                    {name: '', hoverable: false, itemStyle: {normal: {label: {show: false}}}}
                                ],
                                //点
                                /* markPoint : {
                                 clickable: false,
                                 symbol:'image://__PUBLIC__/Common/images/dw.png',
                                 symbolSize : 10,
                                 effect : {
                                 show: false,
                                 color: 'lime'
                                 },
                                 data : pointObject
                                 },*/
                                //轨迹路线
                                markLine: {
                                    name: "time1",
                                    itemStyle: {
                                        normal: {
                                            color: "#5D9CEC",
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
                    var ecConfig = require('echarts/config');
                }
            );
        }
    </script>
@endsection