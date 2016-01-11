@extends('layouts.usermanage')

@section('only_css')

    <style>
        .clear_padding{
            padding: 0;
        }
        .clear_margin{
            margin: 0;
        }
        .border-bottom{
            border-bottom: none!important;
        }
        .btn-default{
            color: #9c9c9c;
        }
        .marb_none{margin-bottom: 0}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/labmanage/booking_examine.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'booking_examine'}"/>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">待处理</a></li>
                        <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">已处理</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-1" class="tab-pane active">
                            <div class="wrapper wrapper-content animated fadeInRight">
                                <div class="row table-head-style1 ">
                                    <div class="col-xs-6 col-md-3">
                                        <form action="" method="get">
                                            <div class="input-group">
                                                <input type="text" id="keyword" name="keyword" placeholder="请输入关键字" class="input-sm form-control" value="">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                                        </span>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-xs-6 col-md-9 user_btn">
                                        <button class="right btn btn-success all_refuse">批量不通过</button>
                                        <button class="right btn btn-success all_pass" style="margin-right: 10px">批量通过</button>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox float-e-margins">
                                <div class="container-fluid ibox-content">
                                    <form action="" class="wait_handle" id="list_form">
                                        <table class="table table-striped" id="table-striped">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <label class="check_label checkbox_input check_all marb_none">
                                                            <div class="check_real check_icon display_inline"></div>
                                                            <input type="hidden" name="" value="">
                                                        </label>
                                                    </th>
                                                    <th>序号</th>
                                                    <th>实验室名称</th>
                                                    <th>地址</th>
                                                    <th>预约日期</th>
                                                    <th>预约时段</th>
                                                    <th>申请人</th>
                                                    <th>申请时间</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <label class="check_label checkbox_input check_one marb_none">
                                                            <div class="check_real check_icon display_inline"></div>
                                                            <input type="hidden" name="" value="">
                                                        </label>
                                                    </td>
                                                    <td class="code">1</td>
                                                    <td class="name">临床技能实验室</td>
                                                    <td class="status">临床教学楼3楼3-13</td>
                                                    <td>
                                                        2015-1-12
                                                    </td>
                                                    <td class="code">
                                                        <p>8:00-10:00</p>
                                                        <p>10:00-12:00</p>
                                                    </td>
                                                    <td class="name">张三</td>
                                                    <td class="status">2015-1-1</td>
                                                    <td class="opera">
                                                        <a class="state1 pass" style="text-decoration: none"><span>通过</span></a>
                                                        <a class="state2 refuse" style="text-decoration: none" data-toggle="modal" data-target="#myModal"><span>不通过</span></a>
                                                        <a class="state1 detail" style="text-decoration: none" data-toggle="modal" data-target="#myModal"><span>详情</span></a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label class="check_label checkbox_input check_one marb_none">
                                                            <div class="check_real check_icon display_inline"></div>
                                                            <input type="hidden" name="" value="">
                                                        </label>
                                                    </td>
                                                    <td class="code">1</td>
                                                    <td class="name">临床技能实验室</td>
                                                    <td class="status">临床教学楼3楼3-13</td>
                                                    <td>
                                                        2015-1-12
                                                    </td>
                                                    <td class="code">
                                                        <p>8:00-10:00</p>
                                                        <p>10:00-12:00</p>
                                                    </td>
                                                    <td class="name">张三</td>
                                                    <td class="status">2015-1-1</td>
                                                    <td class="opera">
                                                        <a class="state1 pass" style="text-decoration: none"><span>通过</span></a>
                                                        <a class="state2 refuse" style="text-decoration: none" data-toggle="modal" data-target="#myModal"><span>不通过</span></a>
                                                        <a class="state1 detail" style="text-decoration: none" data-toggle="modal" data-target="#myModal"><span>详情</span></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div id="tab-2" class="tab-pane">
                            <div class="wrapper wrapper-content animated fadeInRight">
                                <div class="row table-head-style1 ">
                                    <div class="col-xs-6 col-md-3">
                                        <form action="" method="get">
                                            <div class="input-group">
                                                <input type="text" id="keyword" name="keyword" placeholder="请输入关键字" class="input-sm form-control" value="">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox float-e-margins">
                                <div class="container-fluid ibox-content">
                                    <form action="" class="wait_handle" id="list_form">
                                        <table class="table table-striped" id="table-striped">
                                            <thead>
                                            <tr>
                                                <th>序号</th>
                                                <th>实验室名称</th>
                                                <th>地址</th>
                                                <th>预约日期</th>
                                                <th>预约时段</th>
                                                <th>申请人</th>
                                                <th>申请时间</th>
                                                <th>状态</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="code">1</td>
                                                <td class="name">临床技能实验室</td>
                                                <td class="status">临床教学楼3楼3-13</td>
                                                <td>
                                                    2015-1-12
                                                </td>
                                                <td class="code">
                                                    <p>8:00-10:00</p>
                                                    <p>10:00-12:00</p>
                                                </td>
                                                <td class="name">张三</td>
                                                <td class="status">2015-1-1</td>
                                                <td class="opera">
                                                    <span class="state1">已通过</span>
                                                </td>
                                                <td class="opera">
                                                    <a class="state1 detail" style="text-decoration: none" data-toggle="modal" data-target="#myModal"><span>详情</span></a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="code">1</td>
                                                <td class="name">临床技能实验室</td>
                                                <td class="status">临床教学楼3楼3-13</td>
                                                <td>
                                                    2015-1-12
                                                </td>
                                                <td class="code">
                                                    <p>8:00-10:00</p>
                                                    <p>10:00-12:00</p>
                                                </td>
                                                <td class="name">张三</td>
                                                <td class="status">2015-1-1</td>
                                                <td class="opera">
                                                    <span class="state2">未通过</span>
                                                </td>
                                                <td>
                                                    <a class="state1 detail" style="text-decoration: none" data-toggle="modal" data-target="#myModal"><span>详情</span></a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')
    {{--详情       --}}
    <form class="form-horizontal" id="detail_from" novalidate="novalidate" action="" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">详情</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">实验室名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="临床医学实验室" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">地址</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="临床教学楼3楼3-13" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">预约日期</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="2015-12-31" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">预约时段</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="8:00-10:00;10:00-12:00" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">教学课程</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="导尿术" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">学生人数</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="20" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">申请人</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="张三（学号）" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">年级/专业</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="2015级临床医学" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">电话号码</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="13888888888" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">申请原因</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="课程需要" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">申请时间</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="2015-12-22" disabled="disabled"/>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary right" data-dismiss="modal">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                </div>
            </div>
        </div>
    </form>
    {{--不通过--}}
    <form class="form-horizontal" id="refuse_from" novalidate="novalidate" action="" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">提示</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">审核状态</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="不通过" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">不通过原因</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="reason"></textarea>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
@stop
