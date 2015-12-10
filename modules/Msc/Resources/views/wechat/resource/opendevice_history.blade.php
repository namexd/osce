@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
<style rel="stylesheet">
    .main_list  .title_nav .title{    width:33.3%;}
    .detail_list ul li div{   width:33.3%;}
</style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script src="{{asset('msc/wechat/resourcemanage/js/opendevice_history.js')}}"></script>


<script>

    $(document).ready(function(){
        if($('#get_type').html() == 'success'){
            if(layer.alert('操作成功')){
                window.location.href="/msc/wechat/course-order/course-list";
            }
        }
        var now_page=1;
        var qj={page:now_page,dateTime:today};//设置页码
        var url="{{ url('/msc/wechat/open-device/history-list-data') }}";
        var getdetail = "{{ url('/msc/wechat/open-device/history-detail') }}";
        gethistory(qj,url,getdetail);
        $("#order_time").val(today);
        $("#select_submit").click(function () {
            var settime = $("#order_time").val();
                 now_page=1;
            qj={page:now_page,dateTime:settime}
            $(".detail_list ul").empty();
            gethistory(qj, url, getdetail);
            $("#room_all").text("全部");
        })
        //判定到底底部
        //分页代码Begin
        $(window).scroll(function(e){

            if(away_top >= (page_height - window_height)&&now_page<totalpages){
                page_height = $(document).height();
                away_top = $(document).scrollTop();//当前顶部到窗口顶部的距离
                window_height = $(window).height()
                now_page++;
                qj.page=now_page;//设置页码
                gethistory(qj,url,getdetail)
                /*加载显示*/
            }

        })
        //分页代码End
        $("#classroom").select2({});
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

<div id="get_type" style="display:none">{{@$_GET['type']}}</div>
<div class="history_time_select w_90">
    <div class="left2">
        <input id="order_time"  name="begindate" type="date"  placeholder="查询日期" />
    </div>

    <div class="right2">
        <button class="btn4" id="select_submit">查询</button>
    </div>
</div>

<div id="info_list" class="mart_5">
    <div class="main_list" id="borrow_history">
        <div class="title_nav">
            <div class=" title">设备</div>
            <div class=" title">时间段</div>
            <div class=" title">使用者</div>
        </div>
        <div class="detail_list">
            <ul>

            </ul>
        </div>
    </div>
</div>


@stop