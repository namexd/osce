@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>

@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script src="{{asset('msc/wechat/courseorder/js/course_search.js')}}"></script>

<script>

    $(document).ready(function(){
        if($('#get_type').html() == 'success'){
            if(layer.alert('操作成功')){
                window.location.href="/msc/wechat/course-order/course-list";
            }
        }
        var now_page=1;
        var qj={page:now_page,dateTime:today};//设置页码
        var url="{{ url('/msc/wechat/course-order/course-list-data') }}";
        var getdetail = "{{ url('/msc/wechat/course-order/course-confirm') }}";
        var getdetail2 = "{{ url('/msc/wechat/course-order/course-apply') }}";

        gethistory(qj,url,getdetail,getdetail2);
        var settime = $("#order_time").val(today);
        $("#select_submit").click(function () {//条件设置查询
            var settime = $("#order_time").val();
            var selected = $("#classroom").find("option:selected").val();
                 now_page=1;
            qj={page:now_page,dateTime:settime,resources_lab_id:selected}
            $(".detail_list ul").empty();
            gethistory(qj, url, getdetail, getdetail2);
            $("#room_all").text("全部");
        })

        //分页代码Begin
        $(window).scroll(function(e){
            /*判定到达底部*/
            if(away_top >= (page_height - window_height)&&now_page<totalpages){
                now_page++;
                qj.page=now_page;//设置页码
                gethistory(qj,url,getdetail,getdetail2)
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
        课程预约
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div id="get_type" style="display:none">{{@$_GET['type']}}</div>
<div class="history_time_select w_98">
    <p class="font16 clo3 mart_5">请设置您的查询条件</p>
    <div class="left">
        <input id="order_time" class="marb_10" name="begindate" type="date"  placeholder="查询日期" />
        <select name="classroom_id"  id="classroom" placeholder="请选择教室" style="width:100%;">
            <option id="room_all" value="">请选择教室</option>
            @foreach($resourcesClassroomList as $val)
                <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="right">
        <button class="btn3" id="select_submit">查询</button>
    </div>
</div>

<div id="info_list" class="mart_5">
    <div class="main_list" id="borrow_history">
        <div class="title_nav">
            <div class=" title">教室</div>
            <div class=" title">时间段</div>
            <div class=" title">状态</div>
            <div class=" title">操作</div>
        </div>
        <div class="detail_list">
            <ul>

            </ul>
        </div>
    </div>
</div>


@stop