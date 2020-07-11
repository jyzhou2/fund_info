@extends('layouts.public')

@section('head')
    <style>
        .user-wrap {
            width: 100%;
            height: 690px;
        }

        .user-wrap:after {
            content: '';
            display: block;
            clear: both;
        }

        .col-item {
            width: 350px;
            /*min-width: 220px;*/
            height: 210px;
            float: left;
            position: relative;
            margin-bottom: 2%;
            margin-right: 1%;
        }

        .col-item .col-content {
            height: 200px;
            margin: 0 15px 15px 0;
            border: 1px solid #e3e3e3;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
            overflow: hidden;
            -webkit-box-shadow: 3px 3px 5px #eee;
            -moz-box-shadow: 3px 3px 5px #eee;
            -ms-box-shadow: 3px 3px 5px #eee;
            -o-box-shadow: 3px 3px 5px #eee;
            box-shadow: 3px 3px 5px #eee;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            -ms-border-radius: 3px;
            -o-border-radius: 3px;
            border-radius: 3px;
        }

        .user-message {
            width: 100%;
            height: 160px;
            /*float: left;*/
        }

        .user-message table {
            font-size: 16px;
            line-height: 2em;
            margin-top: 15px;
            margin-left: 10px;
            table-layout: fixed;
            width: 95%;
            color: #838389;
        }

        .user-message table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-controller {
            height: 36px;
            bottom: 11px;
            position: absolute;
            line-height: 34px;
            padding-top: 3px;
            background: rgba(250, 250, 250, 0.8);
            border-top: 1px solid #ddd;
            /*display: none;*/
            /*width: 88%;*/
            width: 333px;
        }

        .id_duration_time {
            font-size: 13px;
            position: absolute;
            top: 8px;
            margin-left: 2px;
        }

        /**max width 1366px**/
        /*@media screen and (max-width:1366px){
            .user-controller {
                width: 205px;
            }
        }
        *max width 1600px*
        @media screen and (max-width:1600px){
            .user-controller {
                width: 205px;
            }
        }*/
        .user-controller a {
            margin: 0 12.5%;
            padding: 0;
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 22px;
            text-align: center;
            text-decoration: none;
            border-radius: 50%;
            font-size: 18px;
            background-color: #fff;
        }

        .user-controller a:first-child {
            /*border: 2px solid #157ab5;*/
        }

        .user-controller a:nth-child(2) {
            /*border: 2px solid #EDC772;*/
        }

        .user-controller a:last-child {
            /*border: 2px solid #D9534F;*/
        }

        .ribbon {
            width: 96%;
            height: 100px;
            position: absolute;
            top: 0px;
            left: 12px;
            overflow: hidden;
        }

        .ribbon:before {
            content: "";
            display: block;
            border-radius: 0 5px 5px 0;
            width: 8px;
            height: 37px;
            position: absolute;
            right: 6px;
            top: 10px;
            /* background: rgba(0,0,0,0.35); */
        }

        .ribbon-ing:before {
            /*background: #44b6eb;*/
            background: rgba(68, 182, 235, 0.8);
        }

        .ribbon-end:before {
            /*background: #626262;*/
            background: rgba(98, 98, 98, 0.8);
        }

        .ribbon:after {
            content: "";
            display: block;
            border-radius: 0px 8px 8px 0px;
            width: 8px;
            height: 6px;
            position: absolute;
            right: 6px;
            top: 40px;
            background: rgba(0, 0, 0, 0.35);
        }

        .ribbon-ing:after {
            /*background: #44b6eb;*/
            background: rgba(0, 0, 0, 0.35);
        }

        .ribbon-end:after {
            /*background: #626262;*/
            background: rgba(0, 0, 0, 0.35);

        }

        .ribbon span {
            color: #fff;
            display: inline-block;
            text-align: center;
            width: 100px;
            height: 30px;
            line-height: 30px;
            position: absolute;
            top: 10px;
            right: 14px;
            z-index: 2;
            overflow: hidden;
            /* transform: rotate(45deg); */
            /*-ms-transform: rotate(45deg);*/
            /*-moz-transform: rotate(45deg);*/
            /* -webkit-transform: rotate(45deg); */
            /*-o-transform: rotate(45deg);*/
            /* border: 1px dashed; */
            /*box-shadow: -1px 2px 4px rgba(0,0,0,0.5);*/
        }

        .ribbon-ing span {
            /*box-shadow:0 0 0 3px rgba(255,180,0,0.8);*/
            background: rgba(68, 182, 235, 0.8);
        }

        .ribbon-end span {
            /*box-shadow:0 0 0 3px rgba(98,98,98,0.8);*/
            background: rgba(98, 98, 98, 0.8);
        }

        .id_duration_time .time-number {
            color: red;
            font-size: 16px;
        }

        .pagination {
            margin: 20px 0;
            display: block;
            overflow: hidden;
            width: 100%;
        }

        .warp-detail {
            position: absolute;
            width: 400px;
            height: 500px;
            border: 1px solid #ddd;
            right: 5px;
            display: none;
            background: #F0F0F0;
            border-radius: 3px;
            padding: 15px 0 15px 15px;
        }

        .warp-detail .detail-info {
            height: 85%;
            overflow: auto;
        }

        .warp-detail .detail-info .info-title {
            font-size: 30px;
            height: 40px;
            line-height: 25px;
            color: #44b6eb;
            margin-top: 5%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-bottom: 1px solid #44b6eb;
            padding-left: 10px;
        }

        .warp-detail .detail-info .info-basic {
            margin: 20px 0;
            font-size: 18px;
            color: #924D26;
        }

        .warp-detail .detail-info .info-cycle {
            margin: 20px 0;
            font-size: 18px;
            color: #FFBB00;
        }

        .warp-detail .detail-info .info-item {
            font-size: 15px;
            text-indent: 27px;
            margin: 10px 0;
        }

        .warp-detail .detail-close {
            position: absolute;
            font-size: 27px;
            right: 4%;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #44b6eb;
            text-align: center;
            vertical-align: middle;
            line-height: 37px;
            color: rgba(240, 240, 240, 1);
        }

        .warp-detail .detail-close:hover {
            color: red;
        }

        .warp-detail .detail-controller {
            margin: 10px 30px 0 30px;
            height: 10%;
            padding: 20px 0;
            border-top: 1px solid #ddd;
        }

        .warp-detail .detail-controller a {
            margin: 0 9%;
            padding: 0;
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            text-decoration: none;
            border-radius: 50%;
            font-size: 30px;
            border: 2px solid #44b6eb;
            color: #44b6eb;
        }

        /*语言显示*/
        .m-lang {
            padding: 10px;
        }

        .m-lang label {
            height: 30px;
            width: 80px;
            line-height: 30px;
        }

        .controller-never {
            background-color: #57C8F2;
            color: #57C8F2;
        }

        .controller-never a {
            color: #57C8F2;
        }

        .controller-ing {
            background-color: #44b6eb;
            color: #fff;
        }

        .controller-ing a {
            /*background-color: #44b6eb;
            color: #fff;
            width: 100%;
            border: 0;
            font-size: 28px;
            text-align: left;
            margin-left: -10px;*/
            color: #44b6eb;
        }

        .controller-ing a:hover {
            color: #0f60ad;
        }

        .controller-end {
            background-color: #626262;
            color: #626262;
        }

        .controller-end a {
            color: #626262;
        }

        .ex-wrap {
            width: 100%;
            background-color: #FEB300;
            height: 200px;
            position: relative;
            overflow: hidden;
            border-radius: 1px;
            -webkit-transition: all .3s;
            -moz-transition: all .3s;
            -ms-transition: all .3s;
            transition: all .3s;
        }

        .ex-add-h {
            cursor: pointer;
            background-color: #fff;
            border: 1px solid #44b6eb;
        }

        .ex-wrap:hover {
            -webkit-box-shadow: 3px 4px 8px #ccc;
            -moz-box-shadow: 3px 4px 8px #ccc;
            -ms-box-shadow: 3px 4px 8px #ccc;
            box-shadow: 3px 4px 8px #ccc;
        }

        .ex-add {
            font-size: 80px;
            text-align: center;
            vertical-align: middle;
            position: absolute;
            left: 50%;
            top: 50%;
            -webkit-transform: translate(-50%, -50%);
            -moz-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            color: #666;
        }

        .ex-add-h:hover > .ex-add i {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            transform: rotate(360deg);
            -webkit-transform-origin: 50% 50%;
            -moz-transform-origin: 50% 50%;
            -ms-transform-origin: 50% 50%;
            transform-origin: 50% 50%;
        }

        .ex-add i {
            color: #44b6eb;
            -webkit-transition: all .3s ease-in-out;
            -moz-transition: all .3s ease-in-out;
            -ms-transition: all .3s ease-in-out;
            transition: all .3s ease-in-out;
        }

        .btn-diaocha {
            display: inline-block;
            width: 80px;
            height: 28px;
            line-height: 28px;
            background: #44b6eb;
            color: #fff;
            border-radius: 3px;
            text-align: center;
            font-size: 14px;
            text-decoration: none;
            margin-top: 10px;
        }

        .btn-diaocha:hover {
            text-decoration: none;
            background: #0f60ad;
            color: #fff;
        }
    </style>
@endsection
@section('body')
    <div class="js-check-wrap">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.question.ques_list')}}">问卷管理</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <form class="js-ajax-form" action="" method="post" style="padding: 30px 50px 0px;">
            <div class="user-wrap">
                @foreach($info as $k=>$vo)
                    <div class="col-item">
                        <div class="col-content" style="cursor: pointer;">
                            <!-- 未开始的问卷 -->
                            @if($vo->status==2)
                            @elseif($vo->status==0)
                                <div class="ribbon ribbon-end" onclick="show_info({{$vo['id']}});"><span>未开始</span></div>
                            @else
                                <div class="ribbon ribbon-ing" onclick="show_info({{$vo['id']}});"><span>进行中</span></div>
                            @endif
                            <div class="user-message" onclick="show_info({{$vo['id']}});">
                                <table>
                                    <tr>
                                        <td style="width:10%;"><i class="fa fa-list" title="语言" style="margin-right: 10px;"></i></td>
                                        <td>@if($vo["language"]==1)中文@elseif($vo["language"]==2)英文@endif</td>
                                    </tr>
                                    <tr>
                                        <td style="width:10%;"><i class="fa fa-pencil" title="问卷标题" style="margin-right: 10px;"></i></td>
                                        <td>{{$vo->title}}</td>
                                    </tr>
                                    <tr>
                                        <td><i class="fa fa-bars" title="题目数量" style="margin-right: 10px;"></i></td>
                                        <td>{{$vo->ques_num}}道题目</td>

                                        @if($vo->status==2)
                                            <td class="message-never"><i class="fa fa-file-text-o" title="" style="color: #E9E9E9;top: 25%;right: 15%;font-size: 50px;position: absolute;"></i></td>
                                        @elseif($vo->status==0)
                                            <td class="message-ing"><i class="fa fa-bookmark-o" title="" style="color: #E9E9E9;top: 25%;right: 15%;font-size: 50px;position: absolute;"></i></td>
                                        @else
                                            <td class="message-end"><i class="fa fa-paperclip" title="" style="color: #E9E9E9;top: 25%;right: 15%;font-size: 50px;position: absolute;"></i></td>
                                        @endif

                                    </tr>
                                    <tr>
                                        <td><i class="fa fa-group" title="参与人数" style="margin-right: 10px;"></i></td>
                                        <td>{{$vo->num}}个人参与</td>
                                        <td style="text-align: right">
                                        </td>
                                    </tr>

                                </table>
                            </div>
                            <!-- /.user-message -->
                            <div style="float: right;margin: -70px 15px 0 0">
                                @if($vo->status==0)
                                    <a class="ajaxBtn btn-diaocha" href="javascript:void(0);" uri="{{route('admin.question.ques_status').'?id=' . $vo['id'].'&status=1'}}" msg="确认要开始该调查">开始调查</a>
                                @elseif($vo->status==1)
                                    <a class="ajaxBtn btn-diaocha" href="javascript:void(0);" uri="{{route('admin.question.ques_status').'?id=' . $vo['id'].'&status=0'}}" msg="确认要结束该调查">结束调查</a>
                                @else

                                @endif
                            </div>

                            @if($vo->status==2)
                            <!-- 未开始的问卷 -->
                                <div class="user-controller controller-never">
                                    <a href="javascript:edit_ques({{$vo->id}})"><i class="fa fa-gear" title="编辑问卷" style=""></i></a>
                                    <a href="{{route('admin.question.quesinfo_list').'?id=' . $vo->id.'&title='.$vo->title}}"><i class="fa fa-edit" title="题目管理" style=""></i></a>
                                    <a class="ajaxBtn " href="javascript:void(0);" uri="{{route('admin.question.ques_list').'?del=' . $vo->id}}" msg="确认要删除该问卷"><i class="fa fa-remove" title="删除问卷"
                                                                                                                                                                   style=""></i></a>
                                </div>
                            @elseif($vo->status==0)
                                <div class="user-controller controller-end">
                                    <a href="javascript:edit_ques({{$vo->id}})"><i class="fa fa-gear" title="编辑问卷" style=""></i></a>
                                    <a href="{{route('admin.question.quesinfo_list').'?id=' . $vo->id.'&title='.$vo->title}}"><i class="fa fa-edit" title="题目管理" style=""></i></a>
                                    <a class="ajaxBtn " href="javascript:void(0);" uri="{{route('admin.question.ques_list').'?del=' . $vo->id}}" msg="确认要删除该问卷"><i class="fa fa-remove" title="删除问卷"
                                                                                                                                                                   style=""></i></a>
                                </div>
                        @elseif($vo->status==1)
                            <!-- 进行中的问卷 -->
                                <div class="user-controller controller-ing">
                                    <a href="javascript:edit_ques({{$vo->id}})"><i class="fa fa-gear" title="编辑问卷" style=""></i></a>
                                    <a href="{{route('admin.question.quesinfo_list').'?id=' . $vo->id.'&title='.$vo->title}}"><i class="fa fa-edit" title="题目管理" style=""></i></a>
                                    <a class="ajaxBtn " href="javascript:void(0);" uri="{{route('admin.question.ques_list').'?del=' . $vo->id}}" msg="确认要删除该问卷"><i class="fa fa-remove" title="删除问卷"
                                                                                                                                                                   style=""></i></a>
                                </div>
                        @endif

                        <!-- /.user-controller -->
                            <!-- 右侧下方显示 按钮-->
                            <div style="display: none" id="user-controller{{$vo->id}}">

                                @if($vo->status==2)
                                    <a href="javascript:edit_ques({{$vo->id}})"><i class="fa fa-gear" title="编辑问卷" style=""></i></a>
                                    <a href="{{route('admin.question.quesinfo_list').'?id=' . $vo->id.'&title='.$vo->title}}"><i class="fa fa-edit" title="题目管理" style=""></i></a>
                                    <a href="{{route('admin.question.ques_info').'?id=' . $vo->id}}"><i class="fa fa-line-chart" title="统计结果" style=""></i></a>
                                @elseif($vo->status==0)
                                    <a href="{{route('admin.question.quesinfo_list').'?id=' . $vo->id.'&title='.$vo->title}}"><i class="fa fa-edit" title="题目管理" style=""></i></a>
                                    <a href="{{route('admin.question.ques_info').'?id=' . $vo->id}}"><i class="fa fa-line-chart" title="统计结果" style=""></i></a>
                                    <a href="javascript:export_ques({{$vo->id}})"><i class="fa fa-download" title="导出报表" style=""></i></a>
                                @elseif($vo->status==1)
                                    <a href="{{route('admin.question.quesinfo_list').'?id=' . $vo->id.'&title='.$vo->title}}"><i class="fa fa-edit" title="题目管理" style=""></i></a>
                                    <a href="{{route('admin.question.ques_info').'?id=' . $vo->id}}"><i class="fa fa-line-chart" title="统计结果" style=""></i></a>
                                    <a href="javascript:export_ques({{$vo->id}})"><i class="fa fa-download" title="导出报表" style=""></i></a>
                                @endif

                            </div>
                        </div>
                    </div>
                    <!-- /.col-item -->
                @endforeach
                <div class="col-item" style="width: 333px;height:210px;">
                    <div class="ex-wrap ex-add-h" onclick="edit_ques('add')">
                        <div class="ex-add">
                            <i class="fa fa-plus"></i>
                        </div>
                    </div>
                </div>
                <!-- /.col-item -->
                <div class="warp-detail" style="display: none">
                    <div class="detail-close">
                        <i class="fa fa-close close-detail" title="关闭" style="cursor: pointer;"></i>
                    </div>
                    <div class="detail-info">
                        <div class="info-title">
                            <i class="fa fa-pencil" title="问卷标题" style="margin-right: 10px;"></i><span id="ajax_title">问卷标题</span>
                        </div>
                        <div class="info-basic">
                            <i class="fa fa-circle" title="基本信息" style="margin-right: 10px;"></i>基本信息
                        </div>
                        <div class="info-item">
                            <span class="item-label">计划描述：</span>
                            <p class="item-value" id="ajax_description" style="text-indent: 0;padding-left: 106px;margin-top: -5%;">描述内容</p>
                        </div>

                        <div class="info-item">
                            <span class="item-label">题目数量：</span>
                            <span class="item-value" id="ajax_ques_num">5 题</span>
                        </div>
                        <div class="info-item">
                            <span class="item-label">编辑时间：</span>
                            <span class="item-value" id="ajax_datetime">2016年5月6日</span>
                        </div>
                        <div class="info-item">
                            <span class="item-label">编辑人员：</span>
                            <span class="item-value" id="ajax_user_login">李二小</span>
                        </div>
                        <div class="info-item">
                            <span class="item-label">问卷状态：</span>
                            <span class="item-value" id="ajax_status">未开始</span>
                        </div>
                        <div class="info-item">
                            <span class="item-label">参与人数：</span>
                            <span class="item-value" id="ajax_num">50人</span>
                        </div>

                    </div>
                    <!-- /.detail-info -->
                    <div class="detail-controller" id="ajax_detail-controller">

                    </div>
                    <!-- /.detail-controller -->
                </div>
                <!-- /.warp-detail -->
            </div>
            <!-- /.user-wrap -->
            <div class="row recordpage">
                <div class="col-sm-12">
                    {!! $info->links() !!}
                   {{-- <span>共 {{ $info->total() }} 条记录</span>--}}
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script>
        layui.use('element', function () {
            var $ = layui.jquery
                , element = layui.element(); //Tab的切换功能，切换事件监听等，需要依赖element模块
        });

        function edit_ques(id) {

            var url = "{{route('admin.question.edit_ques')}}?id=" + id;
            if (id == 'add') {
                var t = "新增问卷";
            }
            else {
                var t = "编辑问卷";
            }
            layer.open({
                title: t,
                type: 2,
                area: ['1000px', '550px'],
                fix: true, //固定
                maxmin: true,
                move: false,
                content: url
            });
        }
    </script>
    <script type="text/javascript">
        $(function () {
            // 从右向左渐进实现
            jQuery.fn.slideRightHide = function (speed, callback) {
                this.animate({
                    width: "hide",
                    paddingLeft: "hide",
                    paddingRight: "hide",
                    marginLeft: "hide",
                    marginRight: "hide"
                }, speed, callback);
            };
            jQuery.fn.slideRightShow = function (speed, callback) {
                this.animate({
                    width: "show",
                    paddingLeft: "show",
                    paddingRight: "show",
                    marginLeft: "show",
                    marginRight: "show"
                }, speed, callback);
            };

            //关闭问卷详情
            $('.close-detail').click(function () {
                $('.warp-detail').slideRightHide(500, function () {
                    $('.warp-detail').find("[class='user-controller']").fadeOut(300);
                    $('.user-wrap').css('width', '100%');
                });
            });
        });

        // 显示问卷详情
        function show_info(id) {
            $.post("{{route('admin.question.ajax_ques')}}", {
                    id: id
                },
                function (data) {
                    if (data.status == "false") {
                        layer.msg(data.msg, {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
                    }
                    else {
                        $("#ajax_title").html(data.title);//问卷标题
                        $("#ajax_description").html(data.description);//标题描述
                        $("#ajax_ques_num").html(data.ques_num);//题目数量
                        $("#ajax_datetime").html(data.date_time);//编辑时间
                        $("#ajax_user_login").html(data.user_login);//编辑人员
                        $("#ajax_status").html(data.status);//状态
                        $("#ajax_num").html(data.num);//参与人数
                        $("#ajax_detail-controller").html($("#user-controller" + id).html());//下方按钮
                        $('.warp-detail').slideRightShow(500, function () {
                            $('.warp-detail').find("[class='user-controller']").fadeIn(300);
                            $('.user-wrap').css('width', '70%');
                        });
                    }
                })
        }

        //导出问卷结果
        function export_ques(id) {
            window.location = "{{route('admin.question.ques_export')}}?id=" + id;
        }
    </script>

@endsection