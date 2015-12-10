@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
<style>
	.title_nav div{width: 33.33%!important;}
	.history_list li div{width: 33.33%;float: left;text-align: center;color: #6C6C6C;padding: 10px 0;}
</style>
@stop

@section('only_head_js')
	<script>
		$(function(){
			$("#select_submit").click(function(){
				var date=$("#order_time").val();				
				if(date.length==0){
					return;
				}
				$.ajax({
					type:"post",
					url:"{{ url('/msc/wechat/lab/openlab-history-list') }}",
					async:true,
					data:{
		            	date : date
		            },
					success:function(data){
						console.log(data);
					}
				});
			})
		
		})
	</script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
       	历史记录
</div>

<div class="history_time_select w_90">
    <div class="left2">
        <input id="order_time" class="marb_10" name="begindate" type="date"  placeholder="查询日期" />
    </div>
    <div class="right2">
        <button class="btn4" id="select_submit">查询</button>
    </div>
</div>

<div id="info_list" class="mart_5">
    <div class="main_list" id="borrow_history">
        <div class="title_nav">
            <div class=" title">教室</div>
            <div class=" title">时间段</div>
            <div class=" title">使用者</div>
        </div>
        <div class="history_list">
            <ul>
				<li><a href="#">
					<div>
						<span>教室</span>
					</div>
					<div>
						<p><span>12.02</span>-<span>11.26</span></p>
					</div>
					<div>
						<span>张三等三十人</span>
					</div>
				</a></li>
            </ul>
        </div>
    </div>
</div>


@stop