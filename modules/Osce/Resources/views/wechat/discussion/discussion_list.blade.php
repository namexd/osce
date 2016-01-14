@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<style type="text/css">
	.header_a:focus,.header_a:active{color:#fff;}
	#discussion_ul{overflow: hidden;}
	#discussion_ul li{width:100%;margin-bottom:10px;background:#fff;padding:0 15px 0;font-family:"microsoft yahei";}
	#discussion_ul .p_title{padding:10px 0;font-family: "microsoft yahei";overflow:hidden;text-overflow: ellipsis;white-space: nowrap;}
	#discussion_ul li .main_txt{line-height:1.5em;}
	.p_bottom{border-top:1px solid #ccc;line-height:2.5em;margin-top:10px;}
	.student_name{color:#16BEB0;}
	.comment{display:inline-block;color:#999;}
</style>
@stop
@section('only_head_js')
@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	查看
        <a class="right header_btn nou clof header_a" href="#">提问</a>
    </div>
    <ul id="discussion_ul">
        <li>
        	<a class="nou" href="#">
        		<p class="font14 fontb clo3 p_title">关于麻醉学基础知识中,麻醉的几个不能使用的场合</p>
        		<p class="font12 clo9 main_txt">关于麻醉学基础知识中,麻醉的几个不能使用的场合,听了不太明白，能否解释一下？</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">王同学</span>
        			<span class="clo0">·</span>
        			<span class="clo9">3分钟前</span>
        			<span class="right comment"><img src="{{asset('osce/wechat/common/img/pinglun.png')}}" height="16"/>&nbsp;99+</span>
        		</p>
        	</a>
        </li>
        <li>
        	<a class="nou" href="#">
        		<p class="font14 fontb clo3 p_title">关于麻醉学基础知识中,麻醉的几个不能使用的场合</p>
        		<p class="font12 clo9 main_txt">关于麻醉学基础知识中,麻醉的几个不能使用的场合,听了不太明白，能否解释一下？</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">王同学</span>
        			<span class="clo0">·</span>
        			<span class="clo9">3分钟前</span>
        			<span class="right comment"><img src="{{asset('osce/wechat/common/img/pinglun.png')}}" height="16"/>&nbsp;55</span>
        		</p>
        	</a>
        </li>
        <li>
        	<a class="nou" href="#">
        		<p class="font14 fontb clo3 p_title">关于麻醉学基础知识中,麻醉的几个不能使用的场合</p>
        		<p class="font12 clo9 main_txt">关于麻醉学基础知识中,麻醉的几个不能使用的场合,听了不太明白，能否解释一下？</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">王同学</span>
        			<span class="clo0">·</span>
        			<span class="clo9">3分钟前</span>
        			<span class="right comment"><img src="{{asset('osce/wechat/common/img/pinglun.png')}}" height="16"/>&nbsp;99+</span>
        		</p>
        	</a>
        </li>
    </ul>
@stop