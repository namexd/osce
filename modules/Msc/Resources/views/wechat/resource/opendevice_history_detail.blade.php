@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<style>
    .gn_txt{height: auto;}
</style>
@stop

@section('only_head_js')

@stop

@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        历史记录详情
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>

    </div>
<form action="#">
    <div class="add_main mart_5">
        <div class="form-group">
            <label for="">设备名称</label>
            <div class="txt">
                {{ @$HistoryDetail['ResourcesDevice']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">使用时段</label>
            <div class="txt">
                {{ @$HistoryDetail['begin_datetime'] }}-{{ @$HistoryDetail['end_datetime'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">编号</label>
            <div class="txt">
                {{ @$HistoryDetail['ResourcesDevice']['code'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">地址</label>
            <div class="txt">
                {{ @$HistoryDetail['ResourcesDevice']['ResourcesClassroom']['location'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">使用者</label>
            <div class="txt">
                {{ @$HistoryDetail['user']['name'] }}
            </div>
        </div>
        <div class="form-group">
            <label for="">使用理由</label>
            <div class="txt">
                {{ @$HistoryDetail['ResourcesDeviceApply']['detail'] }}
            </div>
        </div>

        <div class="form-group">
            <label for="">设备状态</label>
            <div class="txt">
                @if (@$HistoryDetail['ResultInit']== 1)
                    良好
                @else
                    还有其他情况
                @endif
            </div>
        </div>
        <div class="form-group">
            <label for="">是否复位</label>
            <div class="txt">
                @if (@$HistoryDetail['ResultInit']== 1)
                    完美复位
                @else
                    还有其他情况
                @endif
            </div>
        </div>
    </div>

</form>

@stop
