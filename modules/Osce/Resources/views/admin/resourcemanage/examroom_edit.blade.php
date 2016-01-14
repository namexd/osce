@extends('osce::admin.layouts.admin_index')
@section('only_css')
    
@stop

@section('only_js')
    <script src="{{asset('osce/plugins/js/plugins/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('osce/plugins/js/plugins/messages_zh.min.js')}}"></script>
    
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>考场信息编辑</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">场所名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" value="{{$data->name}}" name="name">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="description" id="description" class="form-control" name="description" value="{{$data->description}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
                                {{--<button class="btn btn-white" type="submit">取消</button>--}}

                            </div>
                        </div>


                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}