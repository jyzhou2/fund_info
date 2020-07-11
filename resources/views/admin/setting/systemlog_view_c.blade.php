@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        {{--<li><a href="{{route('admin.setting.systemlog')}}">系统日志</a></li>--}}
                        <li class="active"><a href="javascript:void(0);">{{$show_filepath}}【总行数：{{$line}}行】</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <form role="form" class="form-inline" method="get" id="lineform">
                            <input type="hidden" name="path" value="{{$filepath}}">
                            <div class="form-group">
                                起始行数 <input class="form-control" type="text" name="start_line" id="start_line" value="{{$start_line}}" size="10">
                            </div>
                            &nbsp;&nbsp;
                            <div class="form-group">
                                结束行数 <input class="form-control" type="text" name="end_line" id="end_line" value="{{$end_line}}" size="10">
                            </div>
                            &nbsp;&nbsp;
                            <button type="submit" class="btn btn-primary">查询</button>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            向后 <input class="form-control" type="text" name="forward_line" id="forward_line" value="{{$forward_line}}" size="4"> 行
                            <input class="btn btn-success" type="button" value="Go"
                                   onclick="$('#start_line').val(parseInt($('#start_line').val())+parseInt($('#forward_line').val()));$('#end_line').val(parseInt($('#end_line').val())+parseInt($('#forward_line').val()));$('#lineform').submit();">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            向前 <input class="form-control" type="text" name="backward_line" id="backward_line" value="{{$backward_line}}" size="4"> 行
                            <input class="btn btn-success" type="button" value="Go"
                                   onclick="$('#start_line').val(parseInt($('#start_line').val())+parseInt($('#backward_line').val()));$('#end_line').val(parseInt($('#end_line').val())+parseInt($('#backward_line').val()));$('#lineform').submit();">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="table table-striped table-bordered table-hover dataTables-example dataTable">
                            <thead>
                            <tr role="row">
                                <th>行数</th>
                                <th></th>
                            </tr>
                            </thead>
                            @foreach($filecontents as $c)
                                <tr class="gradeA">
                                    <td>{{$start_line+$loop->index}}</td>
                                    <td>
                                        @if($c)
                                            <pre>{{var_export(json_decode($c, true))}}</pre>
                                        @else
                                            Null
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
