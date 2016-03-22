@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('osce/wechat/css/discussion.css')}}" type="text/css" />
@stop
@section('only_head_js')
<script src="{{asset('osce/wechat/discussion/discussion.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'discussion_list','getUrl':'{{route('osce.wechat.getQuestionList')}}','URL':'{{route('osce.wechat.getCheckQuestion')}}','img':'{{asset('osce/wechat/common/img/pinglun.png')}}'}" />
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	讨论区
        <a class="right header_btn nou clof header_a" href="{{ route('osce.wechat.getAddQuestion') }}">提问</a>
    </div>
    <ul id="discussion_ul">
		
    </ul>
@stop