@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/resourcemanage/css/resourcemanage.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
     	开放实验室管理
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<ul class="manage_list">
    <li><a class="ablock nou" href="{{ url('/msc/wechat/open-laboratory/open-lab-list') }}"><i class="icons4 icon17"></i><span>预约申请管理 <i class="fa fa-angle-right i_right"></i></span></a></li>
</ul>
<ul class="manage_list">
    <li><a class="ablock nou" href="{{ route('wechat.open-device.HistoryList') }}"><i class="icons4 icon18"></i><span>历史记录 <i class="fa fa-angle-right i_right"></i></span></a></li>
</ul>
@stop