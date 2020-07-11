@extends('layouts.public')

@section('head')
    <link rel="stylesheet" href="{{cdn('css/add/exhibit.css')}}">
@endsection

@section('body')

    <div class="wrapper wrapper-content">

        <div class="row m-b">
            <div class="col-sm-12">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('admin.data.exhibit')}}">展品列表</a></li>
                        <li><a href="{{route('admin.data.exhibit.edit','add')}}">添加展品</a></li>
                    </ul>
                    <form role="form" class="form-inline form-screen" id="form_submit" method="get">
                        <div class="form-group">
                            所在地区
                            <select name="map_id" id="select_calss1">
                                <option value="0">全部</option>
                                @foreach($map_info as $k=>$v)
                                    <option value="{{$v->id}}" title="{{$v->map_path}}" @if(request('map_id')==$v->id) selected @endif >{{$v->map_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            所属展厅
                            <select name="exhibition_id" id="select_calss3">
                                <option value="0">全部</option>
                                @foreach($exhibition_info as $k=>$v)
                                    <option value="{{$v->id}}" title="{{$v->exhibition_path}}" @if(request('exhibition_id')==$v->id) selected @endif >{{$v->exhibition_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(config('exhibit_config.exhibit.is_lb'))
                            <div class="form-group">
                                是否轮播
                                <select name="is_lb" id="select_calss2">
                                    <option value="0">全部</option>
                                    <option @if(request('is_lb')==1) selected @endif value="1">轮播</option>
                                    <option @if(request('is_lb')==2) selected @endif value="2">不轮播</option>
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <input type="text" name="exhibit_name" placeholder="展品名称" class="form-control" value="{{request('exhibit_name')}}" style=" width: 200px;" maxlength="20">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button type="button" class="btn btn-white" onclick="location.href='{{route('admin.data.exhibit')}}'">重置</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="exhibit">
                    <ul class="exhibit-list">
                        @foreach($info as $g)
                            <li>
                                <div class="list-pic">
                                    <img src="@if(isset(json_decode($g['exhibit_img'],true)['exhibit_list'])){{json_decode($g['exhibit_img'],true)['exhibit_list']}}@endif">
                                    <a class="btn-edit" href="{{route('admin.data.exhibit.edit', $g['id'])}}">编辑</a>
                                    <a class="ajaxBtn btn-delete" href="javascript:void(0);" uri="{{route('admin.data.exhibit.delete' ,$g['id'])}}" msg="是否删除该展厅？">删除</a>
                                    @if(config('exhibit_config.exhibit.is_lb'))
                                        @if($g['is_lb']==1)
                                            <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.data.exhibit.unset_lb' , $g['id'])}}" msg="是否取消轮播？">取消轮播</a>
                                        @else
                                            <a class="ajaxBtn btn-set" href="javascript:void(0);" uri="{{route('admin.data.exhibit.set_lb' , $g['id'])}}" msg="是否设为轮播？">设为轮播</a>
                                        @endif
                                    @endif
                                    <a class="btn-order" href="javascript:set_order({{ $g['id']}},'{{$g['exhibit_name']}}');">排序设置</a>
                                </div>
                                <div class="list-tit">{{$g['exhibit_num']}}&nbsp;&nbsp;&nbsp;&nbsp;{{$g['exhibit_name']}}</div>
                                <div class="list-info">
                                    <span><i class="fa fa-eye fa-lg"></i>{{$g['look_num']}}人浏览</span>
                                    <span><i class="fa fa-thumbs-o-up fa-lg"></i>{{$g['like_num']}}人点赞</span>
                                    <span><i class="fa fa-commenting-o fa-lg"></i>{{$g['collection_num']}}人评论</span>
                                    <span><i class="fa fa-star-o fa-lg"></i>{{$g['comment_num']}}人收藏</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="row recordpage" style="padding: 0 50px;">
                    <div class="col-sm-12">
                        {!! $info->links() !!}
                        {{--<span>共 {{ $info->total() }} 条记录</span>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $('#select_calss1,#select_calss2,#select_calss3').change(function () {
            $('#form_submit').submit();
        })

        function set_order(id, name) {
            var url = "{{route('admin.data.exhibit.set_order')}}?id=" + id;
            layer.open({
                title: '<img src={{cdn("img/map_opr5.png")}}>' + name + '    排序设置',
                type: 2,
                area: ['700px', '280px'],
                fix: true, //固定
                maxmin: false,
                move: false,
                resize: false,
                zIndex: 1,
                content: url
            });
        }
    </script>
@endsection
