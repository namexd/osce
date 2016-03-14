@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
<link rel="stylesheet" href="{{asset('osce/wechat/common/css/weui.min.css')}}" type="text/css" />
<style>
    .form-group label{z-index:0!important;}
    .invigilation{
        margin:10px 3% 0;
        width:94%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .fa.fa-sort-desc{float: right;}
</style>
@stop
@section('only_head_js')
	<script type="text/javascript" src="{{asset('osce/wechat/js/examination.js')}}" ></script>
    <script src="{{asset('osce/wechat/js/jquerysession.js')}}"></script>

@stop

@section('content')
	<input type="hidden" id="parameter" value="{'pagename':'examination_list','ajaxurl':'{{route('osce.wechat.student-exam-query.getEveryExamList')}}','detailUrl':'{{route('osce.wechat.student-exam-query.getExamDetails')}}'}" />
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	成绩查询
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="form-group" style="border: none;">
        <span class="form-control normal_select select_indent invigilation" id="showActionSheet"><span>请选择考试</span><i class="fa fa-sort-desc"></i></span>
        <div id="actionSheet_wrap">
            <div class="weui_mask_transition" id="mask"></div>
            <div class="weui_actionsheet" id="weui_actionsheet">
                <div class="weui_actionsheet_menu" >
                @foreach($ExamList as $list)
                    <div class="weui_actionsheet_cell" value="{{$list->id}}">{{$list->name}}</div>
                @endforeach
                </div>
                <div class="weui_actionsheet_action">
                    <div class="weui_actionsheet_cell" id="actionsheet_cancel">取消</div>
                </div>
            </div>
        </div>
    </div>
    <div class="examination_time">
		<span class="tit">&nbsp;&nbsp;考试时间</span>&nbsp;&nbsp;<span class="time"></span>
	</div>
    <ul id="exmination_ul">
    </ul>
@stop