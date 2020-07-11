@extends('layouts.public')

@section('bodyattr')class="gray-bg"@endsection

@section('body')
    <div class="wrapper wrapper-content">
        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.info.index')}}">信息列表</a></li>
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
                                <input type="text" name="guimo_number" placeholder="用户名/邮箱/手机" class="form-control" value="{{request('guimo_number')}}">
                            </div>
                            <div class="form-group">
                                <label class="sr-only">周段位</label>
                                <select class="form-control" name="one_week_level" >
                                    <option value="0">无</option>

                                    <option value="1">正序</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="sr-only">月段位</label>
                                <select class="form-control" name="one_month_level" >
                                    <option value="0">无</option>

                                    <option value="1">正序</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="sr-only">三月段位</label>
                                <select class="form-control" name="three_months_level" >
                                    <option value="0">无</option>

                                    <option value="1">正序</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="sr-only">半年段位</label>
                                <select class="form-control" name="six_months_level" >
                                    <option value="0">无</option>

                                    <option value="1">正序</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">搜索</button>
                            <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.info.index')}}'">重置</button>
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
                                <th>当前值</th>
                                <th>规模</th>
                                <th>周段位</th>
                                <th>月段位</th>
                                <th>三月段位</th>
                                <th>半年段位</th>
                                <th>最后更新时间</th>
                                <th>创建日期</th>

                            </tr>
                            </thead>
                            @foreach($info as $user)
                                <tr class="gradeA">
                                    <td>                                <a target="_blank" href="http://fund.eastmoney.com/{{$user['jjdm']}}.html">
                                        {{$user['jjdm']}} </a></td>
                                    <td>
                                        @if($jjinfo=\App\Models\JiJinInfo::where('jjdm',$user['jjdm'])->first())
                                            {{$jjinfo->name}}
                                        @endif
                                    </td>
                                    <td>{{$user['gsl']}}</td>
                                    <td>{{$user['guimo_number']}}</td>
                                    <td>{{$user['one_week_level']}}</td>
                                    <td>{{$user['one_month_level']}}</td>
                                    <td>{{$user['three_months_level']}}</td>
                                    <td>{{$user['six_months_level']}}</td>
                                    <td>{{$user['gsl_update_time']}}</td>
                                    <td>
                                        @if($jjinfo=\App\Models\JiJinInfo::where('jjdm',$user['jjdm'])->first())
                                            {{$jjinfo->jijin_create_day}}
                                        @endif
                                    </td>

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
    <script type="text/javascript">
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            laydate.render({
                elem: '#created_at',
                range: '~',
                max: 0
            });
        });
    </script>
@endsection
