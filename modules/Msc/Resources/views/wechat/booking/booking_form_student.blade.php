@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<style>
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt{padding-left: 100px;}
    #Reason_detail .reason_txt{width: 100%;height: 100px;border-radius: 4px;}
    #Reason_detail .marb_10{padding-left: 3%}
</style>
@stop

@section('only_head_js')

@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
   预约实验室
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<form action="" class="w_90 marb_10" method="post">
    <div>
        <div class="mart_5">
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
                        <div class="submit_box" style="width: 100px;">
                            <a  class="btn2" style="font-size: 14px">查看资源清单</a>
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
        </div>
        <div class="w_94" id="Reason_detail" >
            <div class="mart_10 marb_10 font16">预约原因</div>
            <div class="Reason">
                <textarea name="detail" placeholder="请输入预约原因" class="reason_txt">爱的方式的发生的公司法规的法规的发挥的恢复供货方根据非黄金护肤</textarea>
            </div>
            <input class="btn2 mart_10 marb_10" type="submit" value="提交预约">
        </div>
    </div>
</form>
@stop