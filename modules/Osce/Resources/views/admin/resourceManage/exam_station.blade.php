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
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_station','deletes':'{{route('osce.admin.Station.postDelete')}}','firstpage':'{{route('osce.admin.Station.getStationList')}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考站管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.Station.getAddStation')}}" class="btn btn-primary" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="container-fluid ibox-content" id="list_form">

                <form action="{{route('osce.admin.Station.getStationList')}}" method="get">
                    <div class="input-group" style="width: 290px;margin-bottom: 20px;">
                        <input type="text" placeholder="请输入考站名" class="form-control" name="name" value="{{(isset($name)?$name:'')}}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-sm btn-primary" id="search">搜索</button>
                    </span>
                    </div>
                </form>
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>考站名称</th>
                        <th>类型</th>
                        <th>考试项目</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $item)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$item->name}}</td>
                                <td>
                                    @foreach($placeCate as $type => $value)
                                        @if($type == $item->type)
                                        {{$value}}
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{$item->title}}</td>
                                <td>
                                    <a href="{{route('osce.admin.Station.getEditStation')}}?id={{$item->id}}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                                    <a href="javascript:void(0)" class="delete" value="{{$item->id}}"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pull-left">
                    共{{$data->total()}}条
                </div>
                <div class="pull-right">
                    {!! $data->render() !!}
                </div>


        </div>

    </div>

@stop{{-- 内容主体区域 --}}