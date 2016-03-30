@extends('osce::admin.layouts.admin_index')
@section('only_css')
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
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop


@section('content')

<input type="hidden" id="parameter" value="{'pagename':'staff_manage_invigilator_patrol_edit','code':'{{route('osce.admin.invigilator.postCodeUnique')}}','mobile':'{{route('osce.admin.invigilator.postSelectTeacher')}}','idcard':'{{route('osce.admin.invigilator.postIdcardUnique')}}','url':'{{ url('commom/upload-image') }}'}" />

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增巡考</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.invigilator.postAddSpInvigilator')}}">

                    <div class="col-md-3 col-sm-3 image-box">
                        <ul class="img_box">
                            <span class="images_upload" id="images_upload">
                                <input type="file" name="images" id="file0"/>
                               选择图片
                            </span>
                        </ul>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">姓名</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="name">
                                <input type="hidden" required class="form-control" id="type" name="type" value="2">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">性别</label>
                            <div class="col-sm-10">
                                <select name="gender" id="gender" class="form-control">
                                    <option value="1">男</option>
                                    <option value="2">女</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">教师编号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="code" id="code">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">身份证号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="idcard" id="idcard">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系电话</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="mobile" id="mobile">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">电子邮箱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="email" id="email">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="description" id="note">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit" id="save">保存</button>
                                <a class="btn btn-white" href="javascript:history.back(-1)">取消</a>
{{--								<a class="btn btn-white" href="{{route('osce.admin.invigilator.getSpInvigilatorList')}}">取消</a>--}}
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}