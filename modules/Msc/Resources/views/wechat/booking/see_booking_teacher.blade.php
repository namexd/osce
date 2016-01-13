@extends('msc::wechat.layouts.admin')

@section('only_head_css')

<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<style>
    /*实验室名字超出长度修改*/
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt {padding-left: 105px;}
</style>
@stop
@section('only_head_js')

@stop

@section('content')
    <input type="hidden" id="parameter" value="" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       预约记录查看
        <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="mart_5">
        <div class="mart_5 marb_5 form_title" style="font-weight: 700;">预约信息</div>
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
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        8:00-10:00
                    </div>
                </div>
                <div class="form-group">
                    <label for="">教学课程</label>
                    <div class="txt">
                        导尿术
                    </div>
                </div>
                <div class="form-group">
                    <label for="">学生人数</label>
                    <div class="txt">
                        20
                    </div>
                </div>
                <div class="form-group">
                    <label for="">备注</label>
                    <div class="txt">
                        还需要20个假体模型
                    </div>
                </div>
                <div class="form-group">
                    <label for="">状态</label>
                    <div class="txt">
                        已通过
                    </div>
                </div>
            </div>
        </div>
        <div class="mart_5 marb_5 form_title" style="font-weight: 700;">审核信息</div>
        <div id="now_borrow">
            <div class="add_main">
                <div class="form-group">
                    <label for="">审核原因</label>
                    <div class="txt">
                        请按时使用该教室
                    </div>
                </div>
                <div class="form-group">
                    <label for="">审核人</label>
                    <div class="txt">
                        张三（2015.1.1）
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop