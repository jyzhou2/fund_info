@extends('api.common.public')

@section('title'){{$arr['title']}}@endsection

@section('head')
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script type="text/javascript">
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <style type="text/css">
        .bg {
            background: url("{{cdn('img/html/web_bg.png')}}") no-repeat fixed;
            background-size: 100% 100%;
        }

        .content .intro {
            height: 25%;
            padding: 0 5%;
            margin: 20px 0;
            font-size: 1rem;
            line-height: 1.4;
            /*font-size: 2.6vh;*/
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
            overflow-y: scroll;
            text-align: justify;
        }

        /**max width 320px**/
        @media screen and (max-width: 320px) {
            .content .intro {
                font-size: 0.9rem;
            }
        }

        .content .intro p {
            margin: 0;
            color: #fff;
        }

        .content form {
            width: 85%;
            height: 55%;
            margin: 0 auto;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
            /*overflow: hidden;*/
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }

        .content form .ques_content {
            width: 100%;
            height: 92%;
            border: none;
            font-size: 1rem;
            padding: 15px;
            overflow-x: hidden;
            overflow-y: auto;
            background: #fff;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
            opacity: .9;
            border-radius: 10px;
        }

        .content form .ques_content > div.ques_item {
            width: 100%;
            /*height: 100%;*/
            display: none;
            max-height: 90%;
        }

        .content form .ques_content > div.ques_item .ques_rate {
            font-size: 1.2rem;
            color: #7d7d7d;
            margin-bottom: 1rem;
        }

        .content form .ques_content > div.ques_item h1 {
            font-size: 1rem;
            line-height: 1.4;
            font-weight: normal;
        }

        .content form .ques_content > div.ques_item.current {
            display: block;
        }

        .answer_content {
            font-size: 1rem;
            margin: 1rem auto;
            width: 80%;
            max-height: 90%;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .content form .action_btn {
            position: absolute;
            width: 100%;
            height: 40px;
            left: 0;
            bottom: -40px;
            text-align: center;
        }

        .content form .action_btn .btn {
            color: #fff;
            font-size: 1.1rem;
            width: 150px;
            max-width: 40%;
            height: 35px;
            background: url("{{cdn('img/html/btn_web.png')}}") no-repeat center;
            background-size: 100% 100%;
            border: 0;
            float: left;
            display: none;
        }

        .content form .action_btn .btn-next, .content form .action_btn .btn-next .btn-submit {
            float: right;
        }

        .content form .action_btn .btn.show {
            display: inline-block;
            margin: 0 5%;
        }

        .content form textarea:focus, .content form .action_btn .btn:focus, .content form .action_btn .btn:active {
            outline: none;
        }

        /* radio and checkbox */
        .input-wrap {
            position: relative;
            width: 90%;
            line-height: 2rem;
            padding-left: 25px;
            margin-left: 2%;
            padding-top: 1px;
            padding-bottom: 1px;
        }

        @media screen and (max-width: 320px) {
            .input-wrap {
                width: 91%;
            }
        }

        input[type="radio"] {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 20px;
            z-index: 10;
            opacity: 0;
            filter: alpha(opacity=0);
        }

        input[type="radio"] + span {
            position: absolute;
            left: 3px;
            top: 11px;
            display: block;
            border: 1px solid transparent;
            background: #a2a2a2;
            border-radius: 15px;
            width: 10px;
            height: 10px;
            z-index: 0;
        }

        input[type="radio"]:hover + span {
        }

        input[type="radio"]:checked + span {
            left: 0;
            top: 7px;
            width: 16px;
            height: 16px;
            border: 1px solid #ffa749;
            background: transparent;
        }

        input[type="radio"]:checked + span:after {
            content: '';
            display: block;
            width: 10px;
            height: 10px;
            margin-top: 3px;
            margin-left: 3px;
            background-color: #ffa749;
            border-radius: 50%;
        }

        input[type="checkbox"] {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 20px;
            z-index: 10;
            opacity: 0;
            filter: alpha(opacity=0);
        }

        input[type="radio"].default,
        input[type="checkbox"].default {
            width: 15px;
            height: 15px;
            opacity: 1;
            filter: alpha(opacity=1);
        }

        input[type="checkbox"] + span {
            position: absolute;
            left: 0;
            top: 0.5rem;
            display: block;
            border: 1px solid #25C1FA;
            background: #fff;
            /*border-radius: 5px;*/
            width: 16px;
            height: 16px;
            z-index: 0;
        }

        input[type="checkbox"]:hover + span,
        input[type="checkbox"]:checked + span {
            /*border: 1px solid #5D9CEC;*/
        }

        input[type="checkbox"]:checked + span:after {
            content: '';
            display: block;
            width: 8px;
            height: 4px;
            border-left: 3px solid #5D9CEC;
            border-bottom: 3px solid #5D9CEC;
            transform: rotate(-45deg);
            margin-left: 3px;
            margin-top: 4px;
        }

        input[type="button"], button {
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            -webkit-appearance: none;
        }

        input[type="text"] {
            display: block;
            width: 80%;
            height: 28px;
            padding: 3px;
            border: 1px solid #e2e2e2;
            -webkit-appearance: none;
        }

        /*input[type="text"] + input[type="radio"], input[type="text"] + input[type="checkbox"]{
            height: 0;
        }
        input[type="text"] + input[type="radio"] + .input-tag, input[type="text"] + input[type="checkbox"] + .input-tag{
            display: none;
        }*/

        .moveToLeft {
            -webkit-animation: moveToLeft .6s ease both;
            -moz-animation: moveToLeft .6s ease both;
            animation: moveToLeft .6s ease both;
        }

        .moveFromRight {
            -webkit-animation: moveFromRight .6s ease both;
            -moz-animation: moveFromRight .6s ease both;
            animation: moveFromRight .6s ease both;
        }

        /*翻页动画效果*/
        /*左出*/
        @-webkit-keyframes moveToLeft {
            to {
                -webkit-transform: translateX(-100%);
            }
        }

        @-moz-keyframes moveToLeft {
            to {
                -moz-transform: translateX(-100%);
            }
        }

        @keyframes moveToLeft {
            to {
                transform: translateX(-100%);
            }
        }

        /*右进*/
        @-webkit-keyframes moveFromRight {
            from {
                -webkit-transform: translateX(100%);
            }
        }

        @-moz-keyframes moveFromRight {
            from {
                -moz-transform: translateX(100%);
            }
        }

        @keyframes moveFromRight {
            from {
                transform: translateX(100%);
            }
        }

        /*左进*/
        @-webkit-keyframes moveFromLeft {
            from {
                -webkit-transform: translateX(-100%);
            }
        }

        @-moz-keyframes moveFromLeft {
            from {
                -moz-transform: translateX(-100%);
            }
        }

        @keyframes moveFromLeft {
            from {
                transform: translateX(-100%);
            }
        }

        /*右出*/
        @-webkit-keyframes moveToRight {
            to {
                -webkit-transform: translateX(100%);
            }
        }

        @-moz-keyframes moveToRight {
            to {
                -moz-transform: translateX(100%);
            }
        }

        @keyframes moveToRight {
            to {
                transform: translateX(100%);
            }
        }

        ::-webkit-scrollbar {
            -webkit-appearance: none;
            width: 0;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0);
            -webkit-box-shadow: 0 0 1px rgba(255, 255, 255, 0);
        }

    </style>
@endsection

@section('body')
    <div class="content">
        <input type="hidden" id="order-p" value="{{$p}}">
        <div class="intro">
            <p>{{$arr['a']}}：</p>
            <p>{{$arr['b']}}</p>
        </div>
        <form id="myform" action="{{url('/api/question/quesinfo?p='.$p)}}">
            <div class="ques_content">
				<?php
				$ques_arr = [
					'1' => $arr['c'],
					'2' => $arr['d'],
					'3' => ''
				];
				//                $option_en=['1'=>'A','2'=>'B','3'=>'C','4'=>'D','5'=>'E','6'=>'F','7'=>'G','8'=>'H','9'=>'I','10'=>'J','11'=>'K','12'=>'L','13'=>'M','14'=>'N'];
				$option_en = [
					'0' => 'A',
					'1' => 'B',
					'2' => 'C',
					'3' => 'D',
					'4' => 'E',
					'5' => 'F',
					'6' => 'G',
					'7' => 'H',
					'8' => 'I',
					'9' => 'J',
					'10' => 'K',
					'11' => 'L',
					'12' => 'M',
					'13' => 'N'
				];

				?>
                @foreach($info as $k=>$vo)
                    <div class="ques_item">
                        <div class="ques_rate">{{$k+1}}/{{$num}}</div>
                        <h1>{{$k+1}}、{{$vo['question']}}({{$ques_arr[$vo['type']]}}):</h1>
                        <div class="answer_content">
                            @if($vo['type']==3)
                                <div class="answer_content">
                                    <div class="input-wrap">
                                        <input type="text" value="" name="ques_option_text{{$k}}"/>
                                    </div>
                                </div>
                            @elseif($vo['type']==2)
                                @foreach($vo['option_info'] as $kk=>$g)
                                    @if($g['option_type']==1)
                                        <div class="input-wrap">
                                            <input type="checkbox" value="{{$g['id']}}" name="ques_option{{$k}}[]">{{$option_en[$kk]}}.{{$g['option_info']}}
                                            <span class="input-tag"></span>
                                        </div>
                                    @else
                                        <div class="input-wrap">
                                            <input type="checkbox" value="{{$g['id']}}" name="t_ques_option{{$k}}[]">
                                            @if($g['option_info']=="不满意(请注明原因)____________")
                                                {{$arr['j']}}
                                            @else
                                                {{$arr['e']}}
                                            @endif
                                            <span class="input-tag"></span>
                                            <input type="text" value="" name="ques_option_text{{$k}}"/>
                                        </div>
                                    @endif
                                @endforeach
                            @elseif($vo['type']==1)
                                @foreach($vo['option_info'] as $kk=>$g)
                                    @if($g['option_type']==1)
                                        <div class="input-wrap">

                                            <input type="radio" value="r_{{$g['id']}}" name="ques_option{{$k}}">{{$option_en[$kk]}}.{{$g['option_info']}}
                                            <span class="input-tag"></span>
                                        </div>
                                    @else
                                        <div class="input-wrap">
                                            <input type="radio" value="t_{{$g['id']}}" name="ques_option{{$k}}">
                                            @if($g['option_info']=="不满意(请注明原因)____________")
                                                {{$arr['j']}}
                                            @else
                                                {{$arr['e']}}
                                            @endif
                                            <span class="input-tag"></span>
                                            <input type="text" value="" name="ques_option_text{{$k}}"/>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <input type="hidden" value="{{$vo['type']}}" name="ques_type{{$k}}"/>
                        </div>
                    </div>

                @endforeach
            </div>
            <div class="action_btn">

                <input type="hidden" name="num" value="{{$num}}">
                <input type="hidden" name="ques_id" value="{{$ques_id}}">
                <input type="hidden" name="language" value="{{$language}}">
                <input type="button" class="btn-prev btn" value="{{$arr['f']}}">
                <input type="button" class="btn-next btn" value="{{$arr['g']}}">
                <input type="button" id="ajax_submit" class="btn-submit btn" value="{{$arr['h']}}">
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(function () {
            var clentWidth = window.innerWidth, clientHeight = window.innerHeight;
            $('html, body').css('width', clentWidth).css('height', clientHeight);

            if ($('.ques_item').length == 1) {
                $('.btn-submit').addClass('show');
            } else {
                $('.btn-next').addClass('show');
            }

            $('.ques_item').eq(0).addClass('current');

            // 2017-05-02改为下一题按钮切换
            // $('input[type="radio"]').click(function(){
            $('.btn-next').click(function () {
                var nownum = $('.current').index() + 1, showNum = $('.current').index() + 2, allNum = +$('.ques_item').length;
                if ($('.current' + ' :checked').length == 0) {
                    layer.msg('{{trans("base.qqxzda")}}', {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
                    return false;
                } else {
                    if (nownum == allNum) {
                    } else {
                        var hideDom = $('.current'), showDom = $('.current').next();
                        hideDom.removeClass('current');
                        hideDom.addClass('moveToLeft');
                        showDom.addClass('current').addClass('moveFromRight');
                        setTimeout(function () {
                            hideDom.removeClass('moveToLeft');
                            showDom.removeClass('moveFromRight');
                        }, 600);
                    }
                    if (showNum == allNum) {
                        $('.btn-next').removeClass('show');
                        $('.btn-prev').addClass('show');
                        $('.btn-submit').addClass('show');
                    } else {
                        $('.btn-prev').addClass('show');
                    }
                }

            });

            $('.btn-prev').click(function () {
                var nownum = $('.current').index() + 1, showNum = $('.current').index() - 1, allNum = +$('.ques_item').length;
                if (nownum == 0) {
                } else {
                    var hideDom = $('.current'), showDom = $('.current').prev();
                    hideDom.removeClass('current');
                    hideDom.addClass('moveToRight');
                    showDom.addClass('current').addClass('moveFromLeft');
                    setTimeout(function () {
                        hideDom.removeClass('moveToRight');
                        showDom.removeClass('moveFromLeft');
                    }, 600);
                }
                if (showNum == 0) {
                    $('.btn-prev').removeClass('show');
                    $('.btn-submit').removeClass('show');
                    $('.btn-next').addClass('show');
                } else {
                    $('.btn-next').addClass('show');
                    $('.btn-submit').removeClass('show');
                }
            });

            $('input[type="text"]').on('focus', function () {
                var that = $(this);
                if (that.prev().prev().attr('type') == 'radio' || that.prev().prev().attr('type') == 'checkbox') {
                    if (!that.prev().prev().is(':checked')) {
                        that.prev().prev().trigger('click');
                    }
                }
            });
        });
        $("#ajax_submit").click(function () {
            layer.msg("{{$arr['i']}}", {icon: 16, scrollbar: false, shade: [0.3, '#393D49'], time: 0});
            var ajax_url = $('#myform').attr('action');
            $.post(ajax_url, $('#myform').serialize(),
                function (data) {
                    console.log(data);
                    layer.closeAll();
                    if (data.code == 'error') {
                        layer.msg(data.info, {icon: 5, scrollbar: false, time: 2000, shade: [0.3, '#393D49']});
                    }
                    else if (data.code == 'success') {
                        location.href = '{{url('/api/question/quesinfo?p='.$p)}}&end=1&language={{$language}}';
                        //layer.msg(data.info,{icon: 6,scrollbar: false,time: 1000,shade: [0.3, '#393D49']});
                    }
                })
        })
        //微信背景
        if ($("#order-p").val() == 'w') {
            $("body").addClass("bg");
        }
    </script>
    <script src="/js/plugins/layer/layer.js"></script>
@endsection
