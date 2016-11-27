@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .text-indent{text-indent: 10px}
        .show_box{border-radius: 5px;border: 1px solid #cccccc;box-shadow: 5px 5px 5px #cccccc;padding: 20px 10px;}
        .blue{color: #408AFF}
        .border_left{border-left: 1px solid #cecece}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/js/calendar3/laydate.js')}}"></script>
    <script src="{{asset('msc/admin/labmanage/booking_examine.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'lab_booking'}"/>
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row table-head-style1">
            <form action="" method="get" id="fm">
                <div class="col-xs-3">
                    <form action="" method="get">
                        <div class="laydate_div">
                            <input type="text" class="laydate-icon" name="laydate" id="laydate" placeholder="日期格式：YYYY-MM-DD" value="{{$nowtime}}">
                        </div>
                    </form>
                </div>
                <div class="col-xs-9">
                    <label class="check_label checkbox_input check_one mart_5">
                        <div class="check_real check_icon display_inline marl_10 mart_3 type @if($type == 2) check @endif" ></div>
                        <input type="hidden" name="type1" value="2">
                        <span class="right text-indent clof font14">开放实验室</span>
                    </label>
                    <label class="check_label checkbox_input check_one mart_5">
                        <div class="check_real check_icon display_inline marl_10 mart_3 type @if($type == 1) check @endif" ></div>
                        <input type="hidden" name="type2" value="1">
                        <span class="right text-indent clof font14">普通实验室</span>
                    </label>
                    <button class="btn btn-success btn-pl marl_10 sub" type="button" style="margin-top: -10px">查询</button>
                </div>
            </form>
		</div>

        <div class="ibox float-e-margins">
            <div class="container-fluid ibox-content">
                @if(@$Laboratory['data'])
                    @foreach(@$Laboratory['data'] as $k=>$v)
                <div class="col-md-6 marb_25">
                    <div class="show_box overflow">
                        <div class="w_40 left" >
                            <p class="font14 weight">{{@$v['name']}}</p>
                            <p>{{@$v['lname']}} {{@$v['floor']}}楼 {{@$v['code']}}</p>
                        </div>
                        <div class="w_60 left padl_20 border_left">
                            @if(@$type == 2)
                                @foreach(@$v['open_plan'] as $plan)
                                    {{--@if()--}}
                                        @if(@$plan['user_type'] != 2)
                                        <div class="marb_10">
                                            <span>{{@$plan['begintime']}}-{{@$plan['endtime']}}</span>
                                            <span class="marl_10"> @if(@$plan['apply_num'] && @$plan['plan_apply']) 已预约<a href="" class="font16 blue student" data-id="{{@$plan['apply_id']}}" data-time="{{@$plan['apply_time']}}" data-toggle="modal" data-target="#myModal">{{@$plan['apply_num']}}</a>人 @else 空闲 @endif</span>
                                        </div>

                                        @else
                                            <div class="marb_10">
                                                <span>{{@$plan['begintime']}}-{{@$plan['endtime']}}</span>
                                                <span class="marl_10"><a href="" class="font16 blue teacher" datatype="{{@$plan['user_type']}}" data-id="{{@$plan['apply_id']}}" data-toggle="modal" data-target="#myModal">{{@$plan['apply_name']}} {{@$plan['course_name']}} 课程使用</a></span>
                                            </div>
                                        @endif
                                    {{--@endif--}}
                                @endforeach
                            @else
                                @foreach($v['lab_apply'] as $apply)
                                    <div class="marb_10">
                                        <span>{{@$apply['begintime']}}-{{@$apply['endtime']}}</span>
                                        <span class="marl_10"><a href="" class="font16 blue teacher" datatype="{{@$apply['type']}}" data-id="{{@$apply['id']}}" data-toggle="modal" data-target="#myModal">{{@$apply['teacher']['name']}} {{@$apply['course_name']}} 课程使用</a></span>
                                    </div>
                                @endforeach
                            @endif
                            {{--<div class="marb_10">--}}
                                {{--<span>8:00-10:00</span>--}}
                                {{--<span class="marl_10">空闲</span>--}}
                            {{--</div>--}}
                            {{--<div class="marb_10">--}}
                                {{--<span>8:00-10:00</span>--}}
                                {{--<span class="marl_10">空闲</span>--}}
                            {{--</div>--}}
                            {{--<div class="marb_10">--}}
                                {{--<span>8:00-10:00</span>--}}
                                {{--<span class="marl_10">空闲</span>--}}
                            {{--</div>--}}
                            {{--<div class="marb_10">--}}
                                {{--<span>8:00-10:00</span>--}}
                                {{--<span class="marl_10"><a href="" class="font16 blue teacher" data-toggle="modal" data-target="#myModal">张三老师</a></span>--}}
                            {{--</div>--}}

                        </div>
                    </div>
                </div>
                @endforeach
                    @endif
            </div>
        </div>
        {{--分页--}}
        <div class="btn-group pull-right">

        </div>
    </div>

@stop

@section('layer_content')
{{--学生列表--}}
    <form class="form-horizontal" id="stu_from" novalidate="novalidate"  action="" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">学生列表</h4>
        </div>
        <div class="modal-body">
            <div class="marb_25">
                <div class="w_60 left font14 weight lab">
                    <span class="labname">临床医学实验室</span>
                    <span class="address">（临床教学楼3楼3-13）</span>
                </div>
                <div class="w_40 right font14 weight txta_r">
                    <span class="date">2015-12-31</span>
                    <span class="marl_10 time">8:00-10:00</span>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>学号</th>
                        <th>姓名</th>
                        <th>年级</th>
                        <th>专业</th>
                        <th>电话</th>
                    </tr>
                </thead>
                <tbody id="list">

                </tbody>
            </table>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary right"  type="button" data-dismiss="modal">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                </div>
            </div>
        </div>
    </form>
{{--老师预约详情--}}
<form class="form-horizontal" id="teacher_from" novalidate="novalidate" action="{{route('msc.admin.floor.postAddFloorInsert')}}" method="post">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">预约详情</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-3 control-label">实验室名称</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="name" value="临床医学实验室" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">地址</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="address" value="新八教" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">预约日期</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="bookingTime" value="2015-12-31" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">预约时段</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name timeInterval" name="timeInterval" value="8:00-10:00" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group claue">
            <label class="col-sm-3 control-label">教学课程</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="teaching" value="导尿术" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">学生人数</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="number" value="20" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">申请人</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="bookingPerson" value="张三（工号）" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">备注</label>
            <div class="col-sm-9">
                <textarea class="form-control add-name detail" disabled="disabled" name="detail">需要20个假体模型需要20个假体模型需要20个假体模型需要20个假体模型需要20个假体模型需要20个假体模型</textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">申请时间</label>
            <div class="col-sm-9">
                <input type="text" class="form-control name add-name" name="applyTime" value="2015-12-25" disabled="disabled"/>
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="form-group">
            <div class="col-sm-4 col-sm-offset-2 right">
                <button class="btn btn-primary right"  type="button" data-dismiss="modal">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
            </div>
        </div>
    </div>
</form>
 @stop