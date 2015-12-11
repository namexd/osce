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
				$("#layer_loading").show();//加载中显示
				$.ajax({
					type:"post",
					url:"{{ url('/msc/wechat/lab/openlab-history-list') }}",
					async:true,
					data:{
		            	date : date
		            },
					success:function(data){
						$("#layer_loading").hide();
						console.log(data);
							
						for (var i=0;i<data['data']['total'];i++) {
							var arr=data['data']['rows']['labHisList']['data'][i];							
							var id=arr['id'];
							var src='/msc/wechat/lab/openlab-history-item/'+id;	
							var name=arr['name'];
							var firsttime =arr['begin_datetime'].substring(5,11);
							var endtime = arr['end_datetime'].substring(5,11);
							var user=arr['user'];
							var str='<li><a href="'+src+'"><div>'+
							'<span>'+name+'</span></div><div>'+
							'<p><span>'+firsttime+'</span>-<span>'+endtime+'</span></p></div><div>'+
							'<span>'+user+'</span></div></a></li>';
							$("#history_list").append(str);
						}
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
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
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
            <ul id="history_list">
            </ul>
        </div>
    </div>
</div>


@stop