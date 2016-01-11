@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')

@stop


@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">场所管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <div class="panel blank-panel">
                <div class="panel-heading">
                    <div class="panel-options">
                        <ul class="nav nav-tabs">
                            <li class=""><a href="#">考场</a></li>
                            <li><a href="{{route('osce.admin.room.getRoomList')}}?type=2">中控室</a></li>
                            <li class="active"><a href="{{route('osce.admin.room.getRoomList')}}?type=3">走廊</a></li>
                            <li class=""><a href="{{route('osce.admin.room.getRoomList')}}?type=4">候考区</a></li>
                        </ul>
                    </div>
                </div>

                <div class="input-group" style="width: 290px;margin:20px 0;">
                    <input type="text" placeholder="请输入关键字" class="input-sm form-control" name="keyword" style="height: 34px">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-sm btn-primary" id="search">搜索</button>
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
                            <td value="{{$item->id}}">
                                <a href="{{route('osce.admin.room.getEditRoom')}}?id={{$item->id}}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                                <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>

                <div class="btn-group pull-right">

                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}