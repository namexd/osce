@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/css/returnmanagement.css')}}" rel="stylesheet" type="text/css" />

@stop
@section('only_head_js')
 <script src="{{asset('msc/wechat/resourcemanage/js/borrow_lishi.js')}}"></script>
 <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script>

    $(document).ready(function(){
        var now_page=1;
        var qj={page:now_page,dateTime:today};//设置页码
        var url = "{{ url('/msc/wechat/resources-manager/borrow-history-data') }}";
        var getdetail = "{{ url('/msc/wechat/resources-manager/borrow-history-detail') }}";
        gethistory(qj,url,getdetail);
        $("#star_time").val(today);
        $("#select_submit").click(function () {
            var star_time = $("#star_time").val();
            var end_time = $("#end_time").val();
            now_page=1;
            qj={page:now_page,begindate:star_time,enddate:end_time}
            $(".detail_list ul").empty();
            gethistory(qj, url, getdetail, getdetail);
            $("#room_all").text("全部");
        })
        //判定到底底部
        $(window).scroll(function(e){
            if(away_top >= (page_height - window_height)&&now_page<totalpages){
                now_page++;
                var qj={page:now_page};//设置页码
                gethistory(qj,url,getdetail)
                /*加载显示*/
            }
        })

    })



</script>


@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        外借历史
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>


<div class="history_time_select w_98">
    <div class="left">
        <p><label for="">开始日期:</label>  <input id="star_time" name="begindate" type="date" placeholder="起时间"/></p>
        <div class="clear"></div>
        <p><label for="">结束日期:</label><input id="end_time" name="enddate"  type="date" placeholder="终时间"/></p>
    </div>
    <div class="right">
        <button class="btn3" id="select_submit">查询</button>
    </div>
</div>

<div id="info_list" class="mart_5">
    <div class="main_list" id="borrow_history">
        <div class="title_nav">
            <div class=" title">名称</div>
            <div class=" title">编号</div>
            <div class=" title">时间段</div>
            <div class=" title">使用人</div>
            <div class=" title">作态</div>
        </div>
        <div class="detail_list">
            <ul>

            </ul>
        </div>
    </div>
</div>


@stop