@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_check_tag'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">新增试卷</h5>
            </div>
        </div>
        <div class="panel blank-panel">
            <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.machine.postAddMachine')}}">

                <div class="form-group">
                    <label class="col-sm-2 control-label">设备名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name">
                        <input type="hidden"  class="form-control" id="cate_id" name="cate_id" value="2" />
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">设备ID</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="code" name="code">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">厂家</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="factory" name="factory">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">型号</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="sp" name="sp">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">采购日期</label>
                    <div class="col-sm-10">
                        <input type="text" class="laydate-icon" id="purchase_dt" name="purchase_dt" readonly="readonly">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-10">
                        <select id="status"   class="form-control m-b" name="status">
                            <option value="0">正常</option>
                            @foreach($status as $key => $value)
                                @if($key >1)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <a class="btn btn-white" href="{{route("osce.admin.machine.getMachineList",["cate_id"=>2])}}">取消</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}