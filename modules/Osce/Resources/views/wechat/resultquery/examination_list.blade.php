@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
@stop
@section('only_head_js')
	<script type="text/javascript" src="{{asset('osce/wechat/js/examination.js')}}" ></script>
@stop

@section('content')
	<input type="hidden" id="parameter" value="{'pagename':'examination_list','ajaxurl':'{{route('osce.wechat.student-exam-query.getEveryExamList')}}'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	成绩查询
       	<a class="right header_btn nou clof header_a" href="">
       		<i class="icon_share"><img src="{{asset('osce/wechat/common/img/share.png')}}" width="18"/></i>
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
    	<!--<li>
    		<dl>
    			<dd>考站1：82分</dd>
    			<dd>用时：18分23秒</dd>
    			<dd>评价老师：李老师</dd>
    		</dl>
    		<p class="clearfix see_msg">
    			<a class="nou right" href="#">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>
    		</p>
    	</li>
    	<li>
    		<dl>
    			<dd>考站2：82分</dd>
    			<dd>用时：18分23秒</dd>
    			<dd>评价老师：李老师</dd>
    			<dd>SP病人：SP病人</dd>
    		</dl>
    		<p class="clearfix see_msg">
    			<a class="nou right" href="#">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>
    		</p>
    	</li>
    	<li>
    		<dl>
    			<dd>考站3：82分</dd>
    			<dd>用时：18分23秒</dd>
    			<dd>理论考试</dd>
    		</dl>
    		<p class="clearfix see_msg">
    			<a class="nou right" href="#">考卷详情&nbsp;&gt;&nbsp;&nbsp;</a>
    		</p>
    	</li>-->
    </ul>
@stop