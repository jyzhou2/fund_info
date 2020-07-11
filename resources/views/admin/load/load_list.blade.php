@extends('layouts.public')

@section('bodyattr')class=""@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.load.load_list')}}">模块装载列表</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title tablels">
                        <button class="ajaxBtn layui-btn layui-btn-danger" href="javascript:void(0);" uri="{{route('admin.load.uninstall_controller')}}" style="float: right;margin-bottom: 10px"
                                msg="此操作不可逆，是否要卸载？">模块装载管理卸载
                        </button>
                        <table class="table table-striped table-bordered table-hover dataTables-example dataTable">
                            <thead>
                            <tr role="row">
                                <th width="170">模块名称</th>
                                <th width="170">key</th>
                                <th width="250">状态</th>
                                <th width="250">描述</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            @foreach($list as $k=>$g)
                                <tr class="gradeA">
                                    <td>{{$g['name']}}</td>
                                    <td>{{$g['key']}}</td>
                                    <td>
                                        @if($g['status']==0)
                                            <span style="color: blue">未安装</span>
                                        @elseif($g['status']==1)
                                            <span style="color: green">已安装</span>
                                        @elseif($g['status']==2)
                                            <span style="color: red">装载文件缺失</span>
                                        @elseif($g['status']==4)
                                            <span style="color: red">文件内容有修改，请手动操作</span>
                                        @else
                                            <span style="color: red">安装失败，文件缺失，请卸载后重新安装</span>
                                        @endif
                                    </td>
                                    <td>{!! $g['des'] !!}</td>
                                    <td>
                                        @if($g['status']==0)
                                            <a class="ajaxBtn" style="color: green" href="javascript:void(0);" uri="{{route('admin.load.install',$g['key'])}}"
                                               msg="是否要安装{{$g['name']}}？安装前请先确定前置模块是否已安装">安装</a>
                                        @elseif($g['status']==1)
                                            <a class="ajaxBtn" style="color: red" href="javascript:void(0);" uri="{{route('admin.load.uninstall',$g['key'])}}" msg="是否要卸载{{$g['name']}}？">卸载</a>
                                        @elseif($g['status']==4)
                                            请确认后手动操作
                                        @elseif($g['status']==2)
                                            请确认后手动操作
                                        @else
                                            <a class="ajaxBtn" style="color: red" href="javascript:void(0);" uri="{{route('admin.load.uninstall',$g['key'])}}" msg="是否要卸载{{$g['name']}}？">卸载</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@section('script')

@endsection
