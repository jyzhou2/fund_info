/* 地图加载初始化 */
//point_img 选中位置显示的图标
//echart_path echart路径
//svg_path 显示的svg地图绝对路径
//boxid 在哪显示svg
//data 初始化的时候位置信息
function map(point_img,echart_path,svg_path,box_id, str,dituname)
{
    require.config({
        paths: {
            echarts: echart_path
        }
    });
    var ob = eval("(" + str + ")");
    require(
        [
            'echarts',
            'echarts/chart/map' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            var myChart = ec.init(document.getElementById(box_id));
            require('echarts/util/mapData/params').params.map1024 = {
                getGeoJson: function (callback) {
                    $.ajax({
                        url: svg_path,
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
                    text: dituname,
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
                        textFixed: {
                            // '球门区1' : [0, -20],//位置（上下 左右）
                            '球门区2': [0, -20],
                            '禁区1': [10, 20],
                            '禁区2': [-10, 20],
                            '禁区弧线1': [0, -20],
                            '禁区弧线2': [0, -20],
                            '中圈': [0, -20],
                            '开球点': [0, 20]
                        },
                        //圈
                        markPoint: {
                            clickable: false,
                            symbol: 'image://' + point_img,
                            symbolSize: 10,
                            effect: {
                                show: false,
                                color: 'lime'
                            },
                            data: ob
                        },
                        scaleLimit: {
                            max: 15,
                            min: 1
                        }
                    }]
            };
            // 为echarts对象加载数据
            myChart.setOption(option);
            var ecConfig = require('echarts/config');
            myChart.on(ecConfig.EVENT.CLICK, focus);
            function focus(data) {
                if (data.name != 'aaa') {
                    var new_point = [];
                    myChart.delMarkPoint(0, 'ob');
                    myChart.delMarkPoint(0, 'aaa');
                    $("#x").val(data.posx.toFixed(0));
                    $("#y").val(data.posy.toFixed(0));
                    new_point.push({
                        name: 'aaa',
                        geoCoord: [data.posx.toFixed(0), data.posy.toFixed(0)]
                    });
            		console.log( data.posx.toFixed(0) +" "+data.posy.toFixed(0) )
                    myChart.addMarkPoint(0, {data: new_point});
                    myChart.refresh();
                }
            }
        }
    );
}