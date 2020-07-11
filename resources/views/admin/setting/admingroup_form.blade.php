@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('head')
    <style type="text/css">
        .treeview span.indent {
            margin-left: 10px;
            margin-right: 10px;
        }

        .treeview span.icon {
            width: 12px;
            margin-right: 5px;
        }
        .file {
            border: 1px solid #e7eaec;
            padding: 0;
            background-color: #fff;
            position: relative;
            margin-bottom: 0px;
            margin-right: 20px;
        }
        .file .icon {
            height: 36px;
            overflow: hidden;
        }
    </style>
@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('admin.setting.admingroup')}}">角色列表</a></li>
                        <li @if (!isset($ugroup))class="active"@endif><a href="{{route('admin.setting.admingroup.add')}}">添加角色</a></li>
                        @if (isset($ugroup))
                            <li class="active"><a href="#">编辑</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form action="{{route('admin.setting.admingroup.save')}}" method="post" class="form-horizontal ajaxForm">
                            <input type="hidden" name="groupid" value="{{$ugroup->groupid or 0}}"/>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">角色名称</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="groupname" value="{{ old('groupname') ? old('groupname') : (isset($ugroup->groupname) ? $ugroup->groupname : '') }}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">权限设置</label>
                                <div class="col-sm-6 treeview">
                                    <ul class="list-group" name="privs">
                                        @foreach($menulist as $menu)
                                            @if($rule['privs']=='all'||in_array($menu['priv'],$rule['privs']))
                                                <li class="list-group-item node-tree subv1 {{$menu['priv']}}">
                                                    <span class="icon expand-icon glyphicon glyphicon-minus"></span>
                                                    <input type="checkbox" name="privs[]" class="cb" value="{{$menu['priv']}}"
                                                           @if (in_array($menu['priv'], (old('privs') ? old('privs') : (isset($ugroup->privs) ? $ugroup->privs : [])))) checked="checked"@endif/>
                                                    {{$menu['text']}}
                                                </li>
                                                @if(isset($menu['nodes']) && is_array($menu['nodes']))
                                                    @foreach($menu['nodes'] as $submenu)
                                                        @if($rule['privs']=='all'||in_array($submenu['priv'],$rule['privs']))
                                                            <li class="list-group-item node-tree subv2 {{$menu['priv']}}">
                                                                <span class="indent"></span>
                                                                @if(isset($submenu['nodes']) && is_array($submenu['nodes']))
                                                                    <span class="icon expand-icon glyphicon glyphicon-minus"></span>
                                                                @else
                                                                    <span class="icon glyphicon"></span>
                                                                @endif
                                                                <input type="checkbox" name="privs[]" class="cb" value="{{$submenu['priv']}}"
                                                                       @if (in_array($submenu['priv'], (old('privs') ? old('privs') : (isset($ugroup->privs) ? $ugroup->privs : [])))) checked="checked"@endif/>
                                                                {{$submenu['text']}}
                                                            </li>
                                                            @if(isset($submenu['nodes']) && is_array($submenu['nodes']))
                                                                @foreach($submenu['nodes'] as $childmenu)
                                                                    @if($rule['privs']=='all'||in_array($childmenu['priv'],$rule['privs']))
                                                                        <li class="list-group-item node-tree subv3">
                                                                            <span class="indent"></span>
                                                                            <span class="indent"></span>
                                                                            <span class="icon glyphicon"></span>
                                                                            <input type="checkbox" name="privs[]" class="cb" value="{{$childmenu['priv']}}"
                                                                                   @if (in_array($childmenu['priv'], (old('privs') ? old('privs') : (isset($ugroup->privs) ? $ugroup->privs : [])))) checked="checked"@endif/>
                                                                            {{$childmenu['text']}}
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    @if (isset($ugroup))
                                        <button class="btn btn-primary" type="submit">保存</button>
                                        <button class="btn btn-white" type="button" onclick="window.history.back()">返回</button>
                                    @else
                                        <button class="btn btn-primary" type="submit">添加</button>
                                        <button class="btn btn-white" type="reset">重置</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function(){
            function sameCheckedSet(eleInput){
                var eleParent = eleInput.parent(".list-group-item");
                var pclass = eleParent.attr("class").replace(":","-");
                var lastclass = pclass.split(" ").pop();

                /*一级复选框*/
                if( eleParent.is(".subv1") ){
                    var checkStatus = true;
                    var eleParentV2 = $(".list-group-item.subv2."+lastclass);
                    if( eleParentV2.length>0 ){
                        if( !eleInput.is(':checked') ){
                            checkStatus = false;
                        }
                        if(checkStatus){
                            eleParentV2.each(function(){
                                $(this).find("input[type='checkbox']").prop("checked",true);
                            });
                        }else{
                            eleParentV2.each(function(){
                                $(this).find("input[type='checkbox']").prop("checked",false);
                            });
                        }
                    }
                }
                /*二级复选框*/
                else if( eleParent.is(".subv2") ){
                    var checkStatus = false;
                    var eleParentV1 = $(".list-group-item.subv1."+lastclass);
                    $(".list-group-item.subv2."+lastclass).each(function(){
                        var chkbox = $(this).find("input[type='checkbox']");
                        if( chkbox.is(':checked') ){
                            checkStatus = true;
                        }
                    });
                    if(checkStatus){
                        eleParentV1.find("input[type='checkbox']").prop("checked",true);
                    }else{
                        eleParentV1.find("input[type='checkbox']").prop("checked",false);
                    }
                }
            }

            $(".list-group .list-group-item input[type='checkbox']").change(function(){
                sameCheckedSet($(this));
            });

        });
    </script>
@endsection
