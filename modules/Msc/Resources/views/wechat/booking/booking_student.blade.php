@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
<style>
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt{padding-left: 100px;}
    .w85{width: 85%;}
    .w15{width: 15%;}
    .manage_list p{padding: 5px 8px 0 8px;}
    .manage_list div p:last-child{padding:0 8px 2px 8px;}
    .manage_list span{color: #9c9c9c; font-size: 12px;  }
    .manage_list{box-shadow: 0 1px 4px #DCE0E4; }
</style>
@stop

@section('only_head_js')

@stop

@section('content')
<div class="user_header">
   预约实验室
</div>
<div class="history_time_select w_90">
    <div class="left2">
        <input id="order_time"  name="begindate" type="date"  placeholder="查询日期" />
    </div>
    <div class="right2">
        <button class="btn4" id="select_submit">筛选</button>
    </div>
</div>
<div class=" marb_10">
    <div class="nav_list">
        <div class="manage_list">
            <div class="w85 left">
                <p>临床技能室</p>
                <p><span>临床教学楼13-1</span></p>
            </div>
            <div class="w15 right">
                <i class="fa fa-angle-right i_right" style="margin-top: 10px"></i>
            </div>
        </div>

        <div class="manage_list">
            <div class="w85 left">
                <p>临床技能室</p>
                <p><span>临床教学楼13-1</span></p>
            </div>
            <div class="w15 right">
                <i class="fa fa-angle-right i_right" style="margin-top: 10px"></i>
            </div>
        </div>

        <div class="manage_list">
            <div class="w85 left">
                <p>临床技能室</p>
                <p><span>临床教学楼13-1</span></p>
            </div>
            <div class="w15 right">
                <i class="fa fa-angle-right i_right" style="margin-top: 10px"></i>
            </div>
        </div>
    </div>
</div>
@stop