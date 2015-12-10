@extends('msc::admin.layouts.admin')
@section('only_css')
  <link rel="stylesheet" href="{{asset('msc/admin/eqreturnmanage/css/history.css')}}">
  <style rel="stylesheet" >
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
        -moz-border-bottom-colors: none;
        -moz-border-left-colors: none;
        -moz-border-right-colors: none;
        -moz-border-top-colors: none;
        background: 0 0;
        border-color: #ddd #ddd rgba(0,0,0,0);
        border-bottom: #fff;
        border-image: none;
        border-style: solid;
        border-width: 1px;
        color: #555;
        cursor: default;
    }
    .state-ing{color: #72CFBC;}
    .state-before{color: #F9BB77;}
    .item-info{margin: 5px 0;}
    .laydate-icon, .laydate-icon-default, .laydate-icon-danlan, .laydate-icon-dahong, .laydate-icon-molv {width: 180px;}

    /*layer alert*/
    .layui-layer-title{
        background: #fff!important;
        color: #408aff!important;
        font-size: 16px!important;
    }
    .layui-layer-btn{
        background: #fff!important;
        border-top: 1px #fff solid!important;
    }
    .state2 {
        color: #ed5565;
    }
    .change-select p{
        font-size: 14px;
        line-height: 30px;
    }
    /*excel upload*/
    #file-insert li{position: relative;}
    #file-insert li a input{
        display:inline;
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        opacity:0;
    }

    /*屏幕宽度*/
    @media (max-width:1366px){
        .res-control{height: 60px;}
        .res-control .input-group{margin-top: 30px;}
        .Examine{margin: 5px;}
        #start,#end{margin: 5px;}
    }

  </style>
@stop
@section('only_js')


@stop

@section('content')

<div class="row">
    <div class="col-sm-8">
        
        <div class="wrapper wrapper-content animated fadeInRight">
            <!-- title -->
            <div class="row table-head-style1 ">
                <div class="col-xs-6 col-md-6">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start">
                            <input placeholder="结束日期" class="form-control layer-date laydate-icon" id="end">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-md-3 res-control">
                    <div class="input-group">
                        <input type="text" placeholder="请输入教室名称" id="search-input" class="input-sm form-control">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                    </span>
                    </div>

                </div>
                <div class="col-xs-6 col-md-3" style="float: right;">
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-white marl_10 dropdown-toggle" type="button">下载模版 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{route('msc.courses.downloadCoursesListTpl')}}">课程清单模版</a>
                            </li>
                            <li>
                                <a href="{{route('msc.courses.downloadCoursesPlanTpl')}}">课程计划导入清单</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group Examine">
                        <button data-toggle="dropdown" class="btn btn-primary marl_10 dropdown-toggle" type="button">导入课程 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" id="file-insert">
                            <li>
                                <a href="javascript:void(0)" id="file-not-local">课程清单导入<input type="file" name="courses" id="file0" multiple="multiple" /></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" id="file-local">课程计划导入<input type="file" name="plan" id="file1" multiple="multiple" /></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="container-fluid ibox-content">
                  <!-- 选项卡 -->
                  <ul class="nav nav-tabs" role="tablist" id="tab-page">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基础课程</a></li>
                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">紧急课程</a></li>
                    <li role="presentation"><a href="#train" aria-controls="train" role="tab" data-toggle="tab">岗前培训</a></li>
                  </ul>
                  <!-- 主体内容 -->
                  <div class="tab-content">
                    <!-- 基础课程 -->
                    <div role="tabpanel" class="tab-pane active" id="home">
                        <table class="table table-striped" id="table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <div class="btn-group Examine">
                                        <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">课程名称 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu order-course">
                                            <li value="1">
                                                <a href="javascript:void(0)">升序</a>
                                            </li>
                                            <li value="-1">
                                                <a href="javascript:void(0)">降序</a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>日期</th>
                                <th>时间</th>
                                <th>
                                    <div class="btn-group Examine">
                                        <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">教室 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu order-classroom">
                                            <li value="1">
                                                <a href="javascript:void(0)">升序</a>
                                            </li>
                                            <li value="-1">
                                                <a href="javascript:void(0)">降序</a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>小组</th>
                                <th>老师</th>
                                <th>联系电话</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody><!-- 
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span>已结束</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">课件</span></a>
                                            <a href="javascript:void(0)"><span class="edit state1">视频</span></a>
                                            <span class="Scrap state1" data-toggle="modal" data-target="#myModal" data-id="41" data-name="123123">报告</span>
                                            <span class="Print state1" data-toggle="modal" data-target="#myModal" data-resource-id="30">详情</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span class="state-ing">进行中</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">编辑</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span class="state-before">未开始</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">监控</span></a>
                                        </div>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                        <div class="btn-group pull-right">
                            <ul class="pagination" value="23"></ul>
                        </div>
                    </div>
                    <!-- 紧急课程 -->
                    <div role="tabpanel" class="tab-pane" id="profile">
                        <table class="table table-striped" id="table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <div class="btn-group Examine">
                                        <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">课程名称 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu order-course">
                                            <li value="1">
                                                <a href="javascript:void(0)">升序</a>
                                            </li>
                                            <li value="-1">
                                                <a href="javascript:void(0)">降序</a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>日期</th>
                                <th>时间</th>
                                <th>
                                    <div class="btn-group Examine">
                                        <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">教室 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu order-classroom">
                                            <li value="1">
                                                <a href="javascript:void(0)">升序</a>
                                            </li>
                                            <li value="-1">
                                                <a href="javascript:void(0)">降序</a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>小组</th>
                                <th>老师</th>
                                <th>联系电话</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody><!-- 
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span>已结束</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">课件</span></a>
                                            <a href="javascript:void(0)"><span class="edit state1">视频</span></a>
                                            <span class="Scrap state1" data-toggle="modal" data-target="#myModal" data-id="41" data-name="123123">报告</span>
                                            <span class="Print state1" data-toggle="modal" data-target="#myModal" data-resource-id="30">详情</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span>已结束</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">编辑</span></a>
                                            <a href="javascript:void(0)"><span class="read state2">取消</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span>已结束</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">监控</span></a>
                                        </div>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                        <div class="btn-group pull-right">
                            <ul class="pagination"></ul>
                        </div>
                    </div>
                    <!-- 岗前培训 -->
                    <div role="tabpanel" class="tab-pane" id="train">
                        <table class="table table-striped" id="table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>
                                    <div class="btn-group Examine">
                                        <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">课程名称 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu order-course">
                                            <li value="1">
                                                <a href="javascript:void(0)">升序</a>
                                            </li>
                                            <li value="-1">
                                                <a href="javascript:void(0)">降序</a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>日期</th>
                                <th>时间</th>
                                <th>
                                    <div class="btn-group Examine">
                                        <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">教室 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu order-classroom">
                                            <li value="1">
                                                <a href="javascript:void(0)">升序</a>
                                            </li>
                                            <li value="-1">
                                                <a href="javascript:void(0)">降序</a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>小组</th>
                                <th>老师</th>
                                <th>联系电话</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody><!-- 
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span>已结束</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">记录</span></a>
                                            <a href="javascript:void(0)"><span class="edit state1">详情</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span class="state-before">未开始</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">监控</span></a>
                                            <a href="javascript:void(0)"><span class="edit state2">取消</span></a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>医检</td>
                                    <td>2015/9/11</td>
                                    <td>8:00-15:00</td>
                                    <td>临床技能中心7F</td>
                                    <td>一组</td>
                                    <td>窦祥阳</td>
                                    <td>18500124557</td>
                                    <td><span class="state-before">未开始</span></td>
                                    <td>
                                        <div class="opera">
                                            <a href="javascript:void(0)"><span class="read  state1">编辑</span></a>
                                        </div>
                                    </td>
                                </tr> -->
                            </tbody>
                        </table>
                        <div class="btn-group pull-right">
                            <ul class="pagination"></ul>
                        </div>
                    </div>
                  </div>
            </div>
        </div>
    </div>
    <!-- 右侧 -->
    <div class="col-sm-4">
        <div class="ibox float-e-margins">
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="ibox-title">
                    <h5>紧急预约通知</h5>
                    <span class="label label-warning-light">新消息</span>
                </div>
                <div class="ibox-content timeline" id="timeline"><!-- 
                    <div class="timeline-item">
                        <div class="row">
                            <div class="col-xs-3 date ui-sortable">
                                <i class="fa fa-clock-o"></i> 2015-01-12 15:00
                                <br>
                            </div>
                            <div class="col-xs-7 content ui-sortable">
                                <div class="item-info">
                                    <label>课程内容:</label>
                                    <span>方法性综合评价</span>
                                </div>
                                <div class="item-info">
                                    <label>预约教室:</label>
                                    <span>临床技能中心7F（可预约多个教室）</span>
                                </div>
                                <div class="item-info">
                                    <label>预约时间:</label>
                                    <span>2015/09/18 08:00-15:00</span>
                                </div>
                                <div class="item-info">
                                    <label>申请老师:</label>
                                    <span>李老师</span>
                                </div>
                                <div class="item-info">
                                    <label>联系方式:</label>
                                    <span>13811111111</span>
                                </div>
                                <div class="item-info">
                                    <label>课程人数:</label>
                                    <span>60人</span>
                                </div>
                                <div class="item-info">
                                    <button class="btn btn-white">拒绝</button>
                                    <button class="btn btn-primary">通过</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="row">
                            <div class="col-xs-3 date ui-sortable">
                                <i class="fa fa-clock-o"></i> 2015-01-12 15:00
                                <br>
                            </div>
                            <div class="col-xs-7 content ui-sortable">
                                <div class="item-info">
                                    <label>课程内容:</label>
                                    <span>方法性综合评价</span>
                                </div>
                                <div class="item-info">
                                    <label>预约教室:</label>
                                    <span>临床技能中心7F（可预约多个教室）</span>
                                </div>
                                <div class="item-info">
                                    <label>预约时间:</label>
                                    <span>2015/09/18 08:00-15:00</span>
                                </div>
                                <div class="item-info">
                                    <label>申请老师:</label>
                                    <span>李老师</span>
                                </div>
                                <div class="item-info">
                                    <label>联系方式:</label>
                                    <span>13811111111</span>
                                </div>
                                <div class="item-info">
                                    <label>课程人数:</label>
                                    <span>60人</span>
                                </div>
                                <div class="item-info">
                                    <button class="btn btn-white">拒绝</button>
                                    <button class="btn btn-primary">通过</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="row">
                            <div class="col-xs-3 date ui-sortable">
                                <i class="fa fa-clock-o"></i> 2015-01-12 15:00
                                <br>
                            </div>
                            <div class="col-xs-7 content ui-sortable">
                                <div class="item-info">
                                    <label>课程内容:</label>
                                    <span>方法性综合评价</span>
                                </div>
                                <div class="item-info classroom">
                                    <label>预约教室:</label>
                                    <span>临床技能中心7F（可预约多个教室）</span>
                                </div>
                                <div class="item-info">
                                    <label>预约时间:</label>
                                    <span>2015/09/18 08:00-15:00</span>
                                </div>
                                <div class="item-info">
                                    <label>申请老师:</label>
                                    <span>李老师</span>
                                </div>
                                <div class="item-info">
                                    <label>联系方式:</label>
                                    <span>13811111111</span>
                                </div>
                                <div class="item-info">
                                    <label>课程人数:</label>
                                    <span>60人</span>
                                </div>
                                <div class="item-info" value="11">
                                    <button class="btn btn-white modal-control" data-toggle="modal" data-target="#myModal" flag="no">拒绝</button>
                                    <button class="btn btn-primary modal-control" data-toggle="modal" data-target="#myModal" flag="yes">通过</button>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>


    </div>
</div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')
    <!-- 拒绝 -->
    <form class="form-horizontal" id="Form2" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">拒绝申请</h4>
        </div>
        <div class="modal-body" id="rejectValue">

            <div class="form-group">
                <label class="col-sm-3 control-label">拒绝原因：</label>
                <div class="col-sm-9">
                    <select class="form-control" id="choose">
                        <option value="已损坏">已损坏</option>
                        <option value="已借出">已借出</option>
                        <option value="">自定义理由</option>
                    </select>
                    <textarea id="comment" name="comment" class="form-control" required="" aria-required="true"></textarea>

                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id='apply-no' class="notAgree" data-dismiss="modal" aria-hidden="true">提交</button>
        </div>
    </form>
    <!-- 通过 -->
    <form class="form-horizontal" id="Form3" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">通过申请</h4>
        </div>
        <div class="modal-body" id="valueName">
        <div class="emergency-1" style="display:none;">是否通过情急预约申请？</div>
        <div class="emergency-2" style="display:none;">
            <p>紧急课程与以下课程发生冲突</p>
            <p class="edit state2">课程一： <span id="meet-info">临床技能中心7F 2015/09/18 08:00-15:00</span></p>
            <p>请执行以下课程变更。</p>
            <div class="form-group">
                <label class="col-sm-2 control-label">调整方式</label>
                <div class="col-sm-10">
                    <select class="form-control" id="recommend-edit">
                        <option value="1">推荐新的教室与时间</option>
                        <option value="2">修改有冲突的基础课程内容</option>
                    </select>
                </div>
            </div>
            <br/>
            <hr/>
            <div class="change-select">
            <!-- 推荐选择 -->
                  <div class="change-recommend">
                      <div class="form-group">
                        <label class="col-sm-2">推荐</label>
                        <div class="col-sm-10">
                          <div class="form-control" style="height:146px;">
                              <p>您所申请的时间段，课程无法做出调整。请重新选择教室时间段。</p>
                              <label>系统为您推荐最近的空闲教室与世间段</label>
                              <p>推荐教室：<span id="recommend-classroom">A教室</span></p>
                              <p>推荐时间段：<span id="recommend-time">2015/09/18 08:00-15:00</span></p>
                          </div>
                        </div>
                      </div>
                  </div>  
                  <div class="change-edit" style="display:none;">
                      <div class="form-group">
                          <label class="col-sm-2 control-label">现时安排</label>
                          <div class="col-sm-10"><p>开放性伤口包扎课程：临床技能中心7F 2015/09/18 08:00-15:00</p></div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-2 control-label"><label>变更安排</label></div>
                          <div class="col-sm-10"><p>开放性伤口包扎课程</p></div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-2 control-label"><label>&nbsp;</label></div>
                          <div class="col-sm-10">
                            <select class="form-control" id="classroom-chioce">
                                <option value="1">临床技能中心7F</option>
                                <option value="2">临床技能中心8F</option>
                            </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-2 control-label"><label>&nbsp;</label></div>
                          <div class="col-sm-10">
                            <select class="form-control" id="classroom-time">
                                <option value="1">时间</option>
                                <option value="2">世间2</option>
                            </select>
                          </div>
                      </div>
                  </div>
            </div>
        </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success agree" id="apply-yes" data-dismiss="modal" aria-hidden="true">确&nbsp;定</button>
        </div>
    </form>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
    <script>
    $(function(){

        /*时间选择*/
        var start = {
            elem: "#start",
            format: "YYYY-MM-DD hh:mm:ss",
            min: "1970-00-00 00:00:00",
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function (a) {
                end.min = a;
                end.start = a
            }
        };
        var end = {
            elem: "#end",
            format: "YYYY-MM-DD hh:mm:ss",
            min: "1970-00-00 00:00:00",
            max: "2099-06-16 23:59:59",
            istime: true,
            istoday: false,
            choose: function (a) {
                start.max = a
            }
        };
        laydate(start);
        laydate(end);

        /*取消弹出层*/
        $('.tab-pane').on('click','.state2',function(){
            /*确认数据传递*/
            layer.alert('是否取消该课程',callback);
            //取消
            var thisElement = $(this);
            function callback(){
                var num = thisElement.parent().parent().parent().parent().parent().parent().parent().attr('id');
                //岗前培训取消
                if(num=='profile'){
                    $.ajax({
                        type:"get",
                        async:true,
                        url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@getCancelPlan')}}",
                        data:{id:thisElement.parent().parent().attr('value'),type:'provisional'},
                        success:function(res){
                            if(res.code!=1){
                                console.log(res.message);
                                layer.alert('取消失败！',function(){
                                    location.reload();
                                });
                            }else{
                                location.reload();
                            }
                        }
                    });
                }else{
                    //紧急 取消
                    $.ajax({
                        type:"get",
                        async:true,
                        url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@getCancelPlan')}}",
                        data:{id:thisElement.parent().parent().attr('value'),type:'training'},
                        success:function(res){
                            if(res.code!=1){
                                console.log(res.message);
                                layer.alert('取消失败！',function(){
                                    location.reload();
                                });
                            }else{
                                location.reload();
                            }
                        }
                    });
                }
            }
        });

        /*详情页面跳转*/
        $('.tab-pane').on('click','.detail',function(){
            var thisElement = $(this);
            location.href = "{{route('msc.courses.courses')}}?id="+thisElement.parent().parent().attr('value');
        });

        /*编辑页面跳转*/
        $('.tab-pane').on('click','.edit-item',function(){
            var thisElement = $(this);
            location.href = "{{route('msc.courses.coursesEdit')}}?id="+thisElement.parent().parent().attr('value');
        });
        

        /*模态框选择*/
        $('.timeline').on('click','.item-info .modal-control',function(){
            var num = ['no','yes'];

            if($(this).attr('flag')==num[0]){
                //清空，获取id
                //$('#Form2').attr('value',$(this).parent().parent().parent().attr('value'));
                $('#comment').val('');
                $('#Form2').show();
                $('#Form3').hide();
            }else{
                //$('#Form3').attr('value',$(this).parent().parent().parent().attr('value'));
                //$('#start').val('');
                //$('#end').val('');
                //$('#Form3').find('select').val('');
                $('#Form3').show();
                $('#Form2').hide();
            }
        });

        /*不通过下拉切换*/
        $('#choose').change(function(){
            var val=$(this).val();
            if(val.length>0)
            {
                $('#comment').val(val);
            }
        });

        /*默认时间*/
        function timeTest(data){
            var res;
            if(data<10){
                res = '0' + data;
            }else{
                res = data;
            }
            return res;
        }
        var now_time = new Date();
        var start_time = now_time.getFullYear() +'-'+ timeTest(parseInt(now_time.getMonth()+parseInt(1))) +'-'+ timeTest(now_time.getDate()) + ' 00:00:00';
        var end_time = now_time.getFullYear() +'-'+ timeTest(parseInt(now_time.getMonth()+parseInt(1))) +'-'+ timeTest(now_time.getDate()) + ' ' +timeTest(now_time.getHours())+':'+timeTest(now_time.getMinutes())+':'+timeTest(now_time.getSeconds());//00:00:00';
        $('#start').val(start_time);
        $('#end').val(end_time);


        /*分页初始化*/
        function InitPage(res/*总页数*/,elem/*分页UI外层dom*/){
            var html = '';
            //分页html标签的li必须清空
            if(elem.find('li').length!=0)return;
            //当总页数大于5时，初始化处理
            if(parseInt(res)<5){
                html += '<li flag="pre" value="1"><span>«</span></li>';
                for(var i=1;i<=res;i++){
                    html += '<li flag="page" value="'+i+'"><span>'+i+'</span></li>';
                }
                html += '<li value="' +(parseInt(i) -1)+'" flag="last"><span>»</span></li>';
                elem.find('li').remove();
                elem.html(html);
                //active聚焦位置
                elem.find('li').removeClass('active');
                elem.find('li').eq(1).addClass('active');
            }else{
               html += '<li flag="pre" value="1"><span>«</span></li>';
                for(var i=1;i<=5;i++){
                    html += '<li flag="page" value="'+i+'"><span>'+i+'</span></li>';
                }
                html += '<li value="' +(parseInt(i) -1)+'" flag="last"><span>»</span></li>';
                elem.find('li').remove();
                elem.html(html);
                elem.find('li').removeClass('active');
                elem.find('li').eq(1).addClass('active'); 
            }
        }

        /*分页控制*/
        function pagination(elem/*翻页外层dom*/,fun/*function翻页数据更新*/){
            elem.find('.pagination').on('click','li',function(){
                var thisElement = $(this);
                var reqInit = {};
                //请求数据
                reqInit['bagindate'] = $('#start').val();
                reqInit['enddate'] = $('#start').val();
                reqInit['field'] = 'classroom';//此处关键字先默认为classroom。还可以为course，看后期需求
                reqInit['keyword'] = $('#search-input').val();
                if(thisElement.attr('flag')=='page'){
                    //点击翻页
                    thisElement.parent().find('li').removeClass('active');
                    thisElement.addClass('active');
                    reqInit['page'] = thisElement.attr('value');
                    //基础课程ajax请求
                    fun(reqInit);
                }else if(thisElement.attr('flag')=='last'){
                    //测试页数
                    var totalPage = $('#home .pagination').attr('value');
                    var num = 5;
                    //向后加5页
                    var last = thisElement.attr('value');
                    reqInit['page'] = (parseInt(last)+1);
                    //最后一页
                    if(parseInt(last)==totalPage){
                        return;
                    }
                    //翻到最后几页
                    if((parseInt(last) + num)>=totalPage){
                        num = totalPage - parseInt(last);
                    }
                    fun(reqInit);
                    var html = '<li flag="pre" value="'+(parseInt(last)+1)+'"><span>«</span></li>';
                    for(var i = 1;i<=num;i++){
                        html = html +'<li value="'+(parseInt(thisElement.attr('value')) +i)+'" flag="page"><span>'+(parseInt(thisElement.attr('value')) +i)+'</span></li>'
                    }
                    html += '<li value="'+(parseInt(thisElement.attr('value')) +i -1)+'" flag="last"><span>»</span></li>';
                    $('#home .pagination li').remove();
                    $('#home .pagination').html(html);
                    $('#home .pagination').find('li').removeClass('active');
                    $('#home .pagination').find('li').eq(5).addClass('active');
                }else{
                    //向前翻5页
                    var pre = thisElement.attr('value');
                    reqInit['page'] = (parseInt(pre)-5);
                    //到第一页
                    if(pre==1)return;
                    fun(reqInit);
                    var html = '<li flag="pre" value="'+parseInt(pre-5)+'"><span>«</span></li>';
                    for(var i = 5;i>=1;i--){
                        html = html +'<li value="'+(parseInt(thisElement.attr('value')) -i)+'" flag="page"><span>'+(parseInt(thisElement.attr('value')) -i)+'</span></li>'
                    }
                    html += '<li value="'+(parseInt(thisElement.attr('value')) -i -1)+'" flag="last"><span>»</span></li>';
                    $('#home .pagination li').remove();
                    $('#home .pagination').html(html);
                    $('#home .pagination').find('li').removeClass('active');
                    $('#home .pagination').find('li').eq(1).addClass('active');
                }
            })
        }

        /*面包屑*/
        $('#tab-page').on('click','li',function(){
            var elem = $(this).find('a').attr('href');
            //默认数据
            var req = {};
            req['bagindate'] = $('#start').val();
            req['enddate'] = $('#start').val();
            req['field'] = 'classroom';//此处关键字先默认为classroom。还可以为course，看后期需求
            req['keyword'] = $('#search-input').val();
            req['page'] = 1;
            //控制搜索字段
            if(req['keyword']==''){
                req['field'] = '';
            }
            switch(elem){
                  case '#home':
                   nomalCourse(req)
                   break;
                  case '#profile':
                   emergencyCourse(req)
                   break;
                  default:
                   stuffTrain(req);
                   break;
            }
        });

        /*基础课程*/
        var reqInit = {};
        reqInit['bagindate'] = $('#start').val();
        reqInit['enddate'] = $('#start').val();
        reqInit['field'] = 'classroom';//此处关键字先默认为classroom。还可以为course，看后期需求
        reqInit['keyword'] = $('#search-input').val();
        reqInit['page'] = 1;
        if(reqInit['keyword']==''){
            reqInit['field'] = '';
        }
        nomalCourse(reqInit);
        function nomalCourse(req){
            //状态
            var state_status = {
                '1':'<span>已结束</span>',
                '-1':'<span class="state-before">未开始</span>',
                '0':'<span class="state-ing">进行中</span>'
            };
            //操作
            var option = {
                '1':'<a href="javascript:void(0)"><span class="read  state1">课件</span></a><a href="javascript:void(0)"><span class="read  state1">视频</span></a><a href="javascript:void(0)"><span class="read  state1">报告</span></a><a href="javascript:void(0)"><span class="read  state1 detail">详情</span></a>',
                '0':'<a href="javascript:void(0)"><span class="read  state1">监控</span></a>',
                '-1':'<a href="javascript:void(0)"><span class="read  state1 edit-item">编辑</span></a>'
            }
            $.ajax({
                type:"get",
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@getNormalCoursesPlanData')}}",
                async:true,
                data:JSON.stringify(req),
                success:function(res){
                    if(res.code==1){
                        var data = res.data.rows;
                        var html = '';
                        for(var i in data){
                            html +='<tr>'+
                                        '<td>'+data[i].id+'</td>'+
                                        '<td>'+data[i].courses+'</td>'+
                                        '<td>'+data[i].currentdate+'</td>'+
                                        '<td>'+data[i].begintime+'-'+data[i].endtime+'</td>'+
                                        '<td>'+data[i].classroom+'</td>'+
                                        '<td>'+data[i].group+'</td>'+
                                        '<td>'+data[i].teacher+'</td>'+
                                        '<td>'+data[i].mobile+'</td>'+
                                        '<td><span>'+state_status[data[i].status]+'</span></td>'+
                                        '<td>'+
                                            '<div class="opera" value="'+data[i].id+'">'+option[data[i].status]+'</div>'+
                                        '</td>'+
                                    '</tr>';
                        }
                        $('#home table tbody').empty();
                        $('#home table tbody').html(html);
                        //分页初始化
                        $('#home .pagination').attr('value',res.data.total);
                        InitPage(res.data.total,$('#home .pagination'));
                    }else{
                        console.log(res.message);
                    }
                }
            })
        }
        //分页
        pagination($('#home'),nomalCourse);

        /*紧急课程*/
        function emergencyCourse(req){
            //状态
            var state_status = {
                '1':'<span>已结束</span>',
                '-1':'<span class="state-before">未开始</span>',
                '0':'<span class="state-ing">进行中</span>'
            };
            //操作
            var option = {
                '1':'<a href="javascript:void(0)"><span class="read  state1">课件</span></a><a href="javascript:void(0)"><span class="read  state1">视频</span></a><a href="javascript:void(0)"><span class="read  state1">报告</span></a><a href="javascript:void(0)"><span class="read  state1 detail">详情</span></a>',
                '0':'<a href="javascript:void(0)"><span class="read  state1">监控</span></a>',
                '-1':'<a href="javascript:void(0)"><span class="read  state1 edit-item">编辑</span></a><a href="javascript:void(0)"><span class="read  state2">取消</span></a>'
            }
            $.ajax({
                type:"get",
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@getProvisionalCoursesPlanData')}}",
                async:true,
                data:JSON.stringify(req),
                success:function(res){
                    if(res.code==1){
                        var data = res.data.rows;
                        var html = '';
                        for(var i in data){
                            html +='<tr>'+
                                        '<td>'+data[i].id+'</td>'+
                                        '<td>'+data[i].courses+'</td>'+
                                        '<td>'+data[i].currentdate+'</td>'+
                                        '<td>'+data[i].begintime+'-'+data[i].endtime+'</td>'+
                                        '<td>'+data[i].classroom+'</td>'+
                                        '<td>'+data[i].group+'</td>'+
                                        '<td>'+data[i].teacher+'</td>'+
                                        '<td>'+data[i].mobile+'</td>'+
                                        '<td><span>'+state_status[data[i].status]+'</span></td>'+
                                        '<td>'+
                                            '<div class="opera" value="'+data[i].id+'">'+option[data[i].status]+'</div>'+
                                        '</td>'+
                                    '</tr>';
                        }
                        $('#profile table tbody').empty();
                        $('#profile table tbody').html(html);
                        //分页初始化
                        $('#profile .pagination').attr('value',res.data.total);
                        InitPage(res.data.total,$('#profile .pagination'));
                    }else{
                        console.log(res.message);
                    }
                }
            })
        }
        //分页
        pagination($('#profile'),emergencyCourse);

        /*岗前培训*/
        function stuffTrain(req){
            //状态
            var state_status = {
                '1':'<span>已结束</span>',
                '-1':'<span class="state-before">未开始</span>',
                '0':'<span class="state-ing">进行中</span>'
            }
            var option = {
                '1':'<a href="javascript:void(0)"><span class="read  state1">记录</span></a><a href="javascript:void(0)"><span class="read  state1 detail">详情</span></a>',
                '0':'<a href="javascript:void(0)"><span class="read  state1">监控</span></a>',
                '-1':'<a href="javascript:void(0)"><span class="read  state1 edit-item">编辑</span></a><a href="javascript:void(0)"><span class="read  state2">取消</span></a>'
            }
            $.ajax({
                type:"get",
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@getTrainingCoursesPlanList')}}",
                async:true,
                data:JSON.stringify(req),
                success:function(res){
                    if(res.code==1){
                        var data = res.data.rows;
                        var html = '';
                        for(var i in data){
                            html +='<tr>'+
                                        '<td>'+data[i].id+'</td>'+
                                        '<td>'+data[i].courses+'</td>'+
                                        '<td>'+data[i].currentdate+'</td>'+
                                        '<td>'+data[i].begintime+'-'+data[i].endtime+'</td>'+
                                        '<td>'+data[i].classroom+'</td>'+
                                        '<td>'+data[i].group+'</td>'+
                                        '<td>'+data[i].teacher+'</td>'+
                                        '<td>'+data[i].mobile+'</td>'+
                                        '<td><span>'+state_status[data[i].status]+'</span></td>'+
                                        '<td>'+
                                            '<div class="opera" value="'+data[i].id+'">'+option[data[i].status]+'</div>'+
                                        '</td>'+
                                    '</tr>';
                        }
                        $('#train table tbody').empty();
                        $('#train table tbody').html(html);
                        //分页初始化
                        $('#train .pagination').attr('value',res.data.total);
                        InitPage(res.data.total,$('#train .pagination'));
                    }else{
                        console.log(res.message);
                    }
                }
            })
        }
        //分页
        pagination($('#train'),stuffTrain);

        /*搜索*/
        $('#search').click(function(){
            var req = {};
            req['bagindate'] = $('#start').val();
            req['enddate'] = $('#start').val();
            req['field'] = 'classroom';//此处关键字先默认为classroom。还可以为course，看后期需求
            req['keyword'] = $('#search-input').val();
            req['page'] = 1;
            var elemSelect = $('#tab-page').find('.active').find('a').attr('href');
            if(req['keyword']==''){
                req['field'] = '';
            }
            switch(elemSelect){
              case '#home':
               nomalCourse(req)
               break;
              case '#profile':
               emergencyCourse(req)
               break;
              default:
               stuffTrain(req);
               break;
            }
        });

        /*右侧列表*/
        function getRight_list(){
            $.ajax({
                type:"get",
                async:true,
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@getWaitExamineProvisionalCourses')}}",
                success:function(res){
                    if(res.code==1){
                        var data = res.data.rows;
                        var html = '';
                        for(var i in data){
                            html += '<div class="timeline-item">'+
                                        '<div class="row">'+
                                            '<div class="col-xs-3 date ui-sortable">'+
                                                '<i class="fa fa-clock-o"></i>'+data[i].apply_time+
                                                '<br>'+
                                            '</div>'+
                                           '<div class="col-xs-7 content ui-sortable">'+
                                                '<div class="item-info">'+
                                                    '<label>课程内容:</label>'+
                                                    '<span>'+data[i].title+'</span>'+
                                                '</div>'+
                                                '<div class="item-info classroom">'+
                                                    '<label>预约教室:</label>'+
                                                    '<span>'+data[i].classroom+'</span>'+
                                                '</div>'+
                                                '<div class="item-info">'+
                                                    '<label>预约时间:</label>'+
                                                    '<span>'+data[i].time+'</span>'+
                                                '</div>'+
                                                '<div class="item-info">'+
                                                    '<label>申请老师:</label>'+
                                                    '<span>'+data[i].applyer+'</span>'+
                                                '</div>'+
                                                '<div class="item-info">'+
                                                    '<label>联系方式:</label>'+
                                                    '<span>'+data[i].moblie+'</span>'+
                                                '</div>'+
                                                '<div class="item-info">'+
                                                    '<label>课程人数:</label>'+
                                                   '<span>'+data[i].total+'人</span>'+
                                                '</div>'+
                                                '<div class="item-info"  value='+data[i].id+'>'+
                                                    '<button class="btn btn-white modal-control" data-toggle="modal" data-target="#myModal" flag="no">拒绝</button>&nbsp;&nbsp;'+
                                                    '<button class="btn btn-primary modal-control" data-toggle="modal" data-target="#myModal" flag="yes">通过</button>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'
                        }
                        $('#timeline').empty();
                        $('#timeline').html(html);
                    }else{
                        console.log(res.message);
                    }
                }
            });
        }
        //间隔时间更新
        getRight_list();
        //setInterval(getRight_list,120000);
        
        /**
         *紧急预约通知
         *通过与否*/
        $('#timeline').on('click','.item-info',function(){
            var thisElement = $(this);
            $.ajax({
                type:"get",
                async:true,
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@getExamineProvisionalCoursesCheck')}}",
                data:{id:thisElement.attr('value')},
                success:function(res){
                    if(res.code==1){
                        if(res.data.result){
                            //有冲突课程
                            $('#Form3').find('.modal-body').attr('flag',2);
                            $('.emergency-1').hide();
                            $('.emergency-2').show();
                            $('#Form3').attr('value',thisElement.attr('value'));
                            //写入数据
                            var meet_time = res.data.rows.currentdate+"  "+res.data.rows.begintime+'-'+res.data.rows.endtime;
                            $('#recommend-classroom').text(thisElement.siblings('.classroom').find('span').text());
                            $('#recommend-time').text(meet_time);
                            $('#meet-info').text(res.data.rows.name +' '+ meet_time);

                            $('#valueName').attr('value',res.data.rows.id);
                            $('#valueName').attr('course_id',res.data.rows.id);
                        }else{
                            $('#Form3').attr('value',thisElement.attr('value'));
                            //$('#rejectValue').attr('value',res.data.rows.id);
                            $('#Form3').find('.modal-body').attr('flag',1);
                            $('.emergency-2').hide();
                            $('.emergency-1').show();
                        }
                    }else{
                        console.log(res.message);
                    }
                }
            });
        })
        

        /*冲突变更*/
        $('#recommend-edit').change(function(){
            if($(this).val()==1){
                $('.change-recommend').show();
                $('.change-edit').hide();
            }else{
                $('.change-recommend').hide();
                $('.change-edit').show();
                //获取教室和时间
                $.ajax({
                    type:"get",
                    async:true,
                    url:"{{route('msc.resourcesManager.classroomList')}}",
                    data:{id:$('#valueName').attr('value')},
                    success:function(res){
                        var data = res.data.rows;
                        if(res.code==1){
                            var html = '';
                            for(var i in data){
                                html += '<option value="'+data[i].id+'">'+data[i].name+'</option>'
                            }
                            $('#classroom-chioce').html(html);
                            //预加载数据
                            $.ajax({
                                type:"get",
                                async:true,
                                url:"{{route('msc.courses.ClassroomTime')}}",
                                data:{id:$('#classroom-chioce').val(),plan_id:$('#valueName').attr('course_id')},
                                success:function(res){
                                    var data = res.data.rows;
                                    if(res.code==1){
                                        var html = '';
                                        for(var i in data){
                                            html += '<option value="'+data[i]+'">'+data[i]+'</option>'
                                        }
                                        $('#classroom-time').html(html);
                                    }else{
                                        console.log(res.message);
                                    }
                                }
                            });
                        }else{
                            console.log(res.message);
                        }
                    }
                });
                //时间选择
                $('#classroom-chioce').change(function(){
                    var ele = $(this);
                    $.ajax({
                        type:"get",
                        async:true,
                        url:"{{route('msc.courses.ClassroomTime')}}",
                        data:{id:$('#classroom-chioce').val(),plan_id:$('#valueName').attr('course_id')},
                        success:function(res){
                            var data = res.data.rows;
                            if(res.code==1){
                                var html = '';
                                for(var i in data){
                                    html += '<option value="'+data[i]+'">'+data[i]+'</option>'
                                }
                                $('#classroom-time').html(html);
                            }else{
                                console.log(res.message);
                            }
                        }
                    });
                });
            }
        });

        /*通过提交*/
        $("#apply-yes").click(function(){
            if($('#Form3').find('.modal-body').attr('flag')==1){
                //通过
                var req = {};
                    req['id'] = $('#Form3').attr('value');
                    console.log('通过',req);
                    $.post("{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@postMarlboroProvisional')}}",req/*JSON.stringify(req)*/,function(res){
                        if(res.code!=1){
                            console.log(res.message);
                        }else{
                            //通过操作
                            location.reload();
                        }
                    });
            }else{
                if($('#recommend-edit').val()==1){
                    //推荐
                    var req = {}; 
                    req['id'] = $('#Form3').attr('value');
                    var time = $('#recommend-time').text();
                    var day = time.split(' ')[0].replace('/','-').replace('/','-');
                    var timeNow = time.split('  ')[1];
                    req['start'] = day +' '+ timeNow.split('-')[0];
                    req['end'] = day +' '+ timeNow.split('-')[1];
                    console.log('推荐',req);
                    $.ajax({
                        type:"post",
                        async:true,
                        url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@postChangeProvisional')}}",
                        data:req,//JSON.stringify(req),
                        success:function(res){
                            if(res.code!=1){
                                console.log(res.message);
                            }else{
                                //通过操作
                                location.reload();
                            }
                        }
                    });
                }else{
                    //变更
                    var req = {};
                    req['id'] = $('#valueName').attr('value');
                    req['apply_id'] = $('#Form3').attr('value');
                    req['classroom'] = $('#classroom-chioce').val();
                    var time = $('#classroom-time').val();
                    var day = time.split(' ')[0];
                    var timeNow = time.split(' ')[1];
                    req['start'] = day +' '+ timeNow.split('-')[0];
                    req['end'] = day +' '+ timeNow.split('-')[1];
                    console.log('变更',req);
                    $.post("{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@postChangeOldPlan')}}",req/*JSON.stringify(req)*/,function(res){
                        if(res.code!=1){
                            console.log(res.message);
                        }else{
                            //通过操作
                            location.reload();
                        }
                    });
                }
            }
        });

        /*拒绝请求*/
        $('#apply-no').click(function(){
            $.ajax({
                type:"psot",
                async:true,
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@postRefuseProvisionalApply')}}",
                data:{reject:$('#comment').val(),id:$('#Form3').attr('value')},
                success:function(res){
                    if(res.code!=1){
                        console.log(res.message);
                    }else{
                        location.reload();
                    }
                }
            });
        });

        /*文件上传*/
        /*图片上传*/
        $("#file-not-local").change(function(){
            $.ajaxFileUpload
            ({
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@postImportCourses')}}",
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'json',//
                success: function (data, status)
                {
                    if(data.code == 1){
                        //$('.add_img').before('<li><img src="'+(url+data.data.path)+'" width="100%"><i class="fa fa-remove font14 del_img"></i><input type="hidden" name="images_path[]" value="'+data.data.path+'"><>');
                    }
                },
                error: function (data, status, e)
                {
                    //console.log(data);
                }
            });
        }) ;

        $("#file-local").change(function(){
            $.ajaxFileUpload
            ({
                url:"{{action('\Modules\Msc\Http\Controllers\Admin\CoursesController@postImportCoursesPlan')}}",
                secureuri:false,//
                fileElementId:'file1',//必须要是 input file标签 ID
                dataType: 'json',//
                success: function (data, status)
                {
                    if(data.code == 1){
                        //$('.add_img').before('<li><img src="'+(url+data.data.path)+'" width="100%"><i class="fa fa-remove font14 del_img"></i><input type="hidden" name="images_path[]" value="'+data.data.path+'"><>');
                    }
                },
                error: function (data, status, e)
                {
                    //console.log(data);
                }
            });
        }) ;

        /*升序降序 课程*/
        $('.order-course li').click(function(){
            var thisElement = $(this);
            var req = {};
            if(thisElement.attr('value')==1){
                req['orderby'] = 'asc';
            }else{
                req['orderby'] = 'desc';
            }
            req['order'] = 'classroom';
            req['bagindate'] = $('#start').val();
            req['enddate'] = $('#start').val();
            req['field'] = 'classroom';//此处关键字先默认为classroom。还可以为course，看后期需求
            req['keyword'] = $('#search-input').val();
            req['page'] = 1;
            var elemSelect = $('#tab-page').find('.active').find('a').attr('href');
            if(req['keyword']==''){
                req['field'] = '';
            }
            switch(elemSelect){
              case '#home':
               nomalCourse(req)
               break;
              case '#profile':
               emergencyCourse(req)
               break;
              default:
               stuffTrain(req);
               break;
            }
        });

        /*升序降序 教室*/
        $('.order-classroom li').click(function(){
            var thisElement = $(this);
            var req = {};
            if(thisElement.attr('value')==1){
                req['orderby'] = 'asc';
            }else{
                req['orderby'] = 'desc';
            }
            req['order'] = 'classroom';
            req['bagindate'] = $('#start').val();
            req['enddate'] = $('#start').val();
            req['field'] = 'classroom';//此处关键字先默认为classroom。还可以为course，看后期需求
            req['keyword'] = $('#search-input').val();
            req['page'] = 1;
            var elemSelect = $('#tab-page').find('.active').find('a').attr('href');
            if(req['keyword']==''){
                req['field'] = '';
            }
            switch(elemSelect){
              case '#home':
               nomalCourse(req)
               break;
              case '#profile':
               emergencyCourse(req)
               break;
              default:
               stuffTrain(req);
               break;
            }
        });


    })
    </script>
    

@stop