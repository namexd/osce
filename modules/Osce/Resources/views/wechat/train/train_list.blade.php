@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/train.css')}}" type="text/css" />
@stop
@section('only_head_js')
@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	考前培训
       	<a class="right header_btn nou clof header_a" href="#"></a>
    </div>
    <ul id="discussion_ul">
		@foreach($data as $data)

    	<li>
        	<a class="nou" href="{{ route('osce.wechat.getTrainDetail',array('id'=>$data['id']))  }}">
        		<p class="font14 fontb clo3 p_title">{{  $data['name'] }}</p>
        		<p class="font12 clo9 main_txt">{{  $data['address'] }}</p>
        		<p class="font12 clo9 main_txt">{{  $data['begin_dt'] }} ~ {{  $data['end_dt'] }}</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">{{ $data['author']->name }}</span>
        			<span class="clo9">{{  $data['time'] }}</span>
        			<span class="right comment">已读&nbsp;100</span>
        		</p>
        	</a>
        </li>
		@endforeach
    </ul>
	<div class="">
		<div class="pull-left">
			共{{$pagination->total()}}条
		</div>
		<div class="pull-right">
			<nav>
				<ul class="pagination">
					{!! $pagination->render() !!}
					<li>1</li>
					<li>2</li>
					<li>3</li>
				</ul>
			</nav>
		</div>
	</div>
@stop