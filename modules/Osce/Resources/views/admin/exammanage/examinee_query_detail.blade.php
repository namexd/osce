@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <link rel="stylesheet" href="{{asset('osce/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('osce/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style type="text/css">
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
        .form-control[disabled] {background-color: #fff;}
    </style>
@stop

@section('only_js')
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>考生详情</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <form method="post" class="form-horizontal" id="sourceForm" >
                        <input type="hidden" name="resources_type" id="resources_type" value="TOOLS" />
                        <div class="col-md-3 col-sm-3 image-box">
                            <ul class="img_box">
                                <li>
                                    <img src="{{$item->avator}}"/>
                                    <input type="hidden" value="{{$item->avator}}" name="images_path[]">
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-9">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名:</label>
                                <div class="col-sm-10">
                                    <input disabled type="text" class="form-control" name="name" value="{{$item->name}}" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <input type="hidden" name="" id="cate_id" value="-1" />
                                <label class="col-sm-2 control-label">性别:</label>
                                <div class="col-sm-10 select_code">
                                    <select disabled id="gender"   class="form-control m-b" name="gender">
                                        <option value="1">{{$item->userInfo->gender}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">学号:</label>
                                <div class="col-sm-10">
                                    <input disabled type="text"  id="code" name="code" class="form-control" value="{{$item->code}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >身份证号:</label>
                                <div class="col-sm-10">
                                    <input  disabled type="text" id="idcard" name="idcard"  class="form-control" value="{{$item->idcard}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >准考证号:</label>
                                <div class="col-sm-10">
                                    <input disabled type="text" id="exam_sequence" name="exam_sequence"  class="form-control" value="{{$item->exam_sequence}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">联系电话:</label>

                                <div class="col-sm-10">
                                    <input disabled type="text"  id="mobile" name="mobile" class="form-control" value="{{$item->mobile}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">电子邮箱:</label>
                                <div class="col-sm-10">
                                    <input disabled type="text" id="email" name="email" class="form-control" value="{{$item->userInfo->email}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">备注:</label>
                                <div class="col-sm-10">
                                    <textarea disabled name="description" id="description" cols="" rows="" class="form-control">{{$item->description}}</textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2 time-modify">
                                    <a class="btn btn-outline btn-default" href="javascript:history.back(-1)">返回</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}