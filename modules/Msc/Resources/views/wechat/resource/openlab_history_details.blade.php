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



@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        历史记录详情
</div>
<div class="box">
	<div class="form-group">
	    <label for="">教室名称</label>
		<div class="txt">
			开发实验室A
		</div>
	</div>
	<div class="form-group">
	    <label for="">外借时段</label>
		<div class="txt">
			{{ @$historyDetail['real_begindate'] }}-{{ @$historyDetail['real_enddate'] }}
		</div>
	</div>
	<div class="form-group">
	    <label for="">编号</label>
		<div class="txt">
			1002
		</div>
	</div>
	<div class="form-group">
	    <label for="">地址</label>
		<div class="txt">
			新八教
		</div>
	</div>
	<div class="form-group">
	    <label for="">使用老师</label>
		<div class="txt">
			李老师
		</div>
	</div>
	<div class="form-group">
	    <label for="">使用理由</label>
		<div class="txt">
			训练等
		</div>
	</div>
	<div class="form-group">
	    <label for="">参与学生</label>
		<div class="txt">
			所有学生名字或学生组
		</div>
	</div>

</div>



@stop