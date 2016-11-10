@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<style>
.btn2{background: #1ab394}
.has-feedback label~.form-control-feedback {top: 26px;}
.form-group{
    width:96%;
    margin:10px 2%;
}
</style>
@stop
@section('only_head_js')
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
<script src="{{asset('osce/wechat/discussion/discussion.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'discussion_edit'}" />
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.getCheckQuestion',['id'=>$id])}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	编辑
    </div>
    <form class="quiz_form" action="{{  route('osce.wechat.postEditQuestion') }}" method="post" id="list_form">
		@foreach($list as $list)
		<div class="form-group">
	      <label class="" for="name">名称：</label>
	      <input type="text" class="form-control" name="title" id="" value="{{  $list->title }}" placeholder="请输入名称">
	    </div>
	    <div class="form-group">
	      <label class="" for="name">内容：</label>
	      <textarea class="form-control" style="height:100px;resize: none;" id="context" name="content" placeholder="请输入要反馈的内容,不超过200字~" rows="5">{{  $list->content  }}</textarea>
	    </div>
	    <div class="form-group">
			<input type="hidden" name="id" value="{{ $list->id }}">
    		<input class="btn btn2" type="submit" value="提交"/>
    	</div>
			@endforeach
    </form>
@stop