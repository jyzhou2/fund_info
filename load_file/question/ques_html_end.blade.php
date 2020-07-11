@extends('api.common.public')

@section('title','问卷结束')

@section('head')
    <meta name="csrf-token" content="{{csrf_token()}}">
    <script type="text/javascript">
        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <style>
        .bg {
            background: url("{{cdn('img/html/web_bg.png')}}") no-repeat fixed;
            background-size: 100% 100%;
        }

        .content .ques_content {
            width: 90%;
            height: 70%;
            margin: 0 auto;
            padding: 60px 30px 5px 30px;
            background-size: 100% 100%;
            background-repeat: no-repeat;
            position: relative;
            border: none;
            font-size: 1.2rem;
            line-height: 1.4;
            overflow-x: hidden;
            overflow-y: auto;
            color: #E1BC6D;
            text-align: center;
            /*overflow: hidden;*/
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            -ms-box-sizing: border-box;
            -o-box-sizing: border-box;
            box-sizing: border-box;
        }

        .content .ques_content img {
            width: 45%;
        }

        .content .ques_content p {
            text-align: center;
            color: #fff;
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
        <div class="ques_content">
            <img src="/img/html/ques_end.png">
            <p>{{$arr['msg']}}</p>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/plugins/layer/layer.js"></script>
    <script type="text/javascript">
        $(function () {
            var clentWidth = window.innerWidth, clientHeight = window.innerHeight;
            $('html, body').css('width', clentWidth).css('height', clientHeight);

        });
        //微信背景
        if ($("#order-p").val() == 'w') {
            $("body").addClass("bg");
        }
    </script>
@endsection

