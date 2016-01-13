@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />
<style>
    /*实验室名字超出长度修改*/
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt {padding-left:105px;}
</style>
@stop

@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/booking_student.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'open_teacher_detail'}" />
    <div class="user_header">
       预约实验室
    </div>

    <div id="now_borrow">
        <div class="add_main">
            <div class="form-group">
                <label for="">实验室名称</label>
                <div class="txt">
                    临床技能室（13-1）
                </div>
            </div>
            <div class="form-group">
                <label for="">地址</label>
                <div class="txt">
                    新八教
                </div>
            </div>
            <div class="form-group">
                <label for="">预约日期</label>
                <div class="txt">
                    2016.1.1
                </div>
                <div class="submit_box">
                    <button  class="btn4 get_list_detail">查看资源清单</button>
                </div>
            </div>
            <div class="form-group">
                <label for="">预约时段</label>
                <div class="txt">
                    8:00-10:00;10:00-12:00
                </div>
            </div>
        </div>
    </div>
    <form action="" method="post">

        <div class="manage_list">
            <div class="all_list">
                <div class="w_85 left">
                    <p>时段：<span>8:00-10:00</span></p>
                    <p>已预约/容量：<span class="have_booking">0</span><span>/</span><span class="capacity">30</span></p>
                </div>
                <div class="w_15 right">
                    <label class="check_label checkbox_input">
                        <div class="check_real check_icon display_inline" data="morning"></div>
                    </label>
                </div>
            </div>
            <div class="all_list">
                <div class="w_85 left">
                    <p>时段：<span>8:00-10:00</span></p>
                    <p>已预约/容量：<span class="have_booking">0</span><span>/</span><span class="capacity">30</span></p>
                </div>
                <div class="w_15 right">
                    <label class="check_label checkbox_input">
                        <div class="check_real check_icon display_inline" data="morning"></div>
                    </label>
                </div>
            </div>
            <div class="all_list">
                <div class="w_85 left">
                    <p>时段：<span>8:00-10:00</span></p>
                    <p>已预约/容量：<span class="have_booking">0</span><span>/</span><span class="capacity">30</span></p>
                </div>
                <div class="w_15 right">
                    <label class="check_label checkbox_input">
                        <div class="check_real check_icon display_inline" data="morning"></div>
                    </label>
                </div>
            </div>
        </div>
        <div class="w_94">
            <input class="btn2 mart_10 marb_10" type="submit" value="确认预约">
        </div>

    </form>

@stop