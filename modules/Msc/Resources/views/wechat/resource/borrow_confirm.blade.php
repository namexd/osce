@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/personalcenter/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    /*顶部轮播图片切换*/
    .swiper-container{width:100%;height:100%;}
    .swiper-slide{text-align:center;font-size:18px;background:#fff;
        display:-webkit-box;
        display:-ms-flexbox;
        display:-webkit-flex;display:flex;
        -webkit-box-pack:center;
        -ms-flex-pack:center;
        -webkit-justify-content:center;
        justify-content:center;
        -webkit-box-align:center;
        -ms-flex-align:center;
        -webkit-align-items:center;
        align-items:center;}
    .swiper-slide img{width: 100%;}
    .wrapper img{width:100%;}
    .swiper-pagination{ position:relative;}
    .swiper-pagination span{margin:0 0.2em 0 0 }

</style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/common/js/swiper.min.js')}}"></script>
    <script>
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            centeredSlides: true,
            autoplay: 2500,
            spaceBetween:30,
        });
    </script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
     	确认设备外借
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<form id="recheck_info" class="mart_3" method="post" action="{{ url('/msc/wechat/resources-manager/teacher-confirm') }}">
    <div class="add_main">
        <i class="fa fa-angle-right leibie"></i>
        <div class="form-group">
            <label for="">借用者</label>
            <div class="txt">
                {{ @$BorrowingList['user']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">设备名称</label>
            <div class="txt">
                {{ @$BorrowingList['resourcesTool']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">设备编号</label>
            <div class="txt">
                {{ @$BorrowingList['code'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">外借时段</label>
            <div class="txt">
                {{ @$BorrowingList['begindate'] }}-{{ @$BorrowingList['enddate'] }}
            </div>
        </div>
    </div>
    <div class="w_94 mart_10 marb_10">
        <input type="hidden" value="{{ @$BorrowingList['id'] }}" name="BorrowingId">
        <input class="btn2" type="submit"  value="确认外借" />
    </div>
</form>
@stop