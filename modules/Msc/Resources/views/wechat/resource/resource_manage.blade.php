@extends('msc::wechat.layouts.admin')
@section('content')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/css/resourcemanage.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('only_head_js')

@stop

<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
    资源管理
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>

</div>

<ul class="manage_list">
    <li><a class="ablock nou" href="{{ url('/msc/wechat/resource/resource-list') }}"><i class="icon icon1"></i><span>现有资源 <i class="fa fa-angle-right i_right"></i></span></a></li>
    <li><a class="ablock nou" href="{{ url('/msc/wechat/resource/resource-add') }}"><i class="icon icon2"></i><span>新增资源 <i class="fa fa-angle-right i_right"></i></span></a></li>
</ul>

<ul class="manage_list">
    <li><a class="ablock nou" href="{{ url('/msc/wechat/resources-manager/borrow-teacher-manage') }}"><i class="icon icon3"></i><span>设备外借归还管理<i class="fa fa-angle-right i_right"></i></span></a></li>
    <li><a class="ablock nou" href="{{ route('wechat.open-laboratory.OpenLaboratoryManage')}}"><i class="icon icon4"></i><span>开放实验室管理<i class="fa fa-angle-right i_right"></i></span></a></li>
    <li><a class="ablock nou" href="{{ route('wechat.open-device.OpenDeviceManage') }}"><i class="icon icon5"></i><span>开放设备管理<i class="fa fa-angle-right i_right"></i></span></a></li>
    <li><a class="ablock nou" href="{{ url('/msc/wechat/open-laboratory/emergency-manage') }}"><i class="icon icon6"></i><span>突发事件管理<i class="fa fa-angle-right i_right"></i></span></a></li>
</ul>
@stop