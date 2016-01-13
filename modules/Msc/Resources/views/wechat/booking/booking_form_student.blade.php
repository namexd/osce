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
    <script>
        $(document).ready(function() {
            $(".submit_box button").click(function () {
                get_layer();
            })
        });
    </script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
   预约实验室
    <a class="right header_btn" href="#">

    </a>
</div>

<div id="now_borrow" class="mart_5">
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
                <button  class="btn4" >查看资源清单</button>
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

<form action="" class=" marb_10" method="post">
        <div class="w_94" id="Reason_detail" >
            <div class="form_title">预约原因</div>
            <div class="Reason">
                <textarea name="detail" class="textarea1">爱的方式的发生的公司法规的法规的发挥的恢复供货方根据非黄金护肤</textarea>
            </div>
            <input class="btn2 mart_10 marb_10" type="submit" value="提交预约">
        </div>
    </div>
</form>


<div id="sidepopup_layer">
    <div class="box_hidden">
    </div>

    <div class="box_content" >
        <p class="font16 title">资源清单</p>

        <div class="main_list">
            <div class="title_nav">
                <div class="title_number title">序号</div>
                <div class="title_name title">资源名称</div>
                <div class="title_number title">资源类型</div>
                <div class="title_number title">数量</div>
            </div>
            <div class="detail_list" style="padding: 0;">
                <ul>
                    <li style="line-height: 28px">
                        <span class="title_number left">1</span>
                        <span class="title_name left">听诊器</span>
                        <span class="title_number left">耗材</span>
                        <span class="title_number left">30</span>
                    </li>
                    <li style="line-height: 28px">
                        <span class="title_number left">2</span>
                        <span class="title_name left">假体模型</span>
                        <span class="title_number left">模型</span>
                        <span class="title_number left">20</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>
@stop