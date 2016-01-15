@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
        .btn{
            margin: 0!important;
        }
    </style>
@stop

@section('only_js')

@stop


@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">设备管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.machine.getAddCameras')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
            <div class="panel blank-panel">
                <div class="panel-heading">
                    <div class="panel-options">
                        <ul class="nav nav-tabs">
                            @forelse($options as $key=>$option)
                                <li class="{{($key==0&&!array_key_exists('cate_id',$_GET))||(array_key_exists('cate_id',$_GET)&&$_GET['cate_id']==$option['id'])? 'active':''}}"><a href="{{route('osce.admin.machine.getMachineList',['cate_id'=>$option['id']])}}">{{$option['name']}}</a></li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="input-group" style="margin: 20px 0">
                    <form action="{{route('osce.admin.machine.getMachineList',['cate_id'=>1])}}" method="get">
                        <input type="text" placeholder="设备名称" class="form-control" style="width: 250px;margin-right: 10px;" name="name">
                        <div class="btn-group" style="margin-right: 10px;">
                            <button type="button" class="btn btn-default dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
                                @if(array_key_exists('status',$_GET))
                                    @forelse($machineStatuValues as $status=>$machineStatuValue)
                                        @if($_GET['status']==$status)
                                            {{$machineStatuValue}}
                                        @endif
                                    @empty
                                    @endforelse
                                @else
                                    状态
                                @endif
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{route('osce.admin.machine.getMachineList')}}">全部</a></li>
                                @forelse($machineStatuValues as $status=>$machineStatuValue)
                                    <li><a href="{{route('osce.admin.machine.getMachineList',['status'=>$status])}}">{{$machineStatuValue}}</a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                        <button type="submit" class="btn  btn-primary" id="search">&nbsp;搜索&nbsp;</button>
                    </form>
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
                        @forelse($list as $key => $item)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$item->id}}</td>
                            <td>{{$item->name}}</td>
                            <td>{{$machineStatuValues[$item->status]}}</td>
                            <td><a href="{{route('osce.admin.machine.getEditCameras',['id'=>$item->id])}}">编辑</a></td>
                        </tr>
                        @empty
                        @endforelse

                    </tbody>
                </table>

                <div class="btn-group pull-right">
                    {!! $list->appends($_GET)->render() !!}
                </div>
            </div>

    </div>
@stop{{-- 内容主体区域 --}}