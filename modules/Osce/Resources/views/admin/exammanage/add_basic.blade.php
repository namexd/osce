@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .blank-panel .panel-heading {margin-left: -20px;}
    input.laydate-icon{
        border: 0;
        background-position: right;
        background-image: none;
        padding-right: 27px;
        display: inline-block;
        width: 151px;
        line-height: 30px;
    }
    .form-group {
        margin: 15px;
        height: 30px;
        line-height: 30px;
    }
    </style>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'add_basic','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                
            </div>
        </div>
    <div class="container-fluid ibox-content">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('osce.admin.exam.getEditExam')}}?id={{$id}}">基础信息</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamroomAssignment',['id'=>$id])}}">考场安排</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                    </ul>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="row">
                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postEditExam')}}">
                            <input type="hidden" name="exam_id" value="{{$id}}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试名称</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name" name="name" value="{{$examData['name']}}">
                                    <input type="hidden" required class="form-control" id="cate_id" name="cate_id" value="2" />
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试地点</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="code" name="code" value="{{$examData['name']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试顺序</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="code" name="sequence_cate" value="{{$examData['sequence_cate']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">排序方式</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="code" name="sequence_mode" value="{{$examData['sequence_mode']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试时间</label>
                                <div class="col-sm-10">
                                    <a  href="javascript:void(0)"  class="btn btn-outline btn-default" id="add-new" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
                                        <table class="table table-bordered" id="add-basic">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>开始时间</th>
                                                <th>结束时间</th>
                                                <th>时长</th>
                                                <th>操作</th>

                                            </tr>
                                            </thead>
                                            <tbody index="{{count($examScreeningData)}}">
                                            @forelse($examScreeningData as $key => $item)
                                                <tr>
                                                    <td>{{$key+1}}</td>
                                                    <td class="laydate">
                                                        <input type="hidden" name="time[{{$key}}][id]" value="{{$item->id}}">
                                                        <input type="hidden" name="time[{{$key}}][exam_id]" value="{{$id}}">
                                                        <input type="text" class="laydate-icon end" name="time[{{$key}}][begin_dt]" class="laydate-icon end" value="{{date('Y-m-d H:i',strtotime($item->begin_dt))}}">
                                                    </td>
                                                    <td class="laydate">
                                                        <input type="text" class="laydate-icon end" name="time[{{$key}}][end_dt]" class="laydate-icon end" value="{{date('Y-m-d H:i',strtotime($item->end_dt))}}">
                                                    </td>
                                                    <td>3:00</td>
                                                    <td>
                                                        <a href="javascript:void(0)"><span class="read  state1"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                    </td>
                                                </tr>
                                            @empty
                                            @endforelse
                                            </tbody>
                                        </table>

                                        <div class="btn-group pull-right">
                                           
                                        </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="javascript:history.back(-1)">取消</a>

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
<script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>

@stop