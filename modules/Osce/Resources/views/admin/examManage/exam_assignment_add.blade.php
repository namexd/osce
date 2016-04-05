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
	.checkbox_input{font-weight:100;cursor:pointer;}
	.check_name{padding:0;height:16px;position: relative;top:-3px;font-weight: 700;}
	.check_icon.check {background-position: -32px 0;}
</style>
@stop


@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_assignment_add','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'}" />
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
                                    <option value="2">顺序</option>
                                    <option value="3">轮循</option>
                                    <option value="1">随机</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">排考方式</label>
                            <div class="col-sm-10">
                                <select class="form-control" style="width:200px;" name="sequence_mode" >
                                    <option value="1">以考场分组</option>
                                    <option value="2">以考站分组</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">考官配置</label>
                            <div class="col-sm-10">
                                 <select class="form-control" style="width:200px;" name="" >
                                       <option value="1">按考站配置考官</option>
                                       <option value="2">按考场配置考官</option>
                                 </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="row">
                            	<div class="col-md-12">
                            		<div class="clearfix form-group" style="margin-bottom: 0;">
                                        <div class="col-sm-12" id="checkbox_div">
                                            <label class="check_label checkbox_input checkbox_one" style="height: 34px;line-height: 28px;margin-left: 12.1%;">
                                                 <div class="check_icon" style="display: inline-block;margin:5px 0 0 5px;float:left;" disabled></div>
                                                 <input type="checkbox" name="same_time" value="1" disabled>
                                                 <span class="check_name" style="display: inline-block;float:left;">要求考生同时进出考站（考站的时间采用最长考站时间）</span>
                                            </label>
                                        </div>
                                    </div>
                            	</div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                             <label class="col-sm-2 control-label">考试内容</label>
                             <div class="col-sm-10">
                                 <select class="form-control" style="width:200px;" name="" >
                                      <option value="1">由考官指定</option>
                                      <option value="2">由系统指定</option>
                                 </select>
                             </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                             <div class="row">
                                 <div class="col-md-12">
                                      <div class="clearfix form-group" style="margin-bottom: 0;">
                                           <div class="col-sm-12" id="checkbox_div">
                                                <label class="check_label checkbox_input col-sm-2 control-label checkbox_two" style="height: 34px;line-height: 28px;">
                                                    <div class="check_icon" style="display: inline-block;float:right;margin:5px 0 0 5px;"></div>
                                                    <input type="checkbox" name="gradation" value="1">
                                                    <span class="check_name" style="display: inline-block;float:right;">考生分阶段考试</span>
                                                </label>
                                                <div class="col-sm-10">
                                                    <input type="text" required class="form-control" id="gradation" name="gradation" style="float:left;width:200px;">
                                                    <span style="float:left;margin-left:5px;margin-top: 5px;">阶段</span>
                                                </div>
                                           </div>
                                      </div>
                                 </div>
                             </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                             <label class="col-sm-2 control-label">实时发布成绩</label>
                             <div class="col-sm-10">
                                 <select class="form-control" style="width:200px;" name="real_push" >
                                      <option value="1">是</option>
                                      <option value="0">否</option>
                                 </select>
                             </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试时间</label>
                            <div class="col-sm-10">
                                    <a  href="javascript:void(0)"  class="btn btn-primary" id="quick-add" style="float: right;display:none;">快速新增时间</a>
                                    <a  href="javascript:void(0)"  class="btn btn-primary" id="add-new" style="float: right;">新增时间</a>
                                    <table class="table table-bordered" id="exam_add">
                                        <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>开始时间</th>
                                            <th>结束时间</th>
                                            <th>时长</th>
                                            <th>阶段</th>
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
                                <button class="btn btn-primary" id="save" type="submit">保存</button>
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


