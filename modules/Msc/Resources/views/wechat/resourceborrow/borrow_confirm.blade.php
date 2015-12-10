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
     	确认申请信息
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<form id="recheck_info" class="mart_3" method="post" action="{{ url('/msc/wechat/resources-manager/student-confirm') }}">
    <div class="add_main">
        <div class="form-group">
            <label for="">设备名称</label>
            <div class="txt">
                模型3
            </div>
        </div>
        <div class="form-group">
            <label for="">设备编号</label>
            <div class="txt">
                {{ $item['code'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">外借时段</label>
            <div class="txt">
                {{ $item['begindate'] }}-{{ $item['enddate'] }}
            </div>
        </div>
    </div>
    <div class="w_94 mart_10 marb_10">
        <input type="hidden" name="borrowingId" value="{{ $item['borrowingId'] }}">
        <input type="hidden" name="resources_tool_item_id" value="{{ $item['id'] }}">
        <input class="btn2" type="submit"  value="确认外借" />
    </div>
</form>



@stop