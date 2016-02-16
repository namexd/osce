@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
@stop
@section('only_head_js')
	<script type="text/javascript" src="{{asset('osce/wechat/js/examination.js')}}" ></script>
@stop

@section('content')
	<input type="hidden" id="parameter" value="{'pagename':'examination_list','ajaxurl':'{{route('osce.wechat.student-exam-query.getEveryExamList')}}','detailUrl':'{{route('osce.wechat.student-exam-query.getExamDetails')}}'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	成绩查询
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="form-group">
        <select  id="examination" class="form-control normal_select select_indent" name="student_type" required>
        	<option value="">请选择考试</option>
        	@foreach($ExamList as $list)
            	<option value="{{$list->id}}">{{$list->name}}</option>
	        @endforeach
        </select>
    </div>
    <div class="examination_time">
		<span class="tit">&nbsp;&nbsp;考试时间</span>&nbsp;&nbsp;<span class="time"></span>
	</div>
    <ul id="exmination_ul">
    </ul>
@stop