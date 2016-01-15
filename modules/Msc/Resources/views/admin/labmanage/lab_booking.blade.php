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
            <form action="" method="get">
                <div class="col-xs-3">
                    <form action="" method="get">
                        <div class="laydate_div">
                            <input type="text" class="laydate-icon" name="laydate" id="laydate" placeholder="日期格式：YYYY-MM-DD" value="">
                        </div>
                    </form>
                </div>
                <div class="col-xs-9">
                    <label class="check_label checkbox_input check_one mart_5">
                        <div class="check_real check_icon display_inline marl_10 mart_3" ></div>
                        <input type="hidden" name="" value="">
                        <span class="right text-indent clof font14">开放实验室</span>
                    </label>
                    <label class="check_label checkbox_input check_one mart_5">
                        <div class="check_real check_icon display_inline marl_10 mart_3" ></div>
                        <input type="hidden" name="" value="">
                        <span class="right text-indent clof font14">普通实验室</span>
                    </label>
                    <button class="btn btn-success btn-pl marl_10" type="submit" style="margin-top: -10px">查询</button>
                </div>
            </form>
		</div>
        <div class="ibox float-e-margins">
            <div class="container-fluid ibox-content">
                <div class="col-md-6 marb_25">
                    <div class="show_box overflow">
                        <div class="w_40 left" >
                            <p class="font14 weight">医学临床实验室</p>
                            <p>临床医学教学楼三楼3-13</p>
                        </div>
                        <div class="w_60 left padl_20 border_left">
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">已预约<a href="" class="font16 blue student" data-toggle="modal" data-target="#myModal">30</a>人</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10"><a href="" class="font16 blue teacher" data-toggle="modal" data-target="#myModal">张三老师</a></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 marb_25">
                    <div class="show_box overflow">
                        <div class="w_40 left" >
                            <p class="font14 weight">医学临床实验室</p>
                            <p>临床医学教学楼三楼3-13</p>
                        </div>
                        <div class="w_60 left padl_20 border_left">
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">已预约<a href="" class="font16 blue student" data-toggle="modal" data-target="#myModal">30</a>人</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10"><a href="" class="font16 blue teacher" data-toggle="modal" data-target="#myModal">张三老师</a></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 marb_25">
                    <div class="show_box overflow">
                        <div class="w_40 left" >
                            <p class="font14 weight">医学临床实验室</p>
                            <p>临床医学教学楼三楼3-13</p>
                        </div>
                        <div class="w_60 left padl_20 border_left">
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">已预约<a href="" class="font16 blue student" data-toggle="modal" data-target="#myModal">30</a>人</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10"><a href="" class="font16 blue teacher" data-toggle="modal" data-target="#myModal">张三老师</a></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 marb_25">
                    <div class="show_box overflow">
                        <div class="w_40 left" >
                            <p class="font14 weight">医学临床实验室</p>
                            <p>临床医学教学楼三楼3-13</p>
                        </div>
                        <div class="w_60 left padl_20 border_left">
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">已预约<a href="" class="font16 blue student" data-toggle="modal" data-target="#myModal">30</a>人</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10"><a href="" class="font16 blue teacher" data-toggle="modal" data-target="#myModal">张三老师</a></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 marb_25">
                    <div class="show_box overflow">
                        <div class="w_40 left" >
                            <p class="font14 weight">医学临床实验室</p>
                            <p>临床医学教学楼三楼3-13</p>
                        </div>
                        <div class="w_60 left padl_20 border_left">
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">已预约<a href="" class="font16 blue student" data-toggle="modal" data-target="#myModal">30</a>人</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10">空闲</span>
                            </div>
                            <div class="marb_10">
                                <span>8:00-10:00</span>
                                <span class="marl_10"><a href="" class="font16 blue teacher" data-toggle="modal" data-target="#myModal">张三老师</a></span>
                            </div>
                        </div>
                    </div>
                </div>
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
                <div class="w_60 left font14 weight">
                    <span>临床医学实验室</span>
                    <span>（临床教学楼3楼3-13）</span>
                </div>
                <div class="w_40 right font14 weight txta_r">
                    <span>2015-12-31</span>
                    <span class="marl_10">8:00-10:00</span>
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
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>888888888</td>
                        <td>张三</td>
                        <td>2015级</td>
                        <td>临床医学</td>
                        <td>13888888888</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>888888888</td>
                        <td>张三</td>
                        <td>2015级</td>
                        <td>临床医学</td>
                        <td>13888888888</td>
                    </tr>
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
                <input type="text" class="form-control name add-name" name="timeInterval" value="8:00-10:00" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
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
                <textarea class="form-control add-name" disabled="disabled" name="detail">需要20个假体模型需要20个假体模型需要20个假体模型需要20个假体模型需要20个假体模型需要20个假体模型</textarea>
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