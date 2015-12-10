@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/resourcemanage/css/resourcemanage.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src=" https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
        wx.config(<?php echo $js->config(array('scanQRCode'), true, true) ?>);
        $(function(){
            $('#SaoYiSao').click(function(){
                wx.scanQRCode({
                    needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                    scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                    success: function (res) {
                        var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
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
     	设备外借
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<ul class="manage_list">
    <li><a class="ablock nou" href="{{ url('/msc/wechat/resources-manager/add-borrow-apply') }}"><i class="icons2 icon10"></i><span>外借申请 <i class="fa fa-angle-right i_right"></i></span></a></li>
    {{--<li><a class="ablock nou" href="{{ url('/msc/wechat/resources-manager/find-resource') }}"><i class="icons icon6"></i><span>借用时扫一扫 <i class="fa fa-angle-right i_right"></i></span></a></li>--}}
    <li><a class="ablock nou" href="javascript:void(0)" id="SaoYiSao"><i class="icons2 icon11"></i><span>借用时扫一扫 <i class="fa fa-angle-right i_right"></i></span></a></li>
</ul>
@stop