@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
@stop

@section('only_js')
    <script src="{{asset('msc/admin/trainarrange/trainarrange.js')}}"></script>
	<script src="{{asset('msc/admin/openlab/openlab.js')}}"></script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>
				使用历史详情
			</h5>
		</div>
		
		<div class="ibox-content form-horizontal">
			<div class="form-group">
	            <label class="col-sm-2 control-label font12">开放实验室名称</label>
	            <div class="col-sm-10 padt_7">
					{{$pagination['name']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">使用时间段</label>
	            <div class="col-sm-10 padt_7">
	            	{{$pagination['beginTime']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">编号</label>
	            <div class="col-sm-10 padt_7">
	            	{{$pagination['code']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">使用老师</label>
	            <div class="col-sm-10 padt_7">
	            	{{$pagination['teacher']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">使用理由</label>
	            <div class="col-sm-10 padt_7">
	            	{{$pagination['detail']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">参与学生名单</label>
	            <div class="col-sm-10 padt_7">
	            	@foreach($pagination['students'] as $stu)
	            		{{$stu->name}}&nbsp;
	            	@endforeach
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">教室复位状态自检</label>
	            <div class="col-sm-10 padt_7">
	            	@if($pagination['result_init']==1)
                		良好
                	@elseif($pagination['result_init']==2)
                		损坏
                	@elseif($pagination['result_init']==3)
                		严重损坏
                	@endif
	            	
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">是否按时关机</label>
	            <div class="col-sm-10 padt_7">
	            	@if($pagination['result_poweroff']==1)
                		是
                	@elseif($pagination['result_poweroff']==0)
                		否
                	@endif
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
		</div>
	</div>
	@stop{{-- 内容主体区域 --}}
