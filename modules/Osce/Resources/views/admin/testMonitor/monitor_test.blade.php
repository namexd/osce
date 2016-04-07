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
    <script src="{{asset('osce/admin/js/webVideoCtrl.js')}}"></script>
    <script src="{{asset('osce/admin/testMonitor/test_monitor.js')}}"></script>
@stop

{{-- 内容主体区域 --}}
@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'monitor_test'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试监控</h5>
            </div>
        </div>
        @if(!empty($data['examName']['name']))
        <div class="container-fluid ibox-content">
            <ul class="sortable-list connectList agile-list ui-sortable" style="background-color: #fff;">
                <li class="success-element titleBackground">
                    <p class="font20 fontb">{{@$data['examName']['name']}}</p>
                    <div class="font16 messageColor">
                        <span class="marr_25">考站数量：{{@$data['stationCount']}}</span>
                        <span class="marr_25">考生人数：{{@$data['studentCount']}}</span>
                        <span class="marr_25">正在考试：{{@$data['doExamCount']}}</span>
                        <span>已完成：{{@$data['endExamCount']}}</span>
                    </div>
                </li>
            </ul>
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route("osce.admin.ExamControlController.getExamlist")}}">正在考试</a></li>
                        <li><a href="{{route("osce.admin.ExamMonitorController.getExamMonitorLateList")}}">迟到</a></li>
                        <li><a href="{{route("osce.admin.ExamMonitorController.getExamMonitorReplaceList")}}">替考</a></li>
                        <li><a href="{{route("osce.admin.ExamMonitorController.getExamMonitorQuitList")}}">弃考</a></li>
                        <li><a href="{{route("osce.admin.ExamMonitorController.getExamMonitorFinishList")}}">已完成</a></li>
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
                        @if(!empty($data['examInfo'])&& count($data['examInfo'])>0)
                            @foreach(@$data['examInfo'] as $key=>$val )
                                <tr>
                                    <td>{{ @$key+1}}</td>
                                    <td class="student">{{ @$val["name"]}}</td>
                                    <td>{{ @$val["code"]}}</td>
                                    <td>{{ @$val["exam_sequence"]}}</td>
                                    <td class="idCard">{{ @$val["idcard"]}}</td>
                                    <td class="station">{{ @$val["stationName"]}}</td>
                                    <td>{{ @$val["remainStationCount"]}}</td>
                                    <td>
                                        @if((@$val["is_replace"]==-1&&@$val["is_give"]==1) || (@$val["is_replace"]==1&&@$val["is_give"]==1))上报弃考
                                        @elseif(@$val["is_replace"]==1&&@$val["is_give"]==-1)上报替考
                                        @elseif(@$val["is_replace"]==-1&&@$val["is_give"]==-1&&@$val["examOrderStatus"]!=4)考试中
                                        @endif
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)">
                                            <span class="state1 look">
                                                <i class="fa fa-video-camera fa-2x"></i>
                                            </span>
                                        </a>
                                        @if(@$val["is_replace"]==-1&&@$val["is_give"]==-1&&@$val["examOrderStatus"]!=4)
                                            <a href="javascript:void(0)" stationType="{{ @$val['stationType'] }}">
                                                <span class="state1 stop" data-toggle="modal" data-target="#myModal">
                                                    <i class="fa fa-cog fa-2x"></i>
                                                </span>
                                            </a>
                                        @elseif((@$val["is_replace"]==-1&&@$val["is_give"]==1) || (@$val["is_replace"]==1&&@$val["is_give"]==1))
                                            <a href="javascript:void(0)">
                                                <span class="state1 abandon">
                                                    <i class="fa fa-cog fa-2x"></i>
                                                </span>
                                            </a>
                                        @elseif(@$val["is_replace"]==1&&@$val["is_give"]==-1)
                                            <a href="javascript:void(0)">
                                                <span class="state1 replace">
                                                    <i class="fa fa-cog fa-2x"></i>
                                                </span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="alert alert-warning">
                        当前没有正在进行的考试！
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop

@section('layer_content')
    {{--终止考试弹出框--}}
    <form class="form-horizontal" id="stopForm" novalidate="novalidate" method="post" action="{{ route('osce.admin.ExamControlController.postStopExam') }}">
        <input type="hidden" value="" id="stationType">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">终止考试</h4>
        </div>
        <div class="modal-body">
            <div class="form-group text-center font20">
                当前考生
                <span class="stuName state"></span>
                正在
                <span class="stationName state"></span>
                考站考试中
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