@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/discussion.css')}}" type="text/css" />
@stop
@section('only_head_js')
@stop

@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	讨论区
        <a class="right header_btn nou clof header_a" href="{{ route('osce.wechat.getAddQuestion')  }}">提问</a>
    </div>
    <ul id="discussion_ul">
		@foreach($list as $list)
        <li>
        	<a class="nou" href="{{ route('osce.wechat.getCheckQuestion',array('id'=>$list['id']))  }}">
        		<p class="font14 fontb clo3 p_title">{{  $list['title']  }}</p>
        		<p class="font12 clo9 main_txt">{{  $list['content']  }}</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">{{ $list['name']->name  }}</span>
        			<span class="clo0">·</span>
        			<span class="clo9">{{ $list['time']  }}</span>
        			<span class="right comment"><img src="{{asset('osce/wechat/common/img/pinglun.png')}}" height="16"/>&nbsp;{{ $list['count']  }}</span>
        		</p>
        	</a>
        </li>
		@endforeach
    </ul>
	<div class="row">
		<div class="pull-left">
			共{{$pagination->total()}}条
		</div>
		<div class="pull-right">
			<nav>
				<ul class="pagination">
					{!! $pagination->render() !!}
				</ul>
			</nav>
		</div>
	</div>
@stop