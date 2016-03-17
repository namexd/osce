@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<style>
input.laydate-icon{
    border: 0;
    background-position: right;
    background-image: none;
    padding-right: 27px;
    display: inline-block;
    width: 171px;
    line-height: 30px;
}
.time-modify{
    margin-top: 25px!important;
    margin-bottom: 30px!important;
}
</style>
@stop


@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_add','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增考试</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postAddExam')}}">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试名称</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="code" name="name">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试地点</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="address" name="address">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试顺序</label>
                            <div class="col-sm-10">
                                <select class="form-control" style="width:200px;" name="sequence_cate" >
                                    <option value="1">随机</option>
                                    <option value="3">轮循</option>
                                    <option value="2">顺序</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">排序方式</label>
                            <div class="col-sm-10">
                                <select class="form-control" style="width:200px;" name="sequence_mode" >
                                    <option value="1">以考场分组</option>
                                    <option value="2">以考站分组</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试时间</label>
                            <div class="col-sm-10">
                                    <a  href="javascript:void(0)"  class="btn btn-primary" id="add-new" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
                                    <table class="table table-bordered" id="exam_add">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>开始时间</th>
                                            <th>结束时间</th>
                                            <th>时长</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody index="0" id="add-exam">
                                        </tbody>
                                    </table>

                                    <div class="btn-group pull-right">

                                    </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2 time-modify">
                                <button class="btn btn-primary" id="save" type="submit">创建考试</button>
                                <a class="btn btn-white" href="{{route("osce.admin.exam.getExamList")}}">取消</a>

                            </div>
                        </div>


                    </form>

                </div>

            </div>
        </div>
    </div>

</div>
@stop{{-- 内容主体区域 --}}
