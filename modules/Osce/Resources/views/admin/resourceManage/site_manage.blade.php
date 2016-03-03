@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .blank-panel .panel-heading {margin-left: -20px;}
    #start,#end{width: 160px;}
    .input-group input{height: 34px;}
    .panel>.panel-collapse>.table, .panel>.table, .panel>.table-responsive>.table {margin-bottom: 20px;}
    .panel-options .nav.nav-tabs{
        margin-left: 20px!important;
    }
    </style>
@stop

@section('only_js')
 <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'site_manage','deletes':'{{route('osce.admin.room.postDelete')}}','firstpage':'{{route('osce.admin.room.getRoomList')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">场所管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.room.getAddRoom',['type'=>$type])}}" class="btn btn-primary" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
    <form class="container-fluid ibox-content" id="list_form" method="get" action="{{route('osce.admin.room.getRoomList',['type'=>'1'])}}">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="{{$type === '0'?'active':''}}">
                            <a href="{{route('osce.admin.room.getRoomList',['type'=>0])}}">考场</a>
                        </li>
                        @foreach($area as $key => $item)
                            <li class="{{($item['cate'] === $type)?'active':''}}">
                                <a href="{{route('osce.admin.room.getRoomList',['type'=>$item->cate])}}">{{$item->cate}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="input-group" style="width: 290px;margin:20px 0;">
                <input type="text" placeholder="请输入场所名称" class="input-sm form-control" name="keyword" value="{{(isset($keyword)?$keyword:'')}}">
                <input type="hidden" name="type" value="{{$type}}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-primary">搜索</button>
                </span>
            </div>

            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>场所名称</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $k=>$item)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$item->name}}</td>
                        <td>{{$item->description}}</td>
                        <td>
                            <a href="{{route('osce.admin.room.getEditRoom',['id'=>$item->id,'type'=>$type])}}">
                                <span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span>
                            </a>

                            <a href="javascript:void(0)" class="delete" value="{{$item->id}}"  data-type="{{$type}}"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>

            <div class="pull-left">
                共{{$data->total()}}条
            </div>
            <div class="btn-group pull-right">
                {!! $data->appends($_GET)->render() !!}
            </div>

        </div>
    </form>
</div>

@stop{{-- 内容主体区域 --}}