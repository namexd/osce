@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
<style type="text/css">
	.form-group{border-bottom:1px solid #e7eaec;}
	.cj_tab tr{border-bottom:1px solid #e7eaec;}
	.cj_tab tr.even{background:#F9F9F9;}
	.cj_tab th,.cj_tab td{border:none!important;}
	.cj_tab .see{color:#1ab394;}
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
       	<a class="right header_btn nou clof header_a" href="#">
       		<i class="icon_share"><img src="{{asset('osce/wechat/common/img/share.png')}}" width="18"/></i>
       	</a>
    </div>
    <div class="form-group">
        <select  id="examination" class="form-control normal_select select_indent invigilation" name="student_type" required>
        	<option value="">请选择考试</option>
        	@foreach($ExamList as $list)
            	<option value="{{$list->exam_id}}" data-id="{{$list->station_id}}">{{$list->exam_name}}</option>
	        @endforeach
        </select>
    </div>
    {{--<div class="examination_msg">--}}
		{{--<div class="form-group">--}}
            {{--<label for="">考试时间</label>--}}
            {{--<div class="txt" id="time">1</div>--}}
        {{--</div>--}}
        {{--<div class="form-group">--}}
            {{--<label for="">科目</label>--}}
            {{--<div class="txt" id="subject">肠胃炎</div>--}}
        {{--</div>--}}
        {{--<div class="form-group">--}}
            {{--<label for="">考试人数</label>--}}
            {{--<div class="txt" id="number">80人</div>--}}
        {{--</div>--}}
        {{--<div class="form-group">--}}
            {{--<label for="">平均用时</label>--}}
            {{--<div class="txt" id="time2">08：23</div>--}}
        {{--</div>--}}
        {{--<div class="form-group" style="border:none;">--}}
            {{--<label for="">平均成绩</label>--}}
            {{--<div class="txt" id="vgn">86</div>--}}
        {{--</div>--}}
        {{--<table class="table cj_tab">--}}
	  		{{--<tr>--}}
	  			{{--<th>考生姓名</th>--}}
	  			{{--<th>总分</th>--}}
	  			{{--<th>操作</th>--}}
			{{--</tr>--}}
	  	{{--</table>--}}
  	{{--</div>--}}
  	
@stop