@extends('msc::wechat.layouts.admin')

@section('only_head_css')

<link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />
<style>
    /*实验室名字超出长度修改*/
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt {padding-left: 100px;}
</style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/booking_student.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'open_teacher_write'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       预约实验室
        <a class="right header_btn" href="">

        </a>
    </div>
    <div id="info_list" class="mart_5">
        <div id="now_borrow">
            <div class="add_main">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">
                        临床实验室
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
    </div>
    <form action="" method="post" id="myform">
        <div class="add_main">
            <div class="form-group">
                <label for="">教学课程</label>
                <input type="text" class="form-control" value="" name="course">
            </div>
            <div class="form-group">
                <label for="">学生人数</label>
                <input type="number" class="form-control stu_num" value="" name="num">
            </div>
        </div>
        <div class="w_94" >
            <div class="form_title">备注</div>
            <div class="Reason">
                <textarea class="textarea1" style="width: 100%;height: 100px;border-radius: 4px;">爱的方式的发生的公司法规的法规的发挥的恢复供货方根据非黄金护肤</textarea>
            </div>
            <input class="btn2 mart_10 marb_10" type="submit" value="提交预约">
        </div>
    </form>
@stop