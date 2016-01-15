@extends('msc::wechat.layouts.admin')

@section('only_head_css')

<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<style>
    /*实验室名字超出长度修改*/
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt {padding-left:105px;}
</style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/mybooking.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'mybooking_student'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       我的预约
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div id="wrapper2">
        <ul id="thelist2">
            <li class="check"> <span >待审核</span></li>
            <li> <span>待使用</span></li>
            <li> <span>已完成</span></li>
        </ul>
    </div>
    <div id="info_list" class="mart_5">
        <div id="now_borrow">
                <div class="add_main">
                    <div class="form-group">
                        <label for="">实验室名称</label>
                        <div class="txt">
                            临床实验室
                        </div>
                        <div class="state_btn1">
                            待审核
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
                            <a  class="btn4" href="">取消</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">预约时段</label>
                        <div class="txt">
                            8:00-10:00
                        </div>
                    </div>
                </div>
        </div>
        <div id="borrow_attention" style="display: none;">
            <div class="add_main">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">
                        临床实验室
                    </div>
                    <div class="state_btn1">
                        已通过
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
                        2016.1.2
                    </div>
                    <div class="submit_box">
                        <a  class="btn4" href="">取消</a>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        8:00-9:00
                    </div>
                </div>
            </div>
        </div>
        <div class="main_list"  style="display: none;">
            <div class="add_main">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">
                        临床实验室
                    </div>
                    <div class="state_btn1">
                        已通过
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
                        2016.1.2
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        8:00-9:00
                    </div>
                </div>
            </div>
            <div class="add_main">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">
                        临床实验室
                    </div>
                    <div class="state_btn2">
                        未通过
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
                        2016.1.2
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        8:00-9:00
                    </div>
                </div>
            </div>
            <div class="add_main">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">
                        临床实验室
                    </div>
                    <div class="state_btn3">
                        已过期
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
                        2016.1.2
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        8:00-9:00
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop