@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />

    <style>

    </style>
@stop

@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/booking_teacher.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'booking_teacher','url':'{{ route('msc.Laboratory.OpenLaboratoryListData') }}','type':'{{$type}}',
    'target_url':'{{ route('msc.Laboratory.ApplyOpenLaboratory') }}','url2':'{{ route('msc.Laboratory.LaboratoryListData') }}','target_url2':'{{ route('msc.Laboratory.ApplyLaboratory') }}'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        预约实验室
        <a class="right header_btn" href="#">

        </a>
    </div>
    <div class="main_body">
        <div class="time_select w_90">
            <div class="left2">
                <input id="order_time"  name="begindate" type="date"  placeholder="查询日期" />
            </div>
            <div class="right2">
                <button class="btn4" id="select_submit">筛选</button>
            </div>
        </div>
        <div class="manage_list">

        </div>

    </div>

    <div id="sidepopup_layer">
        <div class="box_hidden">
        </div>

        <div class="box_content" >
            <p class="font16 title">请选择具体楼栋或楼层</p>
            <div class="w_96">
                <select   class="select1" id="ban"  style="width:100%;">
                    <option value="" >全部楼栋</option>
                    @foreach($FloorData as $val)
                        <option value="{{@$val['id']}}" floor_top="{{ @$val['floor_top'] }}" floor_bottom="{{ @$val['floor_buttom'] }}">{{@$val['name']}}</option>
                    @endforeach
                </select>

                <select   class="select1 mart_10"  id="floor"  style="width:100%;">
                    <option value="">全部楼层</option>
                </select>
                <button id="submit_layer" type="button" class="btn1">确定</button>
            </div>
        </div>
    </div>
@stop