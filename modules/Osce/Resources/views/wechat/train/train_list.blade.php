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
    	<li>
        	<a class="nou" href="#">
        		<p class="font14 fontb clo3 p_title">2015年第3季度技能培训学生考前培训</p>
        		<p class="font12 clo9 main_txt">临床技能中心10楼虚拟教学中心教室1</p>
        		<p class="font12 clo9 main_txt">2015-11-22 12:30 ~ 2015-11-22 15:00</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">张三</span>
        			<span class="clo9">3分钟前</span>
        			<span class="right comment">已读&nbsp;100</span>
        		</p>
        	</a>
        </li>
		<li>
        	<a class="nou" href="#">
        		<p class="font14 fontb clo3 p_title">2015年第3季度技能培训学生考前培训</p>
        		<p class="font12 clo9 main_txt">临床技能中心10楼虚拟教学中心教室1</p>
        		<p class="font12 clo9 main_txt">2015-11-22 12:30 ~ 2015-11-22 15:00</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">张三</span>
        			<span class="clo9">3分钟前</span>
        			<span class="right comment">已读&nbsp;100</span>
        		</p>
        	</a>
        </li>
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