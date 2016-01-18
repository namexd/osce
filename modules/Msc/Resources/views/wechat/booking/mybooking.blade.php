@extends('msc::wechat.layouts.admin')

@section('only_head_css')

    <link href="{{asset('msc/wechat/booking/css/booking.css')}}" rel="stylesheet" type="text/css" />
    <style>
        /*实验室名字超出长度修改*/
        .add_main .form-group label{width: 95px;}
        .add_main .form-group .txt {padding-left: 105px;}
    </style>
@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/booking/mybooking.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'mybooking','cancelUrl':'{{ route('msc.personalCenter.CancelApply') }}','detailUrl':'{{ route('msc.personalCenter.GetApplyDetails') }}','endUrl':'{{ route('msc.personalCenter.HistoryLaboratoryApplyList') }}'}" />
    <div class="user_header">我的预约</div>
    <div id="mybooking">
        <ul>
            <li class="check"> <span >待审核</span></li>
            <li> <span>待使用</span></li>
            <li> <span>已完成</span></li>
        </ul>
    </div>
    <div id="info_list" class="mart_5">
        <div id="now_borrow">
           @foreach($MyApplyList as $val)
            <div class="add_main" apply_id="{{ @$val['id'] }}">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">{{ @$val['Laboratory']['name'] }}</div>
                    <div class="state_btn1">
                        待审核
                    </div>
                </div>
                <div class="form-group">
                    <label for="">地址</label>
                    <div class="txt">{{ @$val['Laboratory']['FloorInfo']['name'] }}{{ @$val['Laboratory']['floor'] }}楼{{ @$val['Laboratory']['code'] }}</div>
                </div>
                <div class="form-group">
                    <label for="">预约日期</label>
                    <div class="txt">{{ @$val['apply_time'] }}</div>
                    <div class="submit_box" apply_id="{{ @$val['id'] }}">
                        <button  class="btn2">取消</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        @if($val['type'] == 1)
                            {{ substr(@$val['begintime'],0,5) }}-{{ substr(@$val['endtime'],0,5) }}
                            @elseif($val['type'] == 2)
                                @foreach($val['PlanApply'] as $item)
                                    {{ @$item['OpenPlan']['begintime'] }}-{{ @$item['OpenPlan']['endtime'] }}<br/>
                                @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div id="borrow_attention" style="display: none;">
            @foreach($MyPlanList as $val)
            <div class="add_main" apply_id="{{ @$val['id'] }}">
                <div class="form-group">
                    <label for="">实验室名称</label>
                    <div class="txt">{{ @$val['Laboratory']['name'] }}</div>
                    <div class="state_btn1">
                        已通过
                    </div>
                </div>
                <div class="form-group">
                    <label for="">地址</label>
                    <div class="txt">{{ @$val['Laboratory']['FloorInfo']['name'] }}{{ @$val['Laboratory']['floor'] }}楼{{ @$val['Laboratory']['code'] }}</div>
                </div>
                <div class="form-group">
                    <label for="">预约日期</label>
                    <div class="txt">{{ @$val['apply_time'] }}</div>
                    <div class="submit_box" apply_id="{{ @$val['id'] }}">
                        <button  class="btn2">取消</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">预约时段</label>
                    <div class="txt">
                        @if($val['type'] == 1)
                            {{ substr(@$val['begintime'],0,5) }}-{{ substr(@$val['endtime'],0,5) }}
                            @elseif($val['type'] == 2)
                                @foreach($val['PlanApply'] as $item)
                                    {{ @$item['OpenPlan']['begintime'] }}-{{ @$item['OpenPlan']['endtime'] }}<br/>
                                @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div id="complete" style="display: none;">

        </div>
    </div>
    <div id="sidepopup_layer">
        <div class="box_hidden"></div>
        <div class="box_content" >
            <div class="form_title" style="font-weight: 700;">预约信息</div>
            <div class="show_detail">
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
            <div class="form_title" style="font-weight: 700;">审核信息</div>
            <div class="show_detail">
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
    </div>
@stop