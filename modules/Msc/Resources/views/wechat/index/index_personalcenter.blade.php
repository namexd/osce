@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/index/css/index.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

@stop


@section('content')
<div class="user_tx">
  <div class="user_bg">
      <img src="{{asset('msc/wechat/personalcenter/img/tx_bg.png')}}"/>
  </div>
    <div class="tx_img">
        <img src="{{asset('msc/wechat/personalcenter/img/head_defualt.png')}}"/>
        <p>{{ @$user['name'] }}</p>
    </div>
</div>
<div class="clear"></div>
<div class="nav_list">
    <ul class="manage_list">
        <li><a class="ablock nou" href="{{route('msc.wechat.personalCenter.getMyApply')}}"><i class="icon icon1"></i><span>我的设备预约 <i class="fa fa-angle-right i_right"></i></span></a></li>
        <li><a class="ablock nou" href="{{route('msc.personalCenter.getMyOpeningLaboratory')}}"><i class="icon icon1"></i><span>我的实验室预约 <i class="fa fa-angle-right i_right"></i></span></a></li>
        <li><a class="ablock nou" href="{{ url('/msc/wechat/personal-center/personal-my-borrow') }}"><i class="icon icon2"></i><span>我的外借 <i class="fa fa-angle-right i_right"></i></span></a></li>
        <li><a class="ablock nou" href="{{ route('msc.personalCenter.MyCourse') }}"><i class="icon icon3"></i><span>我的课程<i class="fa fa-angle-right i_right"></i></span></a></li>
    </ul>
    <ul class="manage_list">
        <li><a class="ablock nou" href="{{ url('/msc/wechat/personal-center/info') }}"><i class="icon icon4"></i><span>修改个人信息<i class="fa fa-angle-right i_right"></i></span></a></li>
    </ul>
</div>

<div class="footer">
    <ul class="w_90">
        <li>
            <a href="#"><span class="icon1"></span><p>消息</p></a>
        </li>
        <li>
            <a href="{{ url('/msc/wechat/personal-center/info-manage') }}"><span class="icon2"></span><p>信息管理</p></a>
        </li>
        <li  class="check">
            <a href="#"><span class="icon3"></span><p>我</p></a>
        </li>
    </ul>

</div>
@stop