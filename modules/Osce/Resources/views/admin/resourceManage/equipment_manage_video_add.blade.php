@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
<script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'equipment_manage_video_add','name': '{{route("osce.admin.machine.postNameUnique")}}','code': '{{route("osce.admin.machine.postNameUnique")}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增摄像机</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.machine.postAddMachine')}}">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备名称</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="name" name="name">
                                    <input type="hidden"  class="form-control" id="cate_id" name="cate_id" value="1" />
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
                                    <input type="text"  class="laydate-icon" id="purchase_dt" name="purchase_dt" readonly="readonly" placeholder="YYYY-MM-DD">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10">
                                    <select id=""   class="form-control m-b" name="status">
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
                                <label class="col-sm-2 control-label">IP地址</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="ip" name="ip">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">端口</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="port" name="port">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">实时端口</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="realport" name="realport">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">通道号</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="channel" name="channel" placeholder="摄像头编号，如摄像头通道号为D01，则值为'1'">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">功能描述</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="description" class="form-control" name="description">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">账号</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="username" name="username">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">密码</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="password" name="password">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary save" type="submit">保存</button>
                                    <a class="btn btn-white" href="{{route("osce.admin.machine.getMachineList")}}">取消</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop{{-- 内容主体区域 --}}