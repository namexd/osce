@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
	<style>
		.ibox .open>.dropdown-menu{
			left:0;
		}
		.btn-white{
			border: 1px solid #fff;
		}
	</style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
	<script src="{{asset('msc/admin/openlab/openlab.js')}}"></script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<input type="hidden"  id="parameter" value="{'pagename':'lab-exist-detail'}" >
	<div class="ibox float-e-margins ibox-content pad0">
		<div class="row table-head-style1 ">
	        <div class="col-xs-2 col-md-2 head-opera">
	            <input placeholder="日期" class="form-control layer-date laydate-icon" id="start" name="date">
	        </div>
	        <div class="col-xs-6 col-md-2">
	            <form method="get" action="{{route("msc.admin.lab.openLabHistoryList")}}">
	                <div class="input-group">
	                    <input type="text" placeholder="搜索" class="input-sm form-control" name="keyword" value="">
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
						<div class="btn-group Examine">
							<button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">预约人<span class="caret"></span>
							</button>
							<ul class="dropdown-menu order-classroom">
								<li value="1">
									<a href="">升序</a>
								</li>
								<li value="-1">
									<a href="">降序</a>
								</li>
							</ul>
						</div>
					</th>
					<th>
						预约理由
					</th>
	                <th>
						<div class="btn-group Examine">
							<button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">教室复位状态自检<span class="caret"></span>
							</button>
							<ul class="dropdown-menu order-classroom">
								<li value="1">
									<a href="">升序</a>
								</li>
								<li value="-1">
									<a href="">降序</a>
								</li>
							</ul>
						</div>
					</th>
	                <th>
						<div class="btn-group Examine">
							<button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">是否按时关机<span class="caret"></span>
							</button>
							<ul class="dropdown-menu order-classroom">
								<li value="1">
									<a href="">升序</a>
								</li>
								<li value="-1">
									<a href="">降序</a>
								</li>
							</ul>
						</div>
	                </th>
	                <th>操作</th>
	    		</tr> 
	    		<tr>
	    			@foreach($pagination as $list)
	    				<td>{{$list->id}}</td>
		                <td>{{$list->lab->name}}</td>
		                <td>{{date('Y/m/d',strtotime($list->begin_datetime))}}</td>
		                <td>{{date('H:i',strtotime($list->begin_datetime))}}-{{date('H:i',strtotime($list->end_datetime))}}</td>
		                <td>{{$list->lab->code}}</td>
		                <td>{{$list->apply->applyUser->name}}</td>
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
			<div class="pull-right">
				{!! $pagination->render() !!}
			</div>
	    </form>
	</div>
</div>
@stop{{-- 内容主体区域 --}}