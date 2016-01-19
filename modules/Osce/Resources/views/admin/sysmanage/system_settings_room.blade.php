@extends('osce::admin.layouts.admin_index')

@section('only_css')
@stop

@section('only_js')
    
@stop


@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">系统设置</h5>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('osce.admin.config.getIndex')}}">媒体设置</a></li>
                        <li class="active"><a href="#">场所类型</a></li>
                        <a href="{{route('osce.admin.config.getAreaStore')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
                    </ul>
                    
                </div>
                <table class="table table-striped" id="table-striped">
	                <thead>
		                <tr>
		                    <th>类型名称</th>
		                    <th>描述</th>
		                    <th>操作</th>
		                </tr>
	                </thead>
	                <tbody>
					@foreach($data as $item)
	                    <tr>
	                        <td>{{$item->name}}</td>
	                        <td>{{$item->description}}</td>
	                        <td>
	                            <a href="javascript:void(0)"><span class="read  state1"><i class="fa fa-trash-o fa-2x"></i></span></a>
	                        </td>
	                    </tr>
					@endforeach
	                </tbody>
	            </table>
            </div>
            <div class="btn-group pull-right">
            </div>
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}