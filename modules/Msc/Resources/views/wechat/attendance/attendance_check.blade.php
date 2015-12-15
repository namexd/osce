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
     	考勤签到
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div class="w_90">
    <p class="font16 clo3 mart_5">请确认课程信息</p>
</div>
<form id="recheck_info" class="mart_3" method="post" action="{{ route('wechat.open-device.CloseDevice') }}">
    <div class="add_main">
        <div class="form-group">
            <label for="">课程名称</label>
            <div class="txt">
                医检
            </div>
        </div>
        <div class="form-group">
            <label for="">任课教师</label>
            <div class="txt">
                李老师
            </div>
        </div>
        <div class="form-group">
            <label for="">上课时间</label>
            <div class="txt">
                2015/11/25 09:00-10:00
            </div>
        </div>
        <div class="form-group">
            <label for="">上课地点</label>
            <div class="txt">
                新八教0203
            </div>
        </div>
    </div>
    <div class="w_90">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="uid" value="">
        <input class="btn2" type="submit"  value="确认签到" />
    </div>
</form>



@stop