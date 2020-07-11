@extends('layouts.public')


@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.data.autonum')}}">蓝牙关联列表</a></li>
                        <li><a href="{{route('admin.data.autonum.edit','add')}}">添加关联</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" id="form_submit" method="get">
                        <div class="form-group">
                            <input type="text" name="autonum" placeholder="蓝牙编号" class="form-control" value="{{request('autonum')}}" style=" width: 200px;" maxlength="20">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.data.autonum')}}'">重置</button>
                    </form>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <table class="table table-striped table-new table-hover infoTables-example infoTable">
                        <thead>
                        <tr role="row">
                            <th width="100">AutoNum</th>
                            <th width="100">所关联的展品</th>
                            <th width="100">编辑时间</th>
                            <th width="100">操作</th>
                        </tr>
                        </thead>
                        @foreach($info as $k=>$v)
                            <tr class="gradeA">
                                <td>{{$v->autonum}}</td>
                                <td>@if(is_array(json_decode($v->exhibit_name,true)))
                                        @foreach(json_decode($v->exhibit_name,true) as $k=>$g)
                                            {{$g}}&nbsp;&nbsp;&nbsp;&nbsp;
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{$v->updated_at}}</td>
                                <td>
                                    <a class="btn-edit" href="{{route('admin.data.autonum.edit',$v['id'])}}" title="编辑"><i class="fa fa-edit"></i></a>
                                    <a class="ajaxBtn" href="javascript:void(0);" uri="{{route('admin.data.autonum.delete',$v['id'])}}" title="删除" msg="是否删除？"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="row recordpage">
                        <div class="col-sm-12">
                            {!! $info->links() !!}
                            {{--<span>共 {{ $info->total() }} 条记录</span>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


