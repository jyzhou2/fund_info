@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.info.fundList')}}">基金列表</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <form role="form" class="form-inline" method="get">
                            <div class="form-group">
                                <label class="sr-only">规模</label>
                                <input type="text" name="jjdm" placeholder="基金代码" class="form-control"
                                       value="{{request('jjdm')}}">
                                <input type="text" name="name" placeholder="基金名称" class="form-control"
                                       value="{{request('name')}}">
                            </div>

                            <button type="submit" class="btn btn-primary">搜索</button>
                            <button type="button" class="btn btn-white"
                                    onclick="location.href='{{route('admin.info.fundList')}}'">重置
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <table class="table table-striped table-bordered table-hover dataTables-example dataTable">
                            <thead>
                            <tr role="row">
                                <th>id</th>
                                <th>名称</th>
                                <th>基金类型</th>
                                <th>基金规模</th>
                                <th>创建日期</th>
                                <th>月段位</th>
                                <th>三月段位</th>
                                <th>半年段位</th>
                                <th>最后更新时间</th>
                                <th>创建日期</th>

                            </tr>
                            </thead>
                            @foreach($info as $user)
                                <tr class="gradeA">
                                    <td>
                                        <a target="_blank" href="http://fund.eastmoney.com/{{$user['jjdm']}}.html">
                                            {{$user['jjdm']}} </a></td>
                                    <td>

                                        {{$user->name}}

                                    </td>
                                    <td>{{$user->jijin_type}}</td>
                                    <td>{{$user->jijin_guimo}}</td>
                                    <td>{{$user->jijin_create_day}}</td>

                                </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-12">
                                {{--<div>共 {{ $users->total() }} 条记录</div>--}}
                                {!! $info->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
