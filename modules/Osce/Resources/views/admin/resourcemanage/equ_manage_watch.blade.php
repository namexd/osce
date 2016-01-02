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
    <div class="ibox-title route-nav">
        <ol class="breadcrumb">
            <li><a href="#">资源管理</a></li>
            <li class="route-active">设备管理</li>
        </ol>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">设备管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.machine.getAddWatch')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <div class="panel blank-panel">
                <div class="panel-heading">
                    <div class="panel-options">
                        <ul class="nav nav-tabs">
                            @forelse($options as $key=>$option)
                                <li class="{{$_GET['cate_id']==$option['id']? 'active':''}}"><a href="{{route('osce.admin.machine.getMachineList',['cate_id'=>$option['id']])}}">{{$option['name']}}</a></li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="input-group" style="margin: 20px 0">
                    <input type="text" placeholder="设备名称" class="form-control" style="width: 250px;margin-right: 10px;">
                    <div class="btn-group" style="margin-right: 10px;">
                        <button type="button" class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
                            状态<span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn  btn-default" id="search">&nbsp;搜索&nbsp;</button>

                </div>

                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>设备ID</th>
                        <th>设备名称</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($list as $item)
                        <tr>
                            <td>{{$item->id}}</td>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$machineStatuValues[$item->status]}}</td>
                            <td><a href="{{route('osce.admin.machine.getEditWatch',['id'=>$item->id])}}">编辑</a></td>
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