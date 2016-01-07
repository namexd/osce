@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
    .col-sm-1{margin-top: 6px;}
    .col-sm-1>input[type="checkbox"]{vertical-align: sub;}
    </style>
@stop

@section('only_js')

@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增通知</h5>
            </div>
            <div class="ibox-content">
                <form method="post" class="form-horizontal">
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">考试:</label>
                            <div class="col-sm-10">
                                <select id="select_Category"   class="form-control m-b" name="sex">
                                    <option value="">男</option>
                                    <option value="">女</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">接收人:</label>
                            <div class="col-sm-10 select_code">
                                <div class="form-group col-sm-1">
                                    <input type="checkbox">
                                    <label>考生</label>
                                </div>
                                <div class="form-group col-sm-1">
                                    <input type="checkbox">
                                    <label>老师</label>
                                </div>
                                <div class="form-group col-sm-1">
                                    <input type="checkbox">
                                    <label>sp老师</label>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">标题:</label>
                            <div class="col-sm-10">
                                <input type="text"  id="examinee_id" name="examinee_id" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" >内容:</label>
                            <div class="col-sm-10">
                                <input type="text" id="id_number" name="id_number"  class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">附件:</label>

                            <div class="col-sm-10">
                                <input type="text"  id="tell" name="tell" class="form-control">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-white cancel" type="button">取消</button>
                                <button class="btn btn-primary" type="submit">保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}