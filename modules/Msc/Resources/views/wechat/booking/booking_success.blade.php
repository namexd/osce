@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/index/css/index.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        实验室预约
        <a class="right header_btn" href="">

        </a>
    </div>
    <div class="error_attention">
        <img src="{{asset('msc/wechat/index/img/success.png')}}" />
        <p>实验室预约成功，请等待管理员审核！</p>
    </div>
@stop
