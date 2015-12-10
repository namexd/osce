@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/resourcemanage/css/returnmanagement.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/resourcemanage/js/borrow_now.js')}}"></script>
    <script>
        $(document).ready(function(){
            var now_page=1;
            var qj={page:now_page};//设置页码
            var url = "{{ url('/msc/wechat/resources-manager/record-list-data') }}";
            var getdetail = "{{ url('/msc/wechat/resources-manager/borrow-now-attention') }}";
            gethistory(qj,url,getdetail);
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
     现有外借
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<div id="info_list" class="mart_5">
    <div class="main_list">
        <div class="title_nav">
            <div class=" title">模型</div>
            <div class=" title">编号</div>
            <div class=" title">时间段</div>
            <div class=" title">使用人</div>
            <div class=" title">操作</div>
        </div>
        <div class="detail_list">
            <ul>

            </ul>


        </div>
    </div>

</div>


@stop