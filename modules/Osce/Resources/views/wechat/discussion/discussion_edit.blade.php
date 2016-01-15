@extends('osce::wechat.layouts.admin')

@section('only_head_css')

@stop
@section('only_head_js')
@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	提问
    </div>
    <form class="quiz_form" action="{{  route('osce.wechat.postEditQuestion') }}" method="post">
		@foreach($list as $list)
		<div class="form-group">
	      <label class="" for="name">名称：</label>
	      <input type="text" class="form-control" name="title" id="" value="{{  $list->title }}" placeholder="请输入名称">
	    </div>
	    <div class="form-group">
	      <label class="" for="name">内容：</label>
	      <textarea class="form-control" id="context" name="content" placeholder="请输入要反馈的内容,不超过200字~" rows="5">{{  $list->content  }}</textarea>
	    </div>
	    <div class="form-group">
			<input type="hidden" name="id" value="{{ $list->id }}">
    		<input class="btn btn2" type="submit" value="提交"/>
    	</div>
			@endforeach
    </form>
@stop