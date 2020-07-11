@extends('layouts.public')
@section('head')
@endsection
@section('bodyattr')class=""@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form action="" method="post" class="form-horizontal ajaxForm">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="nickname" value="{{$info->nickname}}" maxlength="20"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">邮箱</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="{{$info->email}}" name="email" maxlength="255"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">手机</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" value="{{$info->phone}}"    name="phone" maxlength="11"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-10">
                                    <label class="col-sm-2 control-label" style=" width: 20.666667%;">头像上传</label>
                                    <div class="webuploader-pick" onclick="upload_resource('头像上传','FT_AVATAR','avatar',1);" style=" float: left; display: inline-block; width: auto;">点击上传图片</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-4">
                                    <div id="avatar">
                                        @if(!empty($info->avatar))
                                            <div class="img-div">
                                                <img src="{{get_file_url($info->avatar)}}">
                                                <span onclick="del_img($(this))">×</span>
                                                <input type="hidden" name="avatar" value="{{$info->avatar or ''}}">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-6 col-md-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <button class="btn btn-white" type="button" onclick="window.history.back()">返回</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!--css add begin-->
<!--css add end-->

@endsection

