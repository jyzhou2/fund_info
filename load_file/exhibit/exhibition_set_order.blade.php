@extends('layouts.public')
@section('head')
@endsection
@section('bodyattr')class=""@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <form action="" method="post" class="ajaxForm layui-form" style="margin-top: 60px">
                        <div class="form-group">
                            <div class="col-sm-4" style="width: 700px">
                                <label class="layui-form-label">移动至</label>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="exhibit_id" lay-verify="required" lay-search="">
                                            <option value="">直接选择或搜索选择</option>
                                            @foreach($list_info as $k=>$g)
                                                <option value="{{$g['id']}}">{{$g['exhibition_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                之
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <select name="move_type" style="width: 50px">
                                            <option value="1">前</option>
                                            <option value="2">后</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-6 col-md-offset-2">
                                <input type="hidden" name="id" value="{{$id}}">
                                <button class="btn btn-primary" type="submit" style="margin-left: 240px">保存</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        layui.use(['form'], function () {
            var form = layui.form
        });
    </script>

@endsection

