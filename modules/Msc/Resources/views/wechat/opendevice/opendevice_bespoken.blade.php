@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<style>
	.class-box{background: #F8F8F8;color: #8CA8BF;height: 40px;line-height: 40px;border-bottom: 1px solid #E1E4E7;text-align: center;overflow: hidden;}
	.class-box span{display: inline-block;height: 39px;padding:0 5px;border-bottom:2px solid #F8F8F8}
	.class-box span.on{border-bottom: 2px solid #6595E1;color: #6595E1;}
	.number{line-height: 24px!important;}

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
				var id=$(".class-box span.on").attr("id");
				var date=$("#order_time").val();
				$("#layer_loading").show();//加载中显示
		    	$.ajax({
			        type:'post',
			        url:'/msc/wechat/open-device/open-tools-order-search',
			        data:{
			        	cate_id : id,
			        	date : date
			        },
			        async:true,
			        success:function(res){
			            if(res.code==1){
			                var data = res.data.rows.list.data;
			                for(var i in data){
			                    $(".detail_list ul").append(
			                        '<li id="'+data[i].id+'">'+
					                '<a href="/msc/wechat/open-device/open-tools-time-sec/'+data[i].id+'/'+date+'">'+
						                '<div class="name">'+
							                '<img src="'+data[i].url+'">'+
							                '<span>'+data[i].name+'</span>'+
						                '</div>'+
						                '<div class="number">'+data[i].code+'</div>' +
						            '</a>'+
					                '</li>'
			                    );
			                }
			                $("#layer_loading").hide(200);//加载结束消失
			            }
		       		}
		    	});	
			})
			
			$(".class-box div").click(function(){
				$(".class-box span").removeClass("on");
				if($(this).children().hasClass("on")){
					$(this).children().removeClass("on");
				}else{
					$(this).children().addClass("on");
				}
			})
			$("#select_submit").on('click',function(){
				
			})
		
		})
	</script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        开放设备预约
</div>

<div class="class-box">
	<div class="col-xs-6"><span class="on" id="1">腹腔镜</span></div>
	<div class="col-xs-6"><span class="" id="2">静脉刺穿</span></div>
</div>

<div class="history_time_select w_90">

    <div class="left2">
        <input id="order_time" class="marb_10" name="begindate" type="date"  placeholder="查询日期" />
    </div>

    <div class="right2">
        <button class="btn4" id="select_submit">查询</button>
    </div>
</div>

<div class="main_list">
    <div class="title_nav">
        <div class="name title">名称</div>
        <div class="number title">编号</div>
    </div>
    <div class="detail_list">
        <div class="search_none" style="display: none;">
            <span class="btn_search"><i class="fa fa-file-text-o"></i>
            </span>
        </div>
        <ul>
        	
        </ul>
    </div>
</div>


@stop