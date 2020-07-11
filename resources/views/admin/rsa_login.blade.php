@extends('layouts.public')

@section('title', '登录')

@section('body')
    <div class="middle-box text-center loginscreen">
        <div>
            <h3>{{$system_name}}</h3>

            <form class="m-t" role="form" id="myform" method="POST" action="{{ route('admin.login')}}">
                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    <input type="text" name="username" class="form-control" placeholder="手机/邮箱/用户名" required="" value="{{ old('username') }}"/>
                    @if ($errors->has('username'))
                        <span class="help-block">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input type="password" name="password" class="form-control" placeholder="密码" required="">
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                @if ($captchaadminlogin)
                    <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                        <input type="text" name="captcha" class="form-control" placeholder="验证码" required="" style="width: 150px; float: left;"/>
                        <img src="{{ url('cpt/show') }}" onclick="this.src='{{ url('cpt/show') }}?r=' + Math.random();" title="看不清，换一个" style="cursor:pointer;"/>
                        @if ($errors->has('captcha'))
                            <span class="help-block">
                            <strong>{{ $errors->first('captcha') }}</strong>
                        </span>
                        @endif
                    </div>
                @endif
                <button type="button" class="btn btn-primary block full-width m-b" onclick="login()">登 录</button>
            </form>
            <form style="display: none" method="POST" id="login_form" action="{{ route('admin.login')}}">
                {{ csrf_field() }}
                <input type="hidden" id="data" name="data[]" >
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        var login_str='{{$login_str}}';
        var str_length={{$str_length}};
    </script>
    <script src="{{cdn('js/login.js')}}"></script>
@endsection