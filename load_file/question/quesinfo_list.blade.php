@extends('layouts.public')
@section('head')
    <style>
        .itra-wrap {
            min-width: 1300px;
        }

        .itra-wrap:after {
            content: '';
            display: both;
            clear: both;
        }

        .itra-list {
            float: left;
            width: 80%;
            margin-top: 20px;
            min-width: 780px;
            padding: 0 0 0 5%;
        }

        .itra-statistics {
            float: left;
            width: 30%;
            margin-left: 5%;
            margin-top: 50px;
        }

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
            border-bottom: 2px solid #44b6eb;
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
            border-bottom: 2px solid #44b6eb;
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
            color: #44b6eb;
        }

        .pages_next {
            margin-left: -4px;
        }

        .survey_main {
            /*height: 600px;*/
            width: 96%;
            border: 1px solid #44b6eb;
            position: relative;
        }

        .survey_main .survey_container {
            height: 80%;
            padding: 80px;
            margin: 0 auto;
        }

        .form-container {
            text-align: center;
            font-size: 20px;
        }

        .form-container .title-label {
            display: inline-block;
            font-size: 26px;
            position: relative;
            margin-right: 2%;
        }

        .form-container .title-label:before {
            content: "";
            background-color: #44b6eb;
            width: 14px;
            height: 14px;
            font-size: 20px;
            position: absolute;
            top: 15%;
            left: -20%;
        }

        .form-container input {
            /*height: 30px;*/
            width: 60%;
            display: inline-block;
            margin: 10px 0;
        }

        .form-container .controls-label {
            margin: 20px 0;
            font-size: 26px;
            text-align: left;
            position: relative;
            padding-left: 23%;
        }

        .form-container .controls-label:before {
            content: "";
            background-color: #44b6eb;
            width: 14px;
            height: 14px;
            font-size: 20px;
            position: absolute;
            top: 15%;
            left: 21.2%;
        }

        .form-container p {
            text-indent: 7%;
        }

        .page-actions {
            position: absolute;
            bottom: 1%;
            left: 20%;
            width: 60%;
            border-top: 1px solid #e5e5e5;
            padding: 20px 20px 20px;
        }

        .page-actions a {
            margin: 0px 45%;
            padding: 0;
            display: inline-block;
            width: 50px;
            height: 50px;
            line-height: 48px;
            text-align: center;
            text-decoration: none;
            border-radius: 50%;
            font-size: 34px;
            border: 2px solid #157ab5;
        }

        .hidden_nav {
            background-color: #F0EFEF;
            width: 96.2%;
            position: relative;
        }

        /*最新问卷添加题目样式*/
        .input-wrap {
            width: 140px;
            float: left;
        }

        .input-wrap .ques_leixing {
            width: 20px;
            height: 20px;
        }

        .tixing_content {
            margin: 40px auto;
            border: 1px dotted #aaa;
            padding: 50px;
            display: none;
        }

        .tixing_content.show {
            display: block;
        }

        .tixing_content .ques_title {
            display: inline-block;
            font-size: 18px;
            margin: 10px 20px 10px 0;
            /*vertical-align: top;*/
        }

        .tixing_content .answer_title {
            display: inline-block;
            font-size: 16px;
            margin-right: 20px;
            margin-left: 37px;
        }

        .ques_btn_action {
            margin-top: 20px;
        }

        .ques_btn_action .ques_btn {
            width: 120px;
            height: 40px;
            margin: 0 30px;
            color: #fff;
            background: #44b6eb;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            line-height: 40px;
        }

        .ques_btn_action .ques_btn:hover {
            background: #0f60ad;
        }

        .ques_btn_action .add_input {
            margin-left: 120px;
        }

        .ques_btn_action .add_qita {

        }

        .ques_btn_action .add_bumanyi {

        }

        .option {
            position: relative;
        }

        .option .fa-remove {
            position: absolute;
            top: 15px;
            right: 9%;
            cursor: pointer;
        }

        .ques_radio {
            display: table;
            margin: auto;
        }
    </style>
@endsection
@section('body')
    <body onload="ajax_ques();">
    <div class="js-check-wrap">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="{{route('admin.question.ques_list')}}">问卷管理</a></li>
                        <li class="active"><a href="{{route('admin.question.quesinfo_list').'?id='.$ques_id.'&title='.$title}}">{{$title}}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="itra-wrap">
            <div class="itra-list">
                <div class="survey_pages_tab hidden_nav">
                    <a class="pages_preview" href="javascript:;"><i class="fa fa-chevron-left" title="" style=""></i></a>
                    <div class="pages_wrap">
                        <!-- 页码容器 -->
                        <ul id="ajax_li"></ul>
                    </div>
                    <a class="pages_next" href="javascript:;"><i class="fa fa-chevron-right" title="" style=""></i></a>
                    <a class="pages_more"
                       style="text-decoration: none;border: 2px solid #44b6eb;width: 30px;height: 30px;text-align: center;font-size: 33px;color: #44b6eb;line-height: 24px;padding-right: 2px;"
                       href="javascript:;">+</a>
                    <span style="    margin-left: 1%;font-size: 20px;top: 28%;position: absolute;color: #44b6eb;"></span>
                </div>
                <div class="survey_main">
                    <form method="post" class="form-container" id="myform" action="{{route('admin.question.edit_quesinfo')}}">
                        <div class="survey_container" id="ajax_page">
                        </div>
                        <input type="hidden" name="ques_id" value="{{$ques_id}}"/>
                        <input type="hidden" name="ques_count" id="ques_count" value="0"/>
                        <div class="btn-div">
                            <input type="button" id="ajax_submit" class="btn btn-primary" style="position: absolute;width: 100px;height: 40px;right: 2%;bottom: 4%;" value="保存"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('script')
        <script>
            //ajax加载所有题目
            function ajax_ques() {
                //获取服务器数据

                $.post("{{route('admin.question.ajax_quesinfo')}}", {
                        ques_id: "{{$ques_id}}"
                    },
                    function (data) {
                        show_quesinfo(data);
                    })
            }

            //显示所有题目数据
            function show_quesinfo(data) {
                console.log(data);
                var count = data.count;
                $("#ques_count").val(count);
                var ajax_li = '';
                var ajax_page = '';
                //封面
                ajax_li = ajax_li + '<li class="pages_item current" onclick=change_page("p-start") data-pid="p-start"><span class="pages_show">封面页</span></li>';
                ajax_page = ajax_page + '<div class="page" id="p-start" style="display: block;"><h1 class="title_content" style="position: relative;text-align: center;margin-bottom:80px;">{{$title}}</h1><p style="font-size: 18px;">为了给您提供更好的服务，希望您能抽出几分钟时间，将您的感受和建议告诉我们，我们非常重视每位用户的宝贵意见，期待您的参与！现在我们就马上开始吧！</p></div>';

                //题目内容
                for (var i = 1; i <= count; i++) {
                    ajax_li = ajax_li + '<li class="pages_item" onclick=change_page("p-' + i + '") data-pid="p-' + i + '"><span class="pages_show">第' + i + '题</span>&nbsp;&nbsp;<a class="pages_remove" onclick="del_ques(' + i + ')" >×</a></li>';
                    ajax_page = ajax_page + '<div class="page" id="p-' + i + '" style="position:relative;display: none;">';
                    ajax_page += '<div class="ques_radio clearfix text-center">'
                    if (data['infolist'][i - 1]['type'] == 1) {
                        ajax_page = ajax_page + '<div  class="input-wrap"><input type="radio" id="d_type' + i + '" class="ques_leixing" value="1" checked name="type' + i + '"><span class="input-tag"></span>单选题</div>';
                    }
                    else {
                        ajax_page = ajax_page + '<div class="input-wrap"><input type="radio" id="d_type' + i + '" class="ques_leixing" value="1" name="type' + i + '"><span class="input-tag"></span>单选题</div>';
                    }
                    //注释掉多选题和问答题模式
                    if (data['infolist'][i - 1]['type'] == 2) {
                        ajax_page = ajax_page + '<div  class="input-wrap"><input type="radio" id="d_type2' + i + '" class="ques_leixing" value="2" checked name="type' + i + '"><span class="input-tag"></span>多选题</div>';
                    }
                    else {
                        ajax_page = ajax_page + '<div  class="input-wrap"><input type="radio" id="d_type2' + i + '" class="ques_leixing" value="2" name="type' + i + '"><span class="input-tag"></span>多选题</div>';
                    }
//            if(data['infolist'][i-1]['type']==3){
//                ajax_page=ajax_page+'<div class="input-wrap"><input type="radio" id="d_type3'+i+'" class="ques_leixing" value="3" checked name="type'+i+'"><span class="input-tag"></span>问答题</div>';
//            }
//            else{
//                ajax_page=ajax_page+'<div  class="input-wrap"><input type="radio" id="d_type3'+i+'" class="ques_leixing" value="3" name="type'+i+'"><span class="input-tag"></span>问答题</div>';
//            }
                    ajax_page += '</div>';
                    //单选题
                    if (data['infolist'][i - 1]['type'] == 1) {
                        ajax_page = ajax_page + '<div class="tixing_content danxuan show">';
                        ajax_page = ajax_page + '<div><label class="ques_title">题目标题:</label><input type="text" class="form-control" value="' + data['infolist'][i - 1]["question"] + '" name="r_question' + i + '"></div>';
                        var option_count = data['infolist'][i - 1]['option_count'];
                        for (var a = 1; a <= option_count; a++) {
                            if (data['infolist'][i - 1]['option_info'][a - 1]['option_type'] == 1) {
                                //普通选项
                                ajax_page = ajax_page + '<div class="option option_normal"><label class="answer_title">选项' + a + ':</label><input type="text" class="form-control" name="r_question' + i + 'option[]" value="' + data['infolist'][i - 1]['option_info'][a - 1]['option_info'] + '">';

                                if (a >= 3) {
                                    ajax_page += '<i class="fa fa-remove del-normal" _id="' + i + '" title="删除此项"></i>';
                                    ajax_page += '</div>';
                                } else {
                                    ajax_page += '</div>';
                                }
                            }
                            else if (data['infolist'][i - 1]['option_info'][a - 1]['option_type'] == 2) {
                                //文本编辑选项
                                ajax_page = ajax_page + '<div class="option option_wenben"><label class="answer_title">选项' + a + ':</label><input type="text" class="form-control" readonly name="r_question' + i + 'option[]" value="' + data['infolist'][i - 1]['option_info'][a - 1]['option_info'] + '"><i class="fa fa-remove del-wenben" _id="' + i + '" title="删除此项"></i></div>';
                            }
                        }
                        ajax_page += '<div class="ques_btn_action">';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_input" _id="' + i + '">新增选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_qita" _id="' + i + '">新增其他选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_bumanyi" _id="' + i + '">新增不满意选项</a>';
                        ajax_page += '</div>';
                        ajax_page += '</div>';
                    }
                    else {
                        ajax_page = ajax_page + '<div class="tixing_content danxuan">';
                        ajax_page = ajax_page + '<div><label class="ques_title">题目标题:</label><input type="text" class="form-control" value="" name="r_question' + i + '"></div>';
                        ajax_page = ajax_page + '<div class="option option_normal"><label class="answer_title">选项' + 1 + ':</label><input type="text" class="form-control" name="r_question' + i + 'option[]" value=""></div>';
                        ajax_page = ajax_page + '<div class="option option_normal"><label class="answer_title">选项' + 2 + ':</label><input type="text" class="form-control" name="r_question' + i + 'option[]" value=""></div>';
                        ajax_page += '<div class="ques_btn_action">';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_input" _id="' + i + '">新增选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_qita" _id="' + i + '">新增其他选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_bumanyi" _id="' + i + '">新增不满意选项</a>';
                        ajax_page += '</div>';

                        ajax_page = ajax_page + '</div>';
                    }

                    //多选题
                    if (data['infolist'][i - 1]['type'] == 2) {
                        ajax_page = ajax_page + '<div class="tixing_content duoxuan show">';
                        ajax_page = ajax_page + '<div><label class="ques_title">题目标题:</label><input type="text" class="form-control" value="' + data['infolist'][i - 1]["question"] + '" name="c_question' + i + '"></div>';
                        var option_count = data['infolist'][i - 1]['option_count'];
                        for (var a = 1; a <= option_count; a++) {
                            if (data['infolist'][i - 1]['option_info'][a - 1]['option_type'] == 1) {
                                //普通选项
                                ajax_page = ajax_page + '<div class="option option_normal"><label class="answer_title">选项' + a + ':</label><input type="text" class="form-control" name="c_question' + i + 'option[]" value="' + data['infolist'][i - 1]['option_info'][a - 1]['option_info'] + '">';
                                if (a >= 3) {
                                    ajax_page += '<i class="fa fa-remove del-normal" _id="' + i + '" title="删除此项"></i>';
                                    ajax_page += '</div>';
                                } else {
                                    ajax_page += '</div>';
                                }
                            }
                            else if (data['infolist'][i - 1]['option_info'][a - 1]['option_type'] == 2) {
                                //文本编辑选项
                                ajax_page = ajax_page + '<div class="option option_wenben"><label class="answer_title">选项' + a + ':</label><input type="text" class="form-control" readonly name="c_question' + i + 'option[]" value="' + data['infolist'][i - 1]['option_info'][a - 1]['option_info'] + '"><i class="fa fa-remove del-wenben" _id="' + i + '" title="删除此项"></i></div>';
                            }
                        }
                        ajax_page += '<div class="ques_btn_action">';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_input" _id="' + i + '">新增选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_qita" _id="' + i + '">新增其他选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_bumanyi" _id="' + i + '">新增不满意选项</a>';
                        ajax_page += '</div>';

                        ajax_page = ajax_page + '</div>';
                    }
                    else {
                        ajax_page = ajax_page + '<div class="tixing_content duoxuan">';
                        ajax_page = ajax_page + '<div><label class="ques_title">题目标题:</label><input type="text" class="form-control" value="" name="c_question' + i + '"></div>';
                        ajax_page = ajax_page + '<div class="option option_normal"><label class="answer_title">选项' + 1 + ':</label><input type="text" class="form-control" name="c_question' + i + 'option[]" value=""></div>';
                        ajax_page = ajax_page + '<div class="option option_normal"><label class="answer_title">选项' + 2 + ':</label><input type="text" class="form-control" name="c_question' + i + 'option[]" value=""></div>';

                        ajax_page += '<div class="ques_btn_action">';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_input" _id="' + i + '">新增选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_qita" _id="' + i + '">新增其他选项</a>';
                        ajax_page += '<a href="javascript:;" class="ques_btn add_bumanyi" _id="' + i + '">新增不满意选项</a>';
                        ajax_page += '</div>';

                        ajax_page = ajax_page + '</div>';
                    }

                    //问答题
                    /*
                    if(data['infolist'][i-1]['type']==3){
                        ajax_page = ajax_page + '<div class="tixing_content wenda show">';
                        ajax_page=ajax_page+'<div><label class="ques_title">题目标题:</label><textarea style="width:80%;height:200px;resize:none;" name="t_question'+i+'">'+data['infolist'][i-1]["question"]+'</textarea></div>';
                        ajax_page = ajax_page + '</div>';
                    }
                    else{
                        ajax_page = ajax_page + '<div class="tixing_content wenda">';
                        ajax_page=ajax_page+'<div><label class="ques_title">题目标题:</label><textarea style="width:80%;height:200px;resize:none;" name="t_question'+i+'"></textarea></div>';
                        ajax_page = ajax_page + '</div>';
                    }
                    */
                    ajax_page = ajax_page + '</div>';
                }

                //结束页
                ajax_li = ajax_li + '<li class="pages_item" onclick=change_page("p-end") data-pid="p-end"><span class="pages_show">结束页</span></li>';
                ajax_page = ajax_page + '<div class="page" id="p-end" style="display: none;text-align: center;"><h1 style="position: relative;margin-bottom:80px;"><i class="fa fa-hourglass-end" title="问卷结束" style="color: #44b6eb;margin-right: 10px;font-size: 120px;"></i></h1><p style="font-size: 26px;">问卷到此结束，感谢您的参与！</p></div>';
                $("#ajax_li").html(ajax_li);
                $("#ajax_page").html(ajax_page);
                // 初始化页面滚到最右侧
                $('.pages_wrap').scrollLeft($('.pages_wrap').scrollLeft() + window.screen.width);
                change_page("p-" + count);
                checkQita();
                initFunction();
            }

            // 新增选项、新增编辑选项
            function initFunction() {
                $('.add_input').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var that = $(this);
                    var ques_id = that.attr('_id');
                    var rightDiv = $('#p-' + ques_id).children('.show');
                    var normalIndex = $(rightDiv).find('.option').length;
                    var lastNormal = $($(rightDiv).find('.option')[normalIndex - 1]);
                    if ($('#p-' + ques_id + ' .show').hasClass('danxuan')) {
                        lastNormal.after('<div class="option option_normal"><label class="answer_title">选项' + (normalIndex + 1) + ':</label><input type="text" class="form-control" name="r_question' + ques_id + 'option[]" value=""><i class="fa fa-remove del-mormal" _id="' + ques_id + '" title="删除此项"></i></div>');
                    } else {
                        lastNormal.after('<div class="option option_normal"><label class="answer_title">选项' + (normalIndex + 1) + ':</label><input type="text" class="form-control" name="c_question' + ques_id + 'option[]" value=""><i class="fa fa-remove del-mormal" _id="' + ques_id + '" title="删除此项"></i></div>');
                    }


                    initDelete();
                });

                $('.add_qita').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var that = $(this);
                    var ques_id = that.attr('_id');
                    var rightDiv = $('#p-' + ques_id).children('.show');
                    var normalIndex = $(rightDiv).find('.option').length;
                    var lastNormal = $($(rightDiv).find('.option')[normalIndex - 1]);
                    if ($('#p-' + ques_id + ' .show').hasClass('danxuan')) {
                        lastNormal.after('<div class="option option_wenben"><label class="answer_title">选项' + (normalIndex + 1) + ':</label><input type="text" class="form-control" readonly name="r_question' + ques_id + 'option[]" value="其他____________"><i class="fa fa-remove del-wenben" _id="' + ques_id + '" title="删除此项"></i></div>');
                    } else {
                        lastNormal.after('<div class="option option_wenben"><label class="answer_title">选项' + (normalIndex + 1) + ':</label><input type="text" class="form-control" readonly name="c_question' + ques_id + 'option[]" value="其他____________"><i class="fa fa-remove del-wenben" _id="' + ques_id + '" title="删除此项"></i></div>');
                    }

                    checkQita();
                    initDelete();
                });
                $('.add_bumanyi').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var that = $(this);
                    var ques_id = that.attr('_id');
                    var rightDiv = $('#p-' + ques_id).children('.show');
                    var normalIndex = $(rightDiv).find('.option').length;
                    var lastNormal = $($(rightDiv).find('.option')[normalIndex - 1]);
                    if ($('#p-' + ques_id + ' .show').hasClass('danxuan')) {
                        lastNormal.after('<div class="option option_wenben"><label class="answer_title">选项' + (normalIndex + 1) + ':</label><input type="text" class="form-control" readonly name="r_question' + ques_id + 'option[]" value="不满意(请注明原因)____________"><i class="fa fa-remove del-wenben" _id="' + ques_id + '" title="删除此项"></i></div>');
                    } else {
                        lastNormal.after('<div class="option option_wenben"><label class="answer_title">选项' + (normalIndex + 1) + ':</label><input type="text" class="form-control" readonly name="c_question' + ques_id + 'option[]" value="不满意(请注明原因)____________"><i class="fa fa-remove del-wenben" _id="' + ques_id + '" title="删除此项"></i></div>');
                    }

                    checkQita();
                    initDelete();
                });

                $('.input-wrap input[type="radio"]').click(function () {
                    var that = $(this);
                    var ques_id = that.attr('name').split('type')[1];
                    if (that.val() == 1) {
                        $('#p-' + ques_id + ' .danxuan').addClass('show').siblings('.tixing_content').removeClass('show');
                    } else if (that.val() == 2) {
                        $('#p-' + ques_id + ' .duoxuan').addClass('show').siblings('.tixing_content').removeClass('show');
                    } else {
                        $('#p-' + ques_id + ' .wenda').addClass('show').siblings('.tixing_content').removeClass('show');
                    }
                });
                initDelete();
            }

            function initDelete() {
                // 删除选项
                $('.option .fa-remove').click(function () {
                    var that = $(this);
                    that.parent().remove();
                    if (that.hasClass('del-wenben')) {
                        var ques_id = that.attr('_id');
                        var rightDiv = $('#p-' + ques_id).children('.show');
                        $(rightDiv).find('.add_qita').show();
                        $(rightDiv).find('.add_bumanyi').show();
                        checkQita();
                    }
                });
            }

            // 检查新增编辑选项是否可用
            function checkQita() {
                var checkDiv = $('.tixing_content');
                for (var i = 0; i < checkDiv.length; i++) {
                    (function (item) {
                        var flag = $(item).find('.option_wenben');
                        if (flag.length != 0) {
                            $(item).find('.add_qita').hide();
                            $(item).find('.add_bumanyi').hide();
                        } else {
                            $(item).find('.add_qita').show();
                            $(item).find('.add_bumanyi').show();
                        }
                    })(checkDiv[i]);
                }
                ;
            }

            //切换内容
            function change_page(id) {
                $('.pages_item').siblings("[data-pid='" + id + "']").addClass('current').siblings().removeClass('current');
                if (id != 'p-0') {
                    $('.survey_container .page').css('display', 'none');
                    $("#" + id).show();
                }
            }

            //删除题目
            function del_ques(id) {
                layer.alert('确定要删除吗？(保存后生效)', {
                    time: 0 //不自动关闭
                    , btn: ['确定', '取消']
                    , yes: function (index) {
                        layer.close(index);
                        layer.msg('加载中', {icon: 16, scrollbar: false, shade: [0.3, '#393D49'], time: 0});

                        var del_url = "{{route('admin.question.ajax_forminfo')}}?type=del&del_id=" + id;
                        $.post(del_url, $('#myform').serialize(),
                            function (data) {
                                layer.closeAll();
                                show_quesinfo(data);
                            })
                    }
                });
            }
        </script>
        <script type="text/javascript">
            $(function () {
                // 新增题目
                $('.pages_more').click(function () {
                    layer.msg('加载中', {icon: 16, scrollbar: false, shade: [0.3, '#393D49'], time: 0});
                    $.post("{{route('admin.question.ajax_forminfo')}}?type=add", $('#myform').serialize(),
                        function (data) {
                            layer.closeAll();
                            show_quesinfo(data);
                        })
                });
                $('.pages_next').click(function () {
                    $('.pages_wrap').scrollLeft($('.pages_wrap').scrollLeft() + 98);
                });
                $('.pages_preview').click(function () {
                    $('.pages_wrap').scrollLeft($('.pages_wrap').scrollLeft() - 98);
                });
            })

            $(function () {
                $("#ajax_submit").click(function () {
                    layer.msg('加载中', {icon: 16, scrollbar: false, shade: [0.3, '#393D49'], time: 0});
                    var ajax_url = $('#myform').attr('action');
                    $.post(ajax_url, $('#myform').serialize(),
                        function (data) {
                            layer.closeAll();
                            if (data.status == 'true') {
                                layer.msg(data.info, {icon: 6, scrollbar: false, time: 1000, shade: [0.3, '#393D49']});
                                setTimeout(function () {
                                    reloadPage(window);
                                }, 1000);
                            }
                            else {
                                layer.msg(data.info, {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
                            }
                        })
                })
            })
        </script>
@endsection
