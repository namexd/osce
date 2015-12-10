@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<style>
	.title_nav div{width: 33.33%!important;}
	.detail_list li div{width: 33.33%;color: #6C6C6C}
</style>
@stop

@section('only_head_js')
	<script>
		$(function(){
			var name=$(".name").text().split('\n')[1];
			$(".top-title").text(name);
			var date=window.location.href.split('/')[8];
			var url=$(".url").attr("href").replace('@',date);
			$(".url").attr("href",url);
		})
	</script>
	
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
   	<span class="top-title"></span>
</div>


    <div class="main_list">
         <div class="title_nav">
            <div class=" title">设备名称</div>
            <div class=" title">时间段</div>
            <div class=" title">可预约状态</div>
        </div>
        <div class="detail_list">
	        <ul>
	        	@foreach ($data as $list)
	        	<li>
					<div>
						<span class="name">
							@if($list['id']=="1")
							腹腔镜
							@elseif($list['id']=="2")
							静脉刺穿
							@endif
						</span>
					</div>
					
					<div>
						<p>{{$list['time']}}</p>
					</div>
					
					<div>
						@if($list['status']=="1")
						<span style="color: #a2a2a2;">已预约</span>
						@elseif($list['status']=="2")
						<a class="url" href="/msc/wechat/open-device/open-tools-apply/{{$list['id']}}/@/{{$list['time']}}" style="color: #88ACE7;">可预约</a>
						@endif
					</div>
				</li>
				@endforeach
	        </ul>
	    </div>
    </div>


@stop