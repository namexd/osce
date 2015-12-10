@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourceborrow/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
     	使用完成信息确认
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div class="w_90">
    <p class="font16 clo3 mart_5">请确认以下信息</p>
</div>
<form id="recheck_info" class="mart_3" method="post" action="{{url('/msc/wechat/open-device/add-open-tools-history') }}">
    <div class="add_main">
        <div class="form-group">
            <label for="">使用者</label>
            <div class="txt">
            	{{$data['userName']}}
            </div>
        </div>
        <div class="form-group">
            <label for="">使用设备</label>
            <div class="txt">
            	{{$data['deviceName']}}
            </div>
        </div>
        <div class="form-group">
            <label for="">使用时间</label>
            <div class="txt">
            	{{$data['timeSec']}}
            </div>
        </div>
        <div class="form-group">
            <label for="">共计</label>
            <div class="txt">
            	{{$data['timeLengthHour']}} 小时{{$data['timeLengthMinute']}} 分 {{$data['timeLengthSecond']}} 秒
            </div>
        </div>
        <div class="form-group">
            <label for="">设备情况</label>
            <div class="txt">
            	@if($data['result_init']=="1")
            		良好
            	@elseif($data['result_init']=="2")
            		损坏
            	@elseif($data['result_init']=="3")
            		严重损坏
            	@endif
            </div>
        </div>
    </div>
    <div class="w_90">
    	<input type="hidden" name="deviceId" id="" value="{{$data['deviceId']}}" />
        <input class="btn2 mart_10" type="submit"  value="设备复位，确认离开"/>
        <a href="{{route('wechat.lab-tools.getOpenToolsDelay', ['deviceId'=>$data['deviceId'], 'planId'=>$data['planId']])}}"><input class="btn5 mart_10" type="button"  value="延时申请"/></a>
    </div>
</form>



@stop