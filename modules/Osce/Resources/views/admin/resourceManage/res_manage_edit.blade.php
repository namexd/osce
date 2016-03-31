@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'res_manage_edit'}" />
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>修改用物</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">用物名称</label>
                            <div class="col-sm-10">
                                <input type="hidden" required class="form-control" id="name" name="name" value="{{$data->name}}">
                                <input type="text" required class="form-control" id="name" name="name" value="{{$data->name}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="{{route("osce.admin.supplies.getList")}}">取消</a>
                                {{--<a href="{{route('osce.admin.case.getCaseList')}}" class="btn btn-white">取消</a>--}}
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}