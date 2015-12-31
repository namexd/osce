@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/plugins/js/plugins/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('osce/plugins/js/plugins/messages_zh.min.js')}}"></script>
    <script>

    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增考站</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="www.sogou.com">

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站名称</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站类型</label>
                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="">
                                        <option value="">请选择</option>
                                        <option value="">技能操作</option>
                                        <option value="">标准化病人(sp)</option>
                                        <option>理论考试</option>
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">时间限制</label>

                                <div class="col-sm-10">
                                    <input type="text"  required  ng-model="num" id="code" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">关联摄像机</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="">
                                        <option value="">请选择</option>
                                        <option value="">摄像机A</option>
                                        <option value="">摄像机B</option>
                                        <option>摄像机C</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label" required>所属考场</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="">
                                        <option value="">请选择</option>
                                        <option value="">技能中心413</option>
                                        <option value="">技能中心415</option>
                                        <option>技能中心417</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">病例</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="">
                                        <option value="">请选择</option>
                                        <option value="">病例1</option>
                                        <option value="">病例2</option>
                                        <option>病例3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">评分标准</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="">
                                        <option value="">请选择</option>
                                        <option value="">1</option>
                                        <option value="">22</option>
                                        <option>3</option>
                                    </select>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <input type="button" class="btn btn-white" value="取消" style="margin-right: 20px;">
                                    <input type="submit" class="btn btn-primary" value="保存">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop{{-- 内容主体区域 --}}