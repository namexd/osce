@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
<link rel="stylesheet" href="{{asset('osce/wechat/common/css/weui.min.css')}}" type="text/css" />
<style type="text/css">
	.form-group{border-bottom:1px solid #e7eaec;}
	.cj_tab tr{border-bottom:1px solid #e7eaec;}
	.cj_tab tr.even{background:#F9F9F9;}
	.cj_tab th,.cj_tab td{border:none!important;}
	.cj_tab .see{color:#1ab394;}
	.form-group label{z-index:0!important;}
	.invigilation{
		margin:10px 3% 0;
		width:94%;
		overflow: hidden;
	    text-overflow: ellipsis;
	    white-space: nowrap;
	}
</style>
@stop
@section('only_head_js')
	<script type="text/javascript" src="{{asset('osce/wechat/js/examination.js')}}" ></script>
@stop

@section('content')
	<input type="hidden" id="parameter" value="{'pagename':'examination_list_teacher','ajaxurl':'{{route('osce.wechat.student-exam-query.getTeacherCheckScore')}}','detailUrl':'{{route('osce.wechat.student-exam-query.getEveryExamList')}}'}" />
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.student-exam-query.getResultsQueryIndex')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	成绩查询
       	<a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="form-group" style="border: none;">
	    <span class="form-control normal_select select_indent invigilation" id="showActionSheet">请选择考试</span>
	    <div id="actionSheet_wrap">
	        <div class="weui_mask_transition" id="mask"></div>
	        <div class="weui_actionsheet" id="weui_actionsheet">
	            <div class="weui_actionsheet_menu" >
	            @foreach($ExamList as $list)
	            	<div class="weui_actionsheet_cell" value="{{$list->exam_id}}" data-id="{{$list->station_id}}">{{$list->exam_name}}</div>
	            @endforeach
	            </div>
	            <div class="weui_actionsheet_action">
	                <div class="weui_actionsheet_cell" id="actionsheet_cancel">取消</div>
	            </div>
	        </div>
	    </div>
    </div>
    <div class="examination_msg">
		<div class="form-group">
			<label for="">考试时间</label>
			<div class="txt" id="time"></div>
		</div>
		<div class="form-group">
			<label for="">科目</label>
			<div class="txt" id="subject"></div>
		</div>
		<div class="form-group">
			<label for="">考试人数</label>
			<div class="txt" id="number"></div>
		</div>
		<div class="form-group">
			<label for="">平均用时</label>
			<div class="txt" id="time2"></div>
		</div>
		<div class="form-group" style="border:none;">
			<label for="">平均成绩</label>
			<div class="txt" id="vgn"></div>
		</div>
		<table class="table cj_tab">
			<tr>
				<th>考生姓名</th>
				<th>总分</th>
				<th>操作</th>
			</tr>
		</table>
	</div>
  	
@stop