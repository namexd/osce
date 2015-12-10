@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/information/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourceborrow/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
     	开放设备使用完成信息确认
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div class="w_90">
    <p class="font16 clo3 mart_5">请确认以下信息</p>
</div>
<form id="recheck_info" class="mart_3" method="post" action="{{ route('wechat.open-device.CloseDevice') }}">
    <div class="add_main">
        <div class="form-group">
            <label for="">使用设备</label>
            <div class="txt">
                {{ @$DevicePlanInfo['device']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">使用时间</label>
            <div class="txt">
                {{ @$DevicePlanInfo['currentdate'] }} <br/>{{ @$DevicePlanInfo['begintime'] }}-{{ @$DevicePlanInfo['endtime'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">共计</label>
            <div class="txt">
                {{ @$DevicePlanInfo['total_time'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">设备情况</label>
            <div class="txt">
                良好
            </div>
        </div>
    </div>
    <div class="w_90">
        <input type="hidden" name="id" value="{{ @$DevicePlanInfo['id'] }}">
        <input type="hidden" name="uid" value="{{ @$DevicePlanInfo['user']['id'] }}">
        <input class="btn2" type="submit"  value="设备复位，确认离开" />
    </div>
</form>



@stop