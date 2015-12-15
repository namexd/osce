@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link rel="stylesheet" href="{{asset('msc/wechat/personalcenter/css/documentation.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('msc/wechat/personalcenter/css/jalendar.css')}}" type="text/css" />
<style type="text/css">

</style>


@stop
@section('only_head_js')
    <script type="text/javascript" src="{{asset('msc/wechat/personalcenter/js/jalendar.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $('#myId').jalendar({
                color: '#ed145a', // Unlimited Colors
                lang: 'ZH' // Format: English — 'EN', Türkçe — 'TR'
            });
        });
    </script>

@stop


@section('content')

    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        我的课程
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>

    <div id="myId" class="jalendar">
        <div class="added-event" data-date="14/12/2015" data-time="Tüm Gün" data-title="WWDC 13 on San Francisco, LA"></div>
        <div class="added-event" data-date="16/12/2015" data-time="20:45" data-title="Tarkan İstanbul Concert on Harbiye Açık Hava Tiyatrosu"></div>
        <div class="added-event" data-date="17/12/2015" data-time="21:00" data-title="CodeCanyon İstanbul Meeting on Starbucks, Kadıköy"></div>
        <div class="added-event" data-date="17/12/2015" data-time="22:00" data-title="Front-End Design and Javascript Conferance on Haliç Kongre Merkezi"></div>
        <div class="added-event" data-date="17/12/2015" data-time="22:00" data-title="Lorem ipsum dolor sit amet"></div>
    </div>


@stop