@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/resourcemanage/reourcemanage.css')}}" rel="stylesheet" type="text/css" />
<style>
    .detail_list .name img{width: 55%;float: left;}
    .detail_list .name span{width: 45%;float: left;text-align: left;}
</style>
@stop

@section('only_head_js')

@stop

@section('content')

<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
   查看设备清单
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<div>
    <div class="mart_5 marb_5 form_title">
        <div><span>实验室名称：</span>临床技能室（13-1）</div>
        <div><span>地址：</span>新八教3楼</div>
    </div>
    <div class="main_list">
        <div class="title_nav">
            <div class="title_number title">序号</div>
            <div class="title_name title">资源名称</div>
            <div class="title_number title">资源类型</div>
            <div class="title_number title">数量</div>
        </div>
        <div class="detail_list" style="padding: 0;">
            <ul>
                <li style="line-height: 28px">
                    <span class="title_number left">1</span>
                    <span class="title_name left">听诊器</span>
                    <span class="title_number left">耗材</span>
                    <span class="title_number left">30</span>
                </li>
                <li style="line-height: 28px">
                    <span class="title_number left">2</span>
                    <span class="title_name left">假体模型</span>
                    <span class="title_number left">模型</span>
                    <span class="title_number left">20</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@stop