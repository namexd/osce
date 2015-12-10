@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
<style rel="stylesheet">
    .select2-container--default .select2-selection--single{ height: 32px;
        border: 1px solid #ccc;}
    .select2-container--default .select2-selection--single .select2-selection__arrow{top:3px;}

</style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script src="{{asset('msc/wechat/resource/js/opendevice_applylist.js')}}"></script>


<script>

    $(document).ready(function(){
        if($('#get_type').html() == 'success'){

            $.confirm({
                    title: '提示：',
                    content: '操作成功',
                    confirmButton: '　　　是　　　 ' ,
                    cancelButton: '　　　　　　',
                    confirmButtonClass: 'btn-info',
                    confirm:function(){
                         window.location.href="/msc/wechat/open-laboratory/open-lab-list";
                    }
                })
        }
        
        
        var now_page=1;
        var qj={page:now_page,dateTime:today};//设置页码
        var url="{{ url('/msc/wechat/open-laboratory/ajax-data') }}";
        var getdetail = "{{ url('/msc/wechat/open-laboratory/open-device-page') }}";


        gethistory(qj,url,getdetail);
         $("#order_time").val(today);
        $("#select_submit").click(function () {
            var settime = $("#order_time").val();
            var selected = $("#classroom").find("option:selected").val();
                 now_page=1;
            qj={page:now_page,dateTime:settime,resources_lab_id:selected}
            $(".detail_list ul").empty();
            gethistory(qj, url, getdetail );
            $("#room_all").text("全部");
        })
        //判定到底底部
        $(window).scroll(function(e){

            if(away_top >= (page_height - window_height)&&now_page<totalpages){
                now_page++;
                qj.page=now_page;//设置页码
                gethistory(qj,url,getdetail)
                /*加载显示*/
            }

        })

        $("#classroom").select2({});
    })



</script>


@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        预约申请管理
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
            <div class=" title">申请人</div>
            <div class=" title">审核</div>
        </div>
        <div class="detail_list">
            <ul>

            </ul>
        </div>
    </div>
</div>


@stop