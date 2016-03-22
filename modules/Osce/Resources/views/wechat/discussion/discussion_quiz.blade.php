@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<style>
.btn2{background: #1ab394;width: 96%;}
.has-feedback label~.form-control-feedback {top: 26px;}
.tijiao{
        text-align: center;
    }
</style>
@stop
@section('only_head_js')
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
<script src="{{asset('osce/wechat/discussion/discussion.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'discussion_quiz'}" />
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.getDiscussionLists')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	提问
       	<a class="right header_btn" href="javascript:;">
            
        </a>
    </div>
    <form class="quiz_form" action="{{  route('osce.wechat.postAddQuestion') }}" method="post" id="list_form">
    	<div class="form-group" style="width:96%;margin:10px 2%;">
	      <label class="" for="name">名称：</label>
	      <input type="text" class="form-control" name="title" id="" placeholder="请输入名称">
	    </div>
	    <div class="form-group" style="width:96%;margin:0 2% 10px;">
	      <label class="" for="name">内容：</label>
	      <textarea class="form-control" style="height: 100px;resize:none;" id="context" name="content" placeholder="请输入要反馈的内容,不超过200字~" rows="5"></textarea>
	    </div>
	    <div class="form-group tijiao">
    		<input style="width:96%;margin:0 2%;" class="btn btn2" type="submit" value="提交"/>
    	</div>
    </form>
@stop