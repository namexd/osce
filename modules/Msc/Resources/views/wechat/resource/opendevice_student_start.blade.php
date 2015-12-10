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
     	开放设备使用信息确认
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div class="w_90">
    <p class="font16 clo3 mart_5">请确认以下信息</p>
</div>
<form id="recheck_info" class="mart_3" method="post" action="{{ route('wechat.open-device.EquipmentConfirm') }}">
    <div class="add_main">
        <div class="form-group">
            <label for="">预约人</label>
            <div class="txt">
                {{ @$DevicePlanInfo['user']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">设备名称</label>
            <div class="txt">
                {{ @$DevicePlanInfo['device']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">设备编号</label>
            <div class="txt">
                {{ @$DevicePlanInfo['device']['code'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">时间段</label>
            <div class="txt">
                {{ @$DevicePlanInfo['begintime'] }}-{{ @$DevicePlanInfo['endtime'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">地址</label>
            <div class="txt">
                {{ @$DevicePlanInfo['device']['ResourcesClassroom']['location'] }}
            </div>
        </div>
    </div>
    <div class="w_90">
        <p class="font16 clo3 mart_5">注意事项</p>
        <div class="Reason">
            <textarea id="Reason_detail"  type="" placeholder="例：轻拿轻放"/></textarea>
        </div>
    </div>

    <div class="w_90">
        <input type="hidden" name="resources_device_id" value="{{ @$DevicePlanInfo['device']['id'] }}">
        <input type="hidden" name="opertion_uid" value="{{ @$DevicePlanInfo['opertion_uid'] }}">
        <input type="hidden" name="resources_lab_id" value="{{ @$DevicePlanInfo['device']['ResourcesClassroom']['id'] }}">
        <input type="hidden" name="resources_device_plan_id" value="{{ @$DevicePlanInfo['id'] }}">
        <input class="btn2" type="submit"  value="开始使用" />
    </div>
</form>



@stop