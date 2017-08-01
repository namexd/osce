@extends('osce::theory.base')

@section('title')
	考试管理
@stop
@section('head_css')

	    <link rel="stylesheet" href="{{ asset('osce/theory/css/exam-manage.css') }}" />
	    <!--<link rel="stylesheet" href="{{ asset('osce/theory/css/bootstrap-datetimepicker.min.css') }}" />-->
<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
		
		<!--<script src="{{ asset('osce/theory/js/bootstrap-datetimepicker.min.js') }}"></script>-->
		<script src="{{ asset('osce/theory/js/exam-manage.js') }}"></script>
		
		<!--
		<script type="text/javascript" src="../js/layout.js" ></script>
		<link rel="stylesheet" type="text/css" href="../css/exam-manage.css"/>
		<script src="../js/exam-manage.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" type="text/css" href="../css/bootstrap-datetimepicker.min.css"/>
		<script type="text/javascript" src="../js/bootstrap-datetimepicker.min.js" ></script>	-->
	
@stop	
@section('head_js')
	
@stop


@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop
@section('body')
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.topic.getAddTopic')}}" class="btn btn-primary" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>	
		<div class="container-fluid ibox-content" id="list_form">
			<div class="exam">

				<div class="set-exam">
					<div class="set-exam-list">
						<div class="exam-column clearfix choose-question">
							<span class="exam-left">考题选择：</span>
							<div class="exam-right clearfix">
								<div>
									<select name="" class="form-control sel-question">
										<option value="">请选择考题</option>
									</select>								
								</div>
								<!--<a href="javascript:;">考题预览</a>-->
							</div>
						</div>	
						<div class="exam-column clearfix choose-time">
							<span class="exam-left">考试时间：</span>
							<div class="exam-right clearfix">
								<div>
									<input type="text" onclick="laydate()" placeholder="请选择开始时间" class="form-control sel-time layinput" />
								</div>
								<span>至</span>
								<div>
									<input type="text" onclick="laydate()" placeholder="请选择结束时间" class="form-control sel-time endtime" />
								</div>
							</div>
						</div>	
						<div class="exam-column clearfix choose-question">
							<span class="exam-left">所属考试：</span>
							<div class="exam-right clearfix">
								<div>
									<select name="" class="form-control sel-question">
										<option value="">请选择考题</option>
									</select>								
								</div>
								<!--<a href="javascript:;">考题预览</a>-->
							</div>
						</div>
						<div class="exam-column clearfix choose-question">
							<span class="exam-left">监考老师：</span>
							<div class="exam-right clearfix">
								<div>
									<select name="" class="form-control sel-question">
										<option value="">请选择考题</option>
									</select>								
								</div>
								<!--<a href="javascript:;">考题预览</a>-->
							</div>
						</div>
						
									
					</div>								
				</div>
				
				
				
				<button class="btn btn-primary exam-addbtn" type="submit">保存</button>
				
				
			</div>
		</div>
		
		
		
			
</div>		
		
@stop