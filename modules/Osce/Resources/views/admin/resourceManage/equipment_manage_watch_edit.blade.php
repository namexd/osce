@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'equipment_manage_watch_edit','name': '{{route("osce.admin.machine.postNameUnique")}}','code': '{{route("osce.admin.machine.postNameUnique")}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>编辑腕表</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.machine.postEditMachine')}}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备名称</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name" value="{{$item['name']}}">
                                    <input type="hidden"  class="form-control" id="cate_id" name="cate_id" value="3" />
                                    <input type="hidden"  class="form-control" id="id" name="id" value="{{$item['id']}}" />
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备ID</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="code" name="code" value="{{$item['code']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">感应ID</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nfc_code" name="nfc_code" value="{{$item['nfc_code']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">厂家</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="factory" name="factory" value="{{$item['factory']}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">型号</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="sp" name="sp" value="{{$item['sp']}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">采购日期</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="laydate-icon" id="purchase_dt" name="purchase_dt" readonly="readonly" value="{{date('Y-m-d',strtotime($item['purchase_dt']))}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10">
                                    <select id="status"   class="form-control m-b" name="status">
                                        @if($item['status'] >1)
                                            <option value="0">正常</option>
                                        @endif
                                        @foreach($status as $key => $value)
                                            @if($key >1)
                                                <option value="{{$key}}" {{($item['status']==$key)?'selected="selected"':''}}>{{$value}}</option>
                                            @elseif($item['status']==$key)
                                                <option value="{{$item['status']}}" {{($item['status']==$key)?'selected="selected"':''}}>正常</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">描述</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="description" name="description" value="{{$item['description']}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="{{route("osce.admin.machine.getMachineList",["cate_id"=>3])}}">取消</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop{{-- 内容主体区域 --}}