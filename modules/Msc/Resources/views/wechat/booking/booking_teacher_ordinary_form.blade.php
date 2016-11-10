@extends('msc::wechat.layouts.admin')

@section('only_head_css')

    <link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('msc/admin/js/Mobiscroll/mobiscroll.android-ics-2.5.2.css')}}" rel="stylesheet" type="text/css">
    <style>
        /*实验室名字超出长度修改*/
        .add_main .form-group label{width: 95px;}
        .add_main .form-group .txt {padding-left:100px;}
        .add_main .form-group input {padding-left:100px;}
        #now_borrow .submit_box{ top: 100px;}
        .form-control[readonly]{background-color: #fff;}
        .help-block{padding-left: 100px;}
    </style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/booking_teacher.js')}}"></script>



    <script src="{{asset('msc/admin/js/Mobiscroll/mobiscroll.core-2.5.2.js')}}" type="text/javascript"></script>
    <script src="{{asset('msc/admin/js/Mobiscroll/mobiscroll.core-2.5.2-zh.js')}}" type="text/javascript"></script>
    <link href="{{asset('msc/admin/js/Mobiscroll/mobiscroll.core-2.5.2.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('msc/admin/js/Mobiscroll/mobiscroll.animation-2.5.2.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('msc/admin/js/Mobiscroll/mobiscroll.datetime-2.5.1.js')}}" type="text/javascript"></script>
    <script src="{{asset('msc/admin/js/Mobiscroll/mobiscroll.datetime-2.5.1-zh.js')}}" type="text/javascript"></script>

    <!-- S 可根据自己喜好引入样式风格文件 -->

    <script src="{{asset('msc/admin/js/Mobiscroll/mobiscroll.android-ics-2.5.2.js')}}" type="text/javascript"></script>


    <script type="text/javascript">


    </script>
    <!-- E 可根据自己喜好引入样式风格文件 -->
@stop

@section('content')

    <input type="hidden" id="parameter" value="{'pagename':'booking_teacher_ordinary_form'}" />
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        预约普通实验室
        <a class="right header_btn" href="">

        </a>
    </div>
    <div id="info_list" class="mart_5">
        <div id="now_borrow">
            <div class="add_main">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">
                        {{ $data['LaboratoryInfo']['name']}}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">地址</label>
                    <div class="txt">
                        {{ $data['LaboratoryInfo']['FloorInfo']['address']}} {{ $data['LaboratoryInfo']['FloorInfo']['name']}}{{ $data['LaboratoryInfo']['floor']}}楼{{ $data['LaboratoryInfo']['code']}}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约日期</label>
                    <div class="txt">
                        {{ $data['ApplyTime']}}
                    </div>
                    <div class="submit_box">
                        <button  class="btn4 get_list_detail">查看资源清单</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">已占用时段</label>
                    <div class="txt" id="Take">
                        @foreach($data['LaboratoryInfo']['LabApply'] as $val)
                            <p><span>{{ @$val['begintime'] }}-{{ @$val['endtime'] }}</span>&nbsp;&nbsp;&nbsp;<span>{{ @$val['user']['name'] }}</span></p>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="add_main">
        <div class="form-group">
            <label for="">开始使用</label>
            <input type="text" class="form-control" id="begintime_set" name="begintime" value="">
        </div>
        <div class="form-group">
            <label for="">结束使用</label>
            <input type="text"  class="form-control"  id="endTime_set" name="endtime"  value="">
        </div>
    </div>
    <form action="{{route('msc.Laboratory.ApplyLaboratoryOp')}}" method="post" id="booking_teacher_form">
        <input name="date_time" value="{{ $data['ApplyTime']}}" type="hidden"/>
        <input name="lab_id" value="{{ $data['LaboratoryInfo']['id']}}" type="hidden"/>
        <input type="hidden"  id="begintime" name="begintime" value="">
        <input type="hidden"  id="endtime" name="endtime" value="">
        <div class="add_main">
            <div class="form-group">
                <label for="">教学课程</label>
                <input type="text" class="form-control"  name="course_name" value="">
            </div>
            <div class="form-group">
                <label for="">学生人数</label>
                <input type="number" class="form-control stu_num" value="" name="total">
            </div>
        </div>
        <div id="Reason_detail" class="w_94" >
            <div class="form_title">预约理由</div>
            <div class="form-group">
                <textarea name="description" class=" form-control textarea1" ></textarea>
            </div>
            <input class="btn2 mart_10 marb_10"  type="submit" value="提交预约">
        </div>
    </form>

    <div id="sidepopup_layer">
        <div class="box_hidden">
        </div>

        <div class="box_content" >
            <p class="font16 title">资源清单</p>

            <div class="main_list" id="inner-content">
                <div class="title_nav">
                    <div class="title_number title">序号</div>
                    <div class="title_name title">资源名称</div>
                    <div class="title_number title">资源类型</div>
                    <div class="title_number title">数量</div>
                </div>
                <div class="detail_list">
                    <ul class="inner-content">

                        @foreach($data['LadDeviceList'] as $k => $val)

                            <li>
                                <span class="title_number left">#{{@($k+1)}}</span>
                                <span class="title_name left">{{ @$val['DeviceInfo']['name'] }}</span>
                                <span class="title_number left">{{ @$val['devicesCateInfo']['name'] }}耗材</span>
                                <span class="title_number left">{{ @$val['total']}}</span>
                            </li>
                        @endforeach

                    </ul>
                </div>
            </div>

        </div>
    </div>
@stop