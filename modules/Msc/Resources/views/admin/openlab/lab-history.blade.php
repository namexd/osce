@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('msc/admin/trainarrange/trainarrange.js')}}"></script>
	<script src="{{asset('msc/admin/openlab/openlab.js')}}"></script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<input type="hidden"  id="parameter" value="{'pagename':'lab-history'}" >
	<div class="ibox float-e-margins ibox-content pad0">
		<div class="row table-head-style1 ">
	        <div class="col-xs-2 col-md-2 head-opera">
	            <input placeholder="日期" class="form-control layer-date laydate-icon" id="start" name="begindate">
	        </div>
	        <div class="col-xs-6 col-md-2">
	            <form method="get">
	                <div class="input-group">
	                    <input type="text" placeholder="搜索" class="input-sm form-control" name="" value="">
	                <span class="input-group-btn">
	                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
	                </span>
	                </div>
	            </form>
	        </div>
	        <a href="/msc/admin/lab/openlab-history-analyze-form" class="btn btn-primary right_marr20">使用历史统计分析</a>
	    </div>
	    <form class="container-fluid ibox-content">
	    	<table class="table table-striped table-hover">
	    		<tr class="bgw">
	    			<th>#</th>
	                <th>开发实验室</th>
	                <th>日期</th>
	                <th>时间</th>
	                <th>编号</th>
	                <th>
	                	<select class="bor0 cur">
	                		<option>预约人</option>
	                		<option value="1">李老师</option>
	                		<option value="2">王同学</option>
	                	</select>
					</th>
					<th>
						预约理由
					</th>
	                <th>
	                    <select class="bor0 cur">
	                		<option>教室复位状态自检</option>
	                		<option value="1">良好</option>
	                		<option value="2">有损坏</option>
	                		<option value="3">严重损坏</option>
	                	</select>
	                </th>
	                <th>
	                    <select class="bor0 cur">
	                		<option>是否按时关机</option>
	                		<option value="1">是</option>
	                		<option value="2">否</option>
	                	</select>
	                </th>
	                <th>操作</th>
	    		</tr> 
	    		<tr>
	    			@foreach($pagination as $list)
	    				<td>{{$list->id}}</td>
		                <td>{{$list->name}}</td>
		                <td class="date">{{$list->begin_datetime}}</td>
		                <td class="time">{{$list->begin_datetime}}</td>
		                <td>{{$list->code}}</td>
		                <td>{{$list->user}}</td>
		                <td>{{$list->detail}}</td>
		                <td>
		                	@if($list->result_init==1)
		                		良好
		                	@elseif($list->result_init==2)
		                		<span class="" style="color: #ED5565;">损坏</span>
		                	@elseif($list->result_init==3)
		                		<span class="" style="color: #F00;">严重损坏</span>
		                	@endif
		                </td>
		                <td>
		                	@if($list->result_poweroff==1)
		                		是
		                	@elseif($list->result_poweroff==0)
		                		<span class="" style="color: #ED5565;">否</span>
		                	@endif
		                </td>
		                <td><a class="" href="{{ route('msc.lab.getOpenlabHistoryItem', [$list->id]) }}">查看</a></td>
	    			@endforeach
	            </tr>
	    	</table>
	    </form>
	</div>
</div>
@stop{{-- 内容主体区域 --}}