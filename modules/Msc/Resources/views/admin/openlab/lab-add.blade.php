@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/demo/webuploader-demo.css')}}">
    <link href="{{asset('msc/admin/plugins/css/plugins/ionRangeSlider/ion.rangeSlider.css')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/plugins/css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css')}}" rel="stylesheet">
    <style>
        .layer-date{max-width: 100%!important;}
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/ionRangeSlider/ion.rangeSlider.min.js')}}"></script>
    <script src="{{asset('msc/admin/openlab/openlab.js')}}"></script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <input type="hidden"  id="parameter" value="{'pagename':'lab-add','ajaxurl':'{{ route("msc.admin.resourcesManager.getResourcesList") }}'}" />
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{$title or "新增"}}实验室</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" class="form-horizontal" id="labForm" action="{{route('msc.admin.lab.postHadOpenLabToAdd')}}">

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" value="{{@$openLabDetail->name}}"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <input type="hidden" name="opened" id="cate_id" value="-1" />
                                <label class="col-sm-2 control-label">类别</label>
                                <div class="col-sm-10 select_code">
                                    <select id="select_Category"   class="form-control m-b" name="opened">
                                        <option value="-1">请选择类别</option>
                                        <option value="0" @if(@$openLabDetail['name'] == 0)selected="selected"@endif>普通实验室</option>
                                        <option value="1" @if(@$openLabDetail['name'] == 1)selected="selected"@endif>开发实验室(只能预约实验室)</option>
                                        <option value="2" @if(@$openLabDetail['name'] == 2)selected="selected"@endif>开发实验室(只能预约设备)</option>
                                        {{--@foreach ($resourcesCateList as $item)
                                            <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                                        @endforeach--}}
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">负责人</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="manager_name" name="manager_name" class="form-control" value="{{@$openLabDetail->manager_name}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >负责人电话</label>
                                <div class="col-sm-10">
                                    <input type="text" id="manager_mobile" name="manager_mobile"  class="form-control" value="{{@$openLabDetail->manager_mobile}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10">
                                    <select id="select_Category"   class="form-control m-b" name="status">
                                        <option value="-1">请选择状态</option>
                                        <option value="0" @if(@$openLabDetail['status'] == 0)selected="selected"@endif>不允许预约使用</option>
                                        <option value="1" @if(@$openLabDetail['status'] == 1)selected="selected"@endif>正常</option>
                                        <option value="2" @if(@$openLabDetail['status'] == 2)selected="selected"@endif>已预约</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="address" name="location" class="form-control" value="{{@$openLabDetail->location}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">门牌号</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="code" name="code" class="form-control" value="{{@$openLabDetail->code}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">开放时间段</label>

                                <div class="col-sm-10">

                                    <input type="text" id="ionrange_4" name="example_name" value="" />
                                    <input type="hidden" id="start" name="begintime"  value="{{@$openLabDetail->begintime}}">
                                    <input type="hidden" id="end" name="endtime"  value="{{@$openLabDetail->endtime}}">
                                </div>

                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">说明(功能描述)</label>

                                <div class="col-sm-10">
                                    <input type="text" name="detail" id="detail" class="form-control"  value="{{@$openLabDetail->detail}}">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">最大预约人数</label>
                                <div class="col-sm-10">
                                    <input type="number" name="person_total" id="maxorder" class="form-control"  value="{{@$openLabDetail->person_total}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div id="code_list">
                                <input type="hidden" name="id" value="{{@$openLabDetail->id}}">
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white cancel" type="button" href="javascript:history.back(-1)">取 消</a>
                                    <input class="btn btn-primary" type="submit" value="保存">
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    </div>
@stop{{-- 内容主体区域 --}}