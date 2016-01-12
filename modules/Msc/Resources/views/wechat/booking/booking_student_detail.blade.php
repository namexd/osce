@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet" type="text/css" />
<style>
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt{padding-left: 100px;}
    .w85{width: 85%;}
    .w15{width: 15%;}
    .manage_list p{padding: 5px 8px 0 8px;}
    .manage_list div p:last-child{padding:0 8px 2px 8px;}
    .manage_list{box-shadow: 0 1px 4px #DCE0E4; }
    .check_one{margin: 15px 15px 0 0;}
</style>
@stop

@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/booking_student.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'booking_student'}" />
    <div class="user_header">
       预约实验室
    </div>
    <div class="mart_5 marb_5 form_title">
        <div>实验室名称：<span>临床技能室（13-1）</span></div>
        <div>地址：<span>新八教3楼</span></div>
        <div>
            预约日期：
            <span>2015-12-1</span>
            <div class="right" style="width: 96px">
                <a  class="btn2" style="font-size: 14px;padding: 3px 5px">查看资源清单</a>
            </div>
        </div>
    </div>
    <form action="" method="post">
        <div class=" marb_10">
            <div class="nav_list">
                <div class="manage_list">
                    <div class="w85 left">
                        <p>时段：<span>8:00-10:00</span></p>
                        <p>已预约/容量：<span class="have_booking">0</span>/<span class="capacity">30</span></p>
                    </div>
                    <div class="w15 right">
                        <label class="check_label checkbox_input check_one right">
                            <div class="check_real check_icon display_inline"></div>
                            <input type="hidden" name="" value="">
                        </label>
                    </div>
                </div>

                <div class="manage_list">
                    <div class="w85 left">
                        <p>时段：<span>10:00-11:00</span></p>
                        <p>已预约/容量：<span class="have_booking">30</span>/<span class="capacity">30</span></p>
                    </div>
                    <div class="w15 right">
                        <label class="check_label checkbox_input check_one right">
                            <div class="check_real check_icon display_inline"></div>
                            <input type="hidden" name="" value="">
                        </label>
                    </div>
                </div>

                <div class="manage_list">
                    <div class="w85 left">
                        <p>时段：<span>11:00-12:00</span></p>
                        <p>已预约/容量：<span class="have_booking">0</span>/<span class="capacity">30</span></p>
                    </div>
                    <div class="w15 right">
                        <label class="check_label checkbox_input check_one right">
                            <div class="check_real check_icon display_inline"></div>
                            <input type="hidden" name="" value="">
                        </label>
                    </div>
                </div>
            </div>
            <div class="w_94">
                <input class="btn2 mart_10 marb_10" type="submit" value="确认预约">
            </div>
        </div>
    </form>

@stop