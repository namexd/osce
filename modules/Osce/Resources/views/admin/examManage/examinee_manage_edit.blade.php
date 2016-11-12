@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <link rel="stylesheet" href="{{asset('osce/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('osce/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style type="text/css">
        .has-error .form-control {
            border-color: #ed5565 !important;
        }
        .code_add, .code_del {
            position: absolute;
            right: 15px;
            top: 0;
        }
        .add_box .glyphicon-remove, .add_box .glyphicon-ok {
            display: none !important;
        }
        .img_box{
            width:197px;
            height:251px;
            margin: auto;
        }
        .img_box li img{
            width: 197px;
            height: 251px;
        }
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/admin/examManage/exam_manage.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter"
           value="{'pagename':'examinee_manage_edit','preUrl':'{{route('osce.admin.exam.getExamineeManage',['id'=>$item->id])}}','code':'{{route("osce.admin.exam.postExamSequenceUnique")}}','idcard':'{{route("osce.admin.exam.postExamSequenceUnique")}}','exam_sequence':'{{route("osce.admin.exam.postExamSequenceUnique")}}','mobile':'{{route("osce.admin.exam.postExamSequenceUnique")}}','header':'{{ url('commom/upload-image') }}','id':'{{$item->id}}','exam_id':'{{$item->exam_id}}'}"/>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>考生编辑</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postEditExaminee')}}">
                        <input type="hidden" name="exam_id" value="{{$item->exam_id}}"/>
                        <input type="hidden" name="id" value="{{$item->id}}"/>
                        <input type="hidden" name="resources_type" id="resources_type" value="TOOLS"/>

                        <div class="col-md-3 col-sm-3">
                            <ul class="img_box">
                                <li>
                                    <img src="{{$item->avator}}"/>
                                    <input type="hidden" value="{{$item->avator}}" name="images_path[]">
                                    <i class="fa fa-remove font16 del_img"></i>
                                </li>
                               <span class="images_upload1" id="images_upload"><input type="file" name="images" id="file0"/>选择图片</span>
                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-9">
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" value="{{$item->name}}"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <input type="hidden" name="" id="cate_id" value="-1"/>
                                <label class="col-sm-2 control-label">性别:</label>
                                <div class="col-sm-10 select_code">
                                    <select id="gender" class="form-control m-b" name="gender">
                                        <option value="1" {{$item->userInfo->gender=='男'? 'selected="selected"':''}}>男
                                        </option>
                                        <option value="2" {{$item->userInfo->gender=='女'? 'selected="selected"':''}}>女
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">学号:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="code" name="code" class="form-control"
                                           value="{{$item->code}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">身份证号:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="idcard" name="idcard" class="form-control"
                                           value="{{$item->idcard}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">准考证号:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="exam_sequence" name="exam_sequence" class="form-control"
                                           value="{{$item->exam_sequence}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">班级:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="exam_sequence" name="grade_class" class="form-control"
                                           value="{{$item->grade_class}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">班主任姓名:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="exam_sequence" name="teacher_name" class="form-control"
                                           value="{{$item->teacher_name}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">联系电话:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="mobile" name="mobile" class="form-control"
                                           value="{{$item->mobile}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">电子邮箱:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="email" name="email" class="form-control"
                                           value="{{$item->userInfo->email}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">备注:</label>
                                <div class="col-sm-10">
                                    <textarea name="description" id="" cols="" rows=""
                                              class="form-control">{{$item->description}}</textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="javascript:history.go(-1)">取消</a>
                                    {{--<button class="btn btn-white return-pre" type="button">取消</button>--}}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}