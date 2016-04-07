@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
    <style>
    /*选择定宽*/
    .exam-item,
    .exam-station,
    .station-type,
    .station-belong,
    .station-chioce {width: 181px!important;}
    </style>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'station_assignment','stationAdd':'{{route('osce.admin.ExamArrange.postAddExamFlow')}}','exam_item':'{{route('osce.admin.exam-arrange.getAllSubjects')}}','station_stage':'{{route('osce.admin.exam-arrange.getAllGradations')}}','station_list':'{{route('osce.admin.ExamArrange.getStationList')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考场安排</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                
            </div>
        </div>
    <div class="container-fluid ibox-content">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="{{route('osce.admin.exam.getEditExam',['id'=>$id])}}">基础信息</a></li>
                        <li class="active"><a href="{{route('osce.admin.exam.getChooseExamArrange',['id'=>$id])}}">考场安排</a></li>
                        <li class=""><a href="{{route('osce.admin.exam-arrange.getInvigilateArrange',['id'=>$id])}}">考官安排</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamRemind',['id'=>$id])}}">待考区说明</a></li>
                    </ul>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="row">
                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postStationAssignment')}}">
                            <input type="hidden" name="id" value="{{$id}}">

                            <div class="station-container" index="0">
                            
                                <!-- 一个考站dom -->
                                <!-- <div class="form-group">
                                    <label class="col-sm-2 control-label">&nbsp;</label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-sm-4"><label class="control-label">考站1</label></div>
                                            <div class="col-sm-6">
                                                    <label class="control-label col-sm-2">阶段：</label>
                                                    <select class="form-control col-sm-10" style="width: 381px;"></select>
                                            </div>
                                            <div class="col-sm-2">
                                                <a class="btn btn-primary" href="javascript:void(0)">必考</a>
                                                <a  href="javascript:void(0)" class="btn btn-primary" id="del-station" style="float: right;">删除</a>
                                            </div>
                                        </div>
                                        <table class="table table-bordered" id="examroom">
                                            <thead>
                                                <tr>
                                                    <td>考试项目</td>
                                                    <td>考站</td>
                                                    <td>类型</td>
                                                    <td>考官</td>
                                                    <td>sp</td>
                                                    <td>操作</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>3</td>
                                                    <td>4</td>
                                                    <td>5</td>
                                                    <td>6</td>
                                                    <td>8</td>
                                                    <td>

                                                        <a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>
                                                        <a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> -->
                            </div>

                            <!-- 新增考站 -->
                            <div class="form-group">
                                <div class="col-sm-2 col-sm-offset-4">
                                    <button id="save" class="btn btn-primary" type="submit">保存考场安排</button>
                                </div>
                                <div class="col-sm-2">
                                    <a class="btn btn-white" href="javascript:history.back(-1)">取消</a>
                                </div>
                                <div class="col-sm-2">
                                    <a class="btn btn-primary" href="javascript:void(0)" id="station-add">新增考站</a>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
<script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>

<script>
    $(function(){
        @if(isset($_GET['succ']) && $_GET['succ'] ==1)
            layer.msg('保存成功！',{skin:'msg-success',icon:1});
        @endif
    })
</script>
@stop