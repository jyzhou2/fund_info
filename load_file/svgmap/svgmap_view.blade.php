@extends('layouts.public')
@section('head')
@endsection
@section('bodyattr')class=""@endsection
@section('body')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div name='position' id='position' class="input2" style="margin: 10px 0 0 30px;"></div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{cdn('js/plugins/echarts/echarts.js')}}"></script>
    <script type="text/javascript">

        $(window).resize(function () {
            $("#position").width($(window).width() - 80);
            $("#position").height($(window).height() - 70);
        }).resize();

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
                            url: '{{get_file_url($map->map_path)}}',
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
                                data: []
                            },
                            scaleLimit: {max: 10, min: 0.5},
                        }
                    ]
                };
                // 为echarts对象加载数据
                myChart.setOption(option);
            }
        );
    </script>
@endsection