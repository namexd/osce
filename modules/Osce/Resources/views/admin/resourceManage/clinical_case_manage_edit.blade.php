@extends('osce::admin.layouts.admin_index')
@section('only_css')
    
@stop

@section('only_js')
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'clinical_case_manage_edit','name':'{{route('osce.admin.case.postNameUnique')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>病例编辑</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">病例名称</label>
                            <div class="col-sm-10">
                                <input type="text"  class="form-control" id="name" value="{{$data->name}}" name="name" maxlength="32">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">病例描述</label>
                            <div class="col-sm-10">
                                <textarea ng-model="location" id="location" class="form-control" name="description">{{$data->description}}</textarea>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="{{route("osce.admin.case.getCaseList")}}">取消</a>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}