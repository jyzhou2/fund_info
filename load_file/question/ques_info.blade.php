@extends('layouts.public')

@section('head')
    <style type="text/css">
        .itra-list {
            float: left;
            width: 60%;
            margin-top: 20px;
            /*min-width: 780px;*/
            padding: 0;
            display: inline-block;
        }

        .itra-statistics {
            float: left;
            width: 30%;
            margin-left: 5%;
            margin-top: 50px;
        }

        /*问卷新样式*/
        /*问卷新样式*/
        .pages_wrap {
            max-width: 87%;
            overflow: hidden;
            display: inline-block;
            vertical-align: middle;
        }

        .pages_wrap ul {
            white-space: nowrap;
            margin: 0;
        }

        .pages_wrap .pages_item {
            display: inline-block;
            text-align: center;
            height: 50px;
            line-height: 50px;
            width: 96px;
            /*background-color: #fafafa;
            border: 1px solid #e0e0e0;*/
            font-size: 18px;
            margin-right: -5px;
            color: darkgray;
        }

        .pages_wrap .pages_item a {
            color: darkgray;
        }

        .pages_wrap .pages_item:hover {
            cursor: pointer;
            /*background-color: #fff;*/
            border-bottom: 2px solid #5084CD;
            color: #2C2B2B;
        }

        .pages_wrap .pages_item:hover a {
            color: #2C2B2B;
        }

        .pages_wrap .pages_item:last-child {
            border-right: none;
        }

        .pages_wrap .pages_item .pages_remove {
        }

        .pages_wrap .current {
            /*background: #ffffff;
            border-bottom: none;*/
            border-bottom: 2px solid #5084CD;
            color: #2C2B2B;
        }

        .pages_wrap .current a {
            color: #2C2B2B;
        }

        .pages_preview, .pages_next, .pages_more {
            display: inline-block;
            text-align: center;
            height: 50px;
            line-height: 50px;
            width: 40px;
            /*background-color: #fafafa;
            border: 1px solid #e0e0e0;*/
            font-size: 18px;
            margin-right: -5px;
            vertical-align: middle;
            color: #2C2B2B;
        }

        .pages_preview:hover i, .pages_next:hover i {
            color: #5084CD;
        }

        .pages_next {
            margin-left: -4px;
        }

        .hidden_nav {
            background-color: #F0EFEF;
            width: 96.2%;
            position: relative;
        }

        .survey_main {
            height: 600px;
            width: 94%;
            border: 1px solid #5084CD;
            position: relative;
            padding: 10px;
        }

        /*统计*/
        .itra-statistics {
            float: left;
            width: 30%;
            margin-left: 5%;
            /*margin-top: 50px;*/
        }

    </style>

    <script src="{{cdn('js/plugins/echarts/echarts.min.js')}}"></script>
@endsection

@section('body')
    <div class="js-check-wrap">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.question.ques_list')}}">问卷管理</a></li>
                        <li class="active"><a href="{{route('admin.question.ques_info').'?id='.$id}}">结果统计</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="itra-list" style="padding: 10px 20px 0px;">
            <div class="survey_pages_tab hidden_nav">
                <a class="pages_preview" href="javascript:;"><i class="fa fa-chevron-left" title="" style=""></i></a>
                <div class="pages_wrap">
                    <!-- 页码容器 -->
                    <ul id="ajax_li">
                        @foreach($info as $k=>$vo)
                            @if($vo['type']==3)
                                <a style="color:#555;text-decoration:none;" href='javascript:viewStatistics([{name:"参与人数",value:{{$vo['text_num']}}}])'>
                                    @else
                                        <a style="color:#555;text-decoration:none;" href='javascript:viewStatistics([
								@foreach($vo['option_info'] as $kk=>$g)
                                                {name:"{{$g['option_info']}}",value:{{$g['option_num']}}},
								@endforeach
                                                ])'>
                                            @endif
                                            <li class="pages_item" onclick=change_page("p-{{$k+1}}") data-pid="p-{{$k+1}}">
                                                <span class="pages_show">第{{$k+1}}题</span>
                                            </li>
                                        </a>
                                @endforeach
                    </ul>
                </div>
                <a class="pages_next" href="javascript:;"><i class="fa fa-chevron-right" title="" style=""></i></a>
            </div>
            <div class="survey_main">
				<?php
				$ques_arr = [
					'1' => '(单选)',
					'2' => '(可多选)',
					'3' => '(问答)'
				];
				$option_en = [
					'1' => 'A',
					'2' => 'B',
					'3' => 'C',
					'4' => 'D',
					'5' => 'E',
					'6' => 'F',
					'7' => 'G',
					'8' => 'H',
					'9' => 'I',
					'10' => 'J',
					'11' => 'K',
					'12' => 'L',
					'13' => 'M',
					'14' => 'N'
				];
				?>
                @foreach($info as $k=>$vo)
                    <div class="page" id="p-{{$k+1}}" style="display: none;">
                        <div class="title" style="font-size: 30px;line-height: 35px;margin: 10px 0;">题目：{{$vo['question']}}{{$ques_arr[$vo['type']]}}</div>
                        <table class="table table-hover table-bordered">
                            @if($vo['type']==3)

                                <thead>
                                <tr>
                                    <th>作答详情</th>
                                    <th>提交时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($vo['text_info'] as $kk=>$g)
                                    <tr>
                                        <td>{{$g['text_info']}}</td>
                                        <td>{{$g['datetime']}}</td>
                                    </tr>
                                    @if($kk==10)
                                        <tr>
                                            <td colspan="2"><a href="javascript:ques_textinfo({{$g['quesinfo_id']}},{{$g['ques_id']}},'题目：{{$vo['question']}}{{$ques_arr[$vo['type']]}}')">查看更多</a></td>
                                        </tr>
                                    @endif
                                @endforeach

                                </tbody>
                            @else
                                <thead>
                                <tr>
                                    <th>选项</th>
                                    <th>结果详情</th>
                                </tr>
                                </thead>
                                <tbody id='tb'>
                                @foreach($vo['option_info'] as $kk=>$g)
                                    <tr>
                                        <td>{{$option_en[$kk + 1 ]}}.{{$g['option_info']}}</td>
                                        <td>{{$g['option_num']}}票
                                            @if($g['option_type']==2)
                                                <a href="javascript:ques_textinfo({{$g['quesinfo_id']}},{{$g['ques_id']}},'题目：{{$vo['question']}}{{$ques_arr[$vo['type']]}}')">查看详情</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            @endif
                        </table>
                    </div>
                @endforeach

            </div>
        </div>


        <div class="itra-statistics">
            <div id="main" style="width: 500px;height:500px;"></div>

            <script type="text/javascript">
                // 基于准备好的dom，初始化echarts实例
                viewStatistics([
                    @if(isset($info[0]))
                        @foreach($info[0]['option_info'] as $k=>$g)
                        {
                            name: "{{$g['option_info']}}", value:{{$g['option_num']}}
                        },
                        @endforeach
                    @endif
                ]);

                function viewStatistics(answer) {
                    var chart = echarts.init(document.getElementById('main'));
                    var arr1 = [];
                    var arr2 = [];
                    $.each(answer, function () {
                        var name = this['name'];
                        var valeu = this['value'];
                        arr1.push({
                            name
                        });
                        arr2.push({
                            name: name,
                            value: valeu,
                        });
                    });
                    var option = {
                        title: {
                            text: '问卷调查统计',
                            x: 'center',
                            // top:'-20%'
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: "{a} : {b}<br/> 投票人数 : {c}  ({d}%)"
                        },
                        legend: {
                            orient: 'horizontal',
                            top: 'top',
                            top: '10%',
                            data: arr1
                        },
                        series: [
                            {
                                name: '调查结果',
                                type: 'pie',
                                radius: '40%',
                                center: ['50%', '70%'],
                                data: arr2,
                                itemStyle: {
                                    emphasis: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };
                    chart.setOption(option);
                }

            </script>

        </div>
        <!-- /.itra-statistics -->
    </div>
@endsection
@section('script')

    <script>
        //切换内容
        function change_page(id) {
            console.log(id);
            var question_index = +id.split('p-')[1];
            $('.pages_item').removeClass('current');
            $($('.pages_item')[question_index - 1]).addClass('current');
            // $('.pages_item').siblings("[data-pid='"+ id +"']").addClass('current').siblings('.pages_item').removeClass('current');
            $('.page').css('display', 'none');
            $("#" + id).show();
        }
    </script>
    <script>
        $(function () {
            $('.pages_item:first').addClass('current');
            $('.page:first').css('display', 'block');
            $('.pages_next').click(function () {
                $('.pages_wrap').scrollLeft($('.pages_wrap').scrollLeft() + 98);
            });
            $('.pages_preview').click(function () {
                $('.pages_wrap').scrollLeft($('.pages_wrap').scrollLeft() - 98);
            });
        })
    </script>
    <script>
        function ques_textinfo(quesinfo_id, ques_id, title) {

            var url = "{{route('admin.question.ques_textinfo')}}?&ques_id=" + ques_id + "&quesinfo_id=" + quesinfo_id;
            var index = layer.open({
                title: title,
                type: 2,
                area: ['1000px', '550px'],
                fix: true, //固定
                maxmin: false,
                move: false,
                content: url
            });
            //layer.full(index);
        }
    </script>
@endsection