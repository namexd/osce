@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('only_head_js')

@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        预约开放设备申请
</div>
<form id="recheck_info" class="mart_3" method="post" action="{{url('/msc/wechat/open-device/open-tools-delay') }}">
    <div class="add_main">
        <div class="form-group">
            <label for="">使用设备</label>
            <div class="txt">
            	{{$data['name']}}
            </div>
        </div>
        <div class="form-group">
            <label for="">已使用</label>
            <div class="txt">
            	{{$data['usedTime']}}
            </div>
        </div>
    </div>
    <div class="add_main">
        <div class="form-group">
            <label for="">延长时间</label>
            <div class="txt">
            	{{$data['delayTime']}}
            </div>
        </div>
    </div>
    <div class="w_90">
    	<input type="hidden" name="deviceId" id="deviceId" value="{{$data['id']}}" />
    	<input type="hidden" name="timeSec" id="" value="{{$data['delayTime']}}" />
    	<input type="hidden" name="date" id="name" value="{{$data['date']}}" />
        <input class="btn2 mart_10" type="submit"  value="确认"/>
    </div>
</form>

@stop