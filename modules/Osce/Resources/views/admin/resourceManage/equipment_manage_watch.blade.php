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
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'equipment_manage_watch','del': '{{route("osce.admin.machine.postMachineDelete")}}','url': '{{route("osce.admin.machine.getMachineList",["cate_id"=>3])}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-12 col-md-12">
                <h5 class="title-label">设备管理</h5>
                <a href="{{route('osce.admin.machine.getAddWatch')}}" class="btn btn-primary" style="float:right;margin:0 10px 0 0;">新增</a>
                <a href="{{route('osce.admin.machine.getWatchLogList')}}" class="btn btn-outline btn-default" style="float:right;margin:0 10px 0 0!important;">使用记录</a>
                <div class="clearfix"></div>
            </div>
        </div>

            <div class="ibox-content container-fluid">
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

                <div class="input-group" style="margin-bottom: 20px;margin-top: 10px;">
                    <form action="{{route('osce.admin.machine.getMachineList',['cate_id'=>3])}}" method="get">
                    <input type="hidden" name="cate_id" value="3">
                    <input type="text" placeholder="设备名称" class="form-control" style="width: 250px;margin-right: 10px;height: 36px;" name="name" value="{{(empty($name)?'':$name)}}">

                    <div class="btn-group" style="margin-right: 10px;">
                        <button type="button" class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="">
                            @if(array_key_exists('status',$_GET))
                                @forelse($machineStatuValues as $status=>$machineStatuValue)
                                    @if($_GET['status']==$status)
                                        {{$machineStatuValue}}
                                        <input type="hidden" name="status" value="{{$status}}">
                                    @endif
                                @empty
                                @endforelse
                            @else
                                状态
                            @endif
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="{{route('osce.admin.machine.getMachineList',['cate_id'=>3])}}">全部</a></li>
                            @forelse($machineStatuValues as $status=>$machineStatuValue)
                                @if(array_key_exists('name',$_GET))
                                    <li><a href="{{route('osce.admin.machine.getMachineList',['cate_id'=>3,'status'=>$status,'name'=>$_GET['name']])}}">{{$machineStatuValue}}</a></li>
                                @else
                                    <li><a href="{{route('osce.admin.machine.getMachineList',['cate_id'=>3,'status'=>$status])}}">{{$machineStatuValue}}</a></li>
                                @endif
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
                        <th>感应ID</th>
                        <th>设备名称</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($list as $key => $item)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$item->code}}</td>
                            <td>{{$item->nfc_code}}</td>
                            <td>{{$item->name}}</td>
                            <td style="color: @if($item->status==1)#408aff
                                              @elseif($item->status==2)#ed5565
                                              @elseif($item->status==3)#f8ac59
                                              @endif
                                    ">{{$machineStatuValues[$item->status]}}</td>
                            <td>
                                <a href="{{route('osce.admin.machine.getEditWatch',['id'=>$item->id])}}">
                                    <span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span>
                                </a>
                                <a href="javascript:void(0)"><span class="read state2"><i class="fa fa-trash-o fa-2x" eid="{{$item->id}}"></i></span></a>
                            </td>
                        </tr>
                    @empty
                    @endforelse

                    </tbody>
                </table>

                    <div class="pull-left">
                        共{{$list->total()}}条
                    </div>
                    <div class="pull-right">
                        {!! $list->appends($_GET)->render() !!}
                    </div>


            </div>
    </div>
@stop{{-- 内容主体区域 --}}