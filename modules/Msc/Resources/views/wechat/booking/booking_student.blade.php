@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />

<style>

</style>
@stop

@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/booking_student.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'booking_student','url':'{{ route('msc.Laboratory.OpenLaboratoryListData') }}'
    ,'target_url':'{{ route('msc.Laboratory.ApplyOpenLaboratory') }}'}" />
<div class="user_header">
   预约实验室
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
                    <option value="{{@$val['id']}}" floor_top="{{ @$val['floor_top'] }}" floor_bottom="{{ @$val['floor_bottom'] }}">{{@$val['name']}}</option>
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