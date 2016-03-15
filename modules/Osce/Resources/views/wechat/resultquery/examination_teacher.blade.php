@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
@stop
@section('only_head_js')
	<script type="text/javascript" src="{{asset('osce/wechat/js/examination.js')}}" ></script>
@stop

@section('content')
	<input type="hidden" id="parameter" value="{'pagename':'examination_teacher','ajaxurl':'{{route('osce.wechat.student-exam-query.getEveryExamList')}}','detailUrl':'{{route('osce.wechat.student-exam-query.getEveryExamList')}}'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	成绩查询
       	<a class="right header_btn nou clof header_a" href="#">
       		<i class="icon_share"><img src="{{asset('osce/wechat/common/img/share.png')}}" width="18"/></i>
       	</a>
    </div>
	<div class="examination_msg" style="margin: 15px 0;">
		<div class="form-group">
			<label for="">考试名称</label>
			<div class="txt">{{$examName->name}}</div>
		</div>
		<div class="form-group">
			<label for="">考生姓名</label>
			<div class="txt">{{$studentInfo->name}}</div>
		</div>
		<div class="form-group">
			<label for="">电话</label>
			<div class="txt">{{$studentInfo->mobile}}</div>
		</div>
	</div>

    <div class="examination_time">
		<span class="tit">&nbsp;&nbsp;考试时间</span>&nbsp;&nbsp;{{date('Y-m-d',strtotime($examName->begin_dt))}}~{{date('Y-m-d',strtotime($examName->end_dt))}}<span class="time"></span>
	</div>
    <ul id="exmination_ul">
		@foreach($stationData as $item)

			@if($item['type']==1)
		<li>
            <dl>
                <dd>{{$item['station_name'].":".$item['score'].'分'}}</dd>
                <dd>用时：{{intval($item['time']/60)."分". round(($item['time']/60)-intval($item['time']/60))}}秒</dd>
                <dd style="width:100%">评价老师：{{$item['grade_teacher']}}</dd>
            </dl>
            <p class="clearfix see_msg">
                <a class="nou right" href="{{route('osce.wechat.student-exam-query.getExamDetails',['exam_screening_id'=>$item['exam_screening_id'],'station_id'=>$item['station_id']])}}">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>
            </p>
        </li>
			@elseif($item['type']==2)

        <li>
            <dl>
                <dd>{{$item['station_name'].":".$item['score'].'分'}}</dd>
                <dd>用时：{{intval($item['time']/60)."分". round(($item['time']/60)-intval($item['time']/60))}}秒</dd>
                <dd class="tbl_type"><div class="tbl_cell" style="width:72px">评价老师：</div><div class="tbl_cell">{{$item['grade_teacher']}}</div></dd>
                <dd>SP病人：{{$item['sp_name']}}</dd>
            </dl>
            <p class="clearfix see_msg">
                <a class="nou right" href="{{route('osce.wechat.student-exam-query.getExamDetails',['exam_screening_id'=>$item['exam_screening_id'],'station_id'=>$item['station_id']])}}">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>
            </p>
        </li>
			@elseif($item['type']==3)
        <li>
            <dl>
                <dd>{{$item['station_name'].":".$item['score'].'分'}}</dd>
                <dd>用时：{{intval($item['time']/60)."分". round(($item['time']/60)-intval($item['time']/60))}}秒</dd>
                <dd style="width:100%">理论考试</dd>
            </dl>
            <p class="clearfix see_msg">
                <a class="nou right" href="{{route('osce.wechat.student-exam-query.getExamDetails',['exam_screening_id'=>$item['exam_screening_id'],'station_id'=>$item['station_id']])}}">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>
            </p>
        </li>
    </ul>
	@endif
	@endforeach
@stop