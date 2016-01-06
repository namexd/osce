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
    <script src="{{asset('osce/admin/resourcemanage/js/resourcemanage.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'test_station','deletes':'{{route('osce.admin.Station.postDelete')}}'}" />
    <div class="ibox-title route-nav">
        <ol class="breadcrumb">
            <li><a href="#">资源管理</a></li>
            <li class="route-active">考站管理</li>
        </ol>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考站管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.Station.getAddStation')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <div class="panel blank-panel">
                <div class="input-group" style="width: 290px;margin:20px 0;">
                    <input type="text" placeholder="请输入关键字" class="input-sm form-control">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-sm btn-primary" id="search">搜索</button>
                </span>
                </div>

                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>考站名称</th>
                        <th>类型</th>
                        <th>描述</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                            <th>{{$key+1}}</th>
                            <th>{{$item->name}}</th>
                            <th>{{$item->type}}</th>
                            <th>{{$item->description}}</th>
                            <th>
                                <a href="{{route('osce.admin.Station.getEditStation')}}?id={{$item->id}}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o"></i></span></a>
                                <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                            </th>
                        @endforeach
                    </tbody>
                </table>

                <div class="btn-group pull-right">

                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}