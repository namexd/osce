@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<style>
	.form-group{margin-bottom: 0!important;}
	.form-group .txt{padding-left: 100px!important;}
	.box{background: #fff;width:110%;margin:0 -5%;}
</style>
@stop

@section('only_head_js')

@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        预约开放设备申请
</div>
<form action="/msc/wechat/open-device/open-tools-apply" class="w_90 marb_10" method="post">
	<div class="mart_10 marb_10">请确认你的预约信息</div>
	<div class="box">
		<div class="form-group">
		    <label for="">设备名称</label>
			<div class="txt">
				{{$data['name']}}
			</div>
		</div>
		<div class="form-group">
		    <label for="">设备编号</label>
			<div class="txt">
				{{$data['code']}}
			</div>
		</div>
		<div class="form-group">
		    <label for="">预约时段</label>
			<div class="txt" name="timeSec">
				{{$data['timeSec']}}
			</div>
			<input type="hidden" name="timeSec" value="{{$data['timeSec']}}" />
		</div>
		<div class="form-group">
		    <label for="">预约人</label>
			<div class="txt">
				{{$data['userName']}}
			</div>
		</div>
	</div>
	<div class="mart_10 marb_10">预约理由</div>
	<div class="Reason">
	    <textarea id="Reason_detail" name="detail" type="" placeholder="请输入外借理由"></textarea>
	</div>
	<input type="hidden" name="date" value="{{$data['date']}}" />
	<input type="hidden" name="deviceId" value="{{$data['id']}}" />
	
	<input class="btn2 mart_10 marb_10" type="submit" value="提交申请">
</form>

@stop