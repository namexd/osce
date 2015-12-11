@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
<style>
	.form-group{margin-bottom: 0!important;}
	.box{background: #fff;padding-bottom: 100px;}
	.form-group .txt{padding-left: 100px!important;}
</style>
@stop

@section('only_head_js')
	<script type="text/javascript">
		$(function(){
			var d=$(".ftime").val().substring(0,10);
			var t1=$(".ftime").val().substring(11,16);
			var t2=$(".etime").val().substring(11,16);
			var time=d+" "+t1+"-"+t2;
			$(".time").text(time);
		})
	</script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        历史记录详情
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<div class="box">
	<div class="form-group">
	    <label for="">教室名称</label>
		<div class="txt">
			{{ $data['name'] }}
		</div>
	</div>
	<div class="form-group">
	    <label for="">外借时段</label>
		<div class="txt">
			<input type="hidden" class="ftime" name="" id="" value="{{ $data['beginTime']}}" />
			<input type="hidden" class="etime" name="" id="" value="{{ $data['endTime'] }}" />
			<span class="time"></span>
		</div>
	</div>
	<div class="form-group">
	    <label for="">编号</label>
		<div class="txt">
			{{ $data['code']}}
		</div>
	</div>
	<div class="form-group">
	    <label for="">地址</label>
		<div class="txt">
			{{ $data['location']}}
		</div>
	</div>
	<div class="form-group">
	    <label for="">使用老师</label>
		<div class="txt">
			{{ $data['teacher']}}李老师
		</div>
	</div>
	<div class="form-group">
	    <label for="">使用理由</label>
		<div class="txt">
			{{ $data['detail']}}
		</div>
	</div>
	<div class="form-group">
	    <label for="">参与学生</label>
		<div class="txt">
			@foreach($data['students'] as $list)
				{{$list['name']}} 
			@endforeach
		</div>
	</div>

</div>
@stop