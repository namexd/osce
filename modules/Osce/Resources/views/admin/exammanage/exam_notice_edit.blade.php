@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
    .col-sm-1{margin-top: 6px;}
    .col-sm-1>input[type="checkbox"]{vertical-align: sub;}
    .form-group.col-sm-1{margin-bottom: 0!important;}
    .images_upload {
        display: inline-block;
        height: 34px!important;
        width: 70px!important;
        border: 1px dashed #ccc;
        cursor: pointer;
        background-image:none!important;
        /*background: url(../images/add_img.png) no-repeat center; */
    }
    input#file0 {
        position: relative;
        top: -5px;
        left: -5px;
        height: 34px;
        width: 70px;
        opacity: 0;
    }
    </style>
@stop

@section('only_js')
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.config.js')}}"></script>
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.all.min.js')}}"></script>
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/lang/zh-cn/zh-cn.js')}}"></script>
 <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
 <script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_notice_edit'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增通知</h5>
        </div>
        <div class="ibox-content">
            <form method="post" class="form-horizontal" action="">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">考试:</label>
                        <div class="col-sm-10">
                            <select id="select_Category"   class="form-control" name="">
                                <option>2016OSCE考试第一期</option>
                                <option>2016OSCE考试第一期</option>
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">接收人:</label>
                        <div class="col-sm-10 select_code">
                            <div class="form-group col-sm-1">
                                <input type="checkbox" name="accept[]" checked="checked">
                                <label>考生</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input type="checkbox" name="accept[]">
                                <label>老师</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input type="checkbox" name="accept[]">
                                <label>sp老师</label>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">标题:</label>
                        <div class="col-sm-10">
                            <input type="text"  id="examinee_id" name="name" value="{{$item->name}}" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" >内容:</label>
                        <div class="col-sm-10">
                            <script id="editor" type="text/plain" style="width:100%;height:500px;"></script>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">附件:</label>

                        <div class="col-sm-10">
                            <span class="images_upload">
                                <input type="file" name="images" id="file0"/>
                            </span>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit">保存</button>
                            <a class="btn btn-white cancel" href="javascript:history.back(-1)">取消</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}