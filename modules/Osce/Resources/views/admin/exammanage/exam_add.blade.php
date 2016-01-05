@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
span.laydate-icon{
    border: 0;
    background-position: right;
    background-image: none;
    padding-right: 27px;
    display: inline-block;
    width: 151px;
    line-height: 30px;
}
</style>
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
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.machine.postAddMachine')}}">

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="name">
                                <input type="hidden" required class="form-control" id="cate_id" name="cate_id" value="2" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试地点</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="code" name="code">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试时间</label>
                            <div class="col-sm-10">
                                <form class="container-fluid ibox-content" id="list_form">
                                    <a  href="javascript:void(0)"  class="btn btn-outline btn-default" id="add-new" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
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
                                        <tbody index="0">
                                            <tr>
                                                <td>1</td>
                                                <td class="laydate">
                                                    <span class="laydate-icon end">2015-11-12 09:00</span>
                                                </td>
                                                <td class="laydate">
                                                    <span class="laydate-icon end">2015-11-12 09:00</span>
                                                </td>
                                                <td>3:00</td>
                                                <td>
                                                    <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="btn-group pull-right">
                                       
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">创建考试</button>
                                <button class="btn btn-white" type="submit">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>

                            </div>
                        </div>


                    </form>

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