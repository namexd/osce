@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        #myModalLabel{color: #16BEB0;}
        /*强调颜色*/
        .state{color: #ed5565;}
        /*标题展示区域*/
        .success-element:hover {cursor: default!important;}
        .titleBackground{background-color: #E9EDEF!important;}
        .messageColor{color: #999A9E}
    </style>
@stop

@section('only_js')
    <input type="hidden" id="parameter" value="{'pagename':'monitor_test'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试监控</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <ul class="sortable-list connectList agile-list ui-sortable" style="background-color: #fff;">
                <li class="success-element titleBackground">
                    <p class="font20 fontb">考试2016.1.12（二选一）</p>
                    <div class="font16 messageColor">
                        <span class="marr_25">考站数量：1</span>
                        <span class="marr_25">考生人数：1</span>
                        <span class="marr_25">正在考试：1</span>
                        <span>已完成：1</span>
                    </div>
                </li>
            </ul>
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="">正在考试</a></li>
                        <li><a href="">迟到</a></li>
                        <li><a href="">替考</a></li>
                        <li><a href="">弃考</a></li>
                        <li><a href="">已完成</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <div class="list_all">
                    <table class="table table-striped" id="table-striped" style="background:#fff">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>考生姓名</th>
                            <th>学号</th>
                            <th>准考证号</th>
                            <th>身份证号</th>
                            <th>当前考站</th>
                            <th>剩余考站数</th>
                            <th>状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="subjectBody">
                            <tr>
                                <td>1</td>
                                <td>张三</td>
                                <td>SF1986</td>
                                <td>SF1986</td>
                                <td>510821199008300065</td>
                                <td>操作技能A</td>
                                <td>1</td>
                                <td>考试中</td>
                                <td>
                                    <a href="javascript:void(0)">
                                        <span class="state1 look">
                                            <i class="fa fa-video-camera fa-2x"></i>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0)">
                                        <span class="state1 stop" data-toggle="modal" data-target="#myModal">
                                            <i class="fa fa-cog fa-2x"></i>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0)">
                                        <span class="state1 abandon">
                                            <i class="fa fa-cog fa-2x"></i>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0)">
                                        <span class="state1 replace">
                                            <i class="fa fa-cog fa-2x"></i>
                                        </span>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')
    {{--终止考试弹出框--}}
    <form class="form-horizontal" id="stopForm" novalidate="novalidate" method="post" action="">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">终止考试</h4>
        </div>
        <div class="modal-body">
            <div class="form-group text-center font20">
                当前考生<span class="stuName state">张三</span>正在<span class="stationName state">XXX</span>考站考试中
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">请选择终止原因：</label>
                <div class="col-sm-9">
                    <select name="reason" id="reason" class="form-control">
                        <option value="1">放弃考试</option>
                        <option value="2">作弊</option>
                        <option value="3">替考</option>
                        <option value="4">其他原因</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id='stopSure'>确定</button>
            <button type="button" class="btn btn-white" data-dismiss="modal" aria-hidden="true">取消</button>
        </div>
    </form>
@stop