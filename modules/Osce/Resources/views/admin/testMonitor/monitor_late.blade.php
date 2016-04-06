@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        /*标题展示区域*/
        .success-element:hover {cursor: default!important;}
        .titleBackground{background-color: #E9EDEF!important;}
        .messageColor{color: #999A9E}
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/testMonitor/test_monitor.js')}}"></script>
@stop

{{-- 内容主体区域 --}}
@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'monitor_late'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试监控</h5>
            </div>
        </div>
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
                        <li><a href="{{route("osce.admin.ExamControlController.getExamlist")}}">正在考试</a></li>
                        <li class="active"><a href="{{route("osce.admin.ExamMonitorController.getExamMonitorLateList")}}">迟到</a></li>
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
                                <th>联系电话</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody class="subjectBody">
                            @if(!empty($data)&& count($data)>0)
                                @foreach(@$data as $key=>$val )
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td class="student">{{$val['name']}}</td>
                                        <td>{{$val['code']}}</td>
                                        <td>{{$val['exam_sequence']}}</td>
                                        <td class="idCard">{{$val['idcard']}}</td>
                                        <td>{{$val['mobile']}}</td>
                                        <td>
                                            <a href="javascript:void(0)">
                                                <span class="state1 abandon">
                                                    <i class="fa fa-cog fa-2x"></i>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

