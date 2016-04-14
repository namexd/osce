@extends('osce::admin.layouts.admin_index')
@section('only_css')
<link href="{{asset('osce/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
<style>
    .img_box{
        width:197px;
        height:251px;
        margin: auto;
    }
    .img_box li img{
        width: 197px;
        height: 251px;
    }
    .select2-container--default{width:100% !important;}
                 .select2-container--default .select2-selection--multiple{border:1px solid #e5e6e7;}
                 .select2-container--default.select2-container--focus .select2-selection--multiple {border:1px solid  #1ab394 !important;outline: 0;}
                 .select2-container--open .select2-selection--single {background-color: #fff;border: 1px solid #1ab394 !important;border-radius: 4px;}
                 .select2-container--open .select2-dropdown {border: 1px solid #1ab394 !important;}
                 .select2-container--open .select2-search--dropdown .select2-search__field {border: 1px solid #1ab394 !important;}
</style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'staff_manage_invigilator_edit','code':'{{route('osce.admin.invigilator.postCodeUnique')}}','mobile':'{{route('osce.admin.invigilator.postSelectTeacher')}}','idcard':'{{route('osce.admin.invigilator.postIdcardUnique')}}','url':'{{ url('commom/upload-image') }}','email':'{{ route('osce.admin.invigilator.postEmailUnique')}}','get_subject':'{{route('osce.admin.invigilator.getSubjects')}}'}" />
@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>编辑考官</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.invigilator.postEditInvigilator')}}">
                        <div class="col-md-3 col-sm-3">
                            <ul class="img_box">
                                <li>
                                    <img src="{{$item->userInfo->avatar}}"/>
                                    <input type="hidden" value="{{$item->userInfo->avatar}}" name="images_path[]">
                                    <i class="fa fa-remove font16 del_img"></i>
                                </li>
                                <span class="images_upload1" id="images_upload">
                                    <input type="file" name="images" id="file0"/>选择图片
                                </span>
                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-9">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name" name="name" value="{{$item->name}}">
                                    <input type="hidden" required class="form-control" id="id" name="id" value="{{$item->id}}">
                                    <input type="hidden" required class="form-control" id="is_sp" name="is_sp" value="2">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">性别</label>
                                <div class="col-sm-10">
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="1" {{$item->userInfo->gender=='男'? 'selected="selected"':''}}>男</option>
                                        <option value="2" {{$item->userInfo->gender=='女'? 'selected="selected"':''}}>女</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">教师编号</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="code" id="code" value="{{$item->code}}" maxlength="20">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">身份证号</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="idcard" id="idcard" value="{{$item->userInfo->idcard}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">联系电话</label>
                                <div class="col-sm-10">
                                    <input type="text" ng-model="mobile" id="mobile" class="form-control" name="mobile" value="{{$item->userInfo->mobile}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">电子邮箱</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="email" id="email" value="{{$item->userInfo->email}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">支持考试项目</label>
                                <div class="col-sm-10">
                                    <select class="form-control data-example-ajax"  name="subject[]"  multiple="multiple">
                                        @foreach($subjects as $subject)
                                            <option selected="selected" value="{{$subject->subject_id}}">{{$subject->subject_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">备注</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="description" id="note" value="{{$item->description}}" maxlength="100">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="{{route("osce.admin.invigilator.getInvigilatorList")}}">取消</a>
                                    {{--<button class="btn btn-white" type="submit">取消</button>--}}
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

@stop{{-- 内容主体区域 --}}