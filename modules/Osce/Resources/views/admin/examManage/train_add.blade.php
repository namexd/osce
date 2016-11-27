@extends('osce::admin.layouts.admin_index')
@section('only_css')
	<link rel="stylesheet" href="{{asset('osce/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('osce/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style>
	    .col-sm-1{margin-top: 6px;}
	    .col-sm-1>input[type="checkbox"]{vertical-align: sub;}
	    .form-group.col-sm-1{margin-bottom: 0!important;}
	    .upload{
	        display:block;
	        height: 34px!important;
	        width: 100px!important;
	        cursor: pointer;
	        background-image:none!important;
	        position:relative;
	        margin:0!important;
	    }
	    #file0{position:absolute;top:0;left:0;width:100px;height:34px;opacity:0;cursor:pointer;}
	    .upload_list{line-height:1em;color:#4f9fcf;}
	    .fa-remove{cursor:pointer;}
	    .laydate-icon{width:200px;}
		.file-msg{
			position: relative;
			top: -26px;
			left: 109px;
			color: #42b2b1;
		}
    </style>
@stop


@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor1.config.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.all.min.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/UEditor/lang/zh-cn/zh-cn.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
<script src="{{asset('osce/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
<script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>

@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'train_add','URL':'{{url('/osce/admin/train/upload-file')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title" style="position: relative;">
            <h5>新增考前培训</h5>
            <a href="javascript:history.back(-1)" class="btn btn-default" style="position: absolute;right:10px;top:4px;">&nbsp;返回&nbsp;</a>
        </div>
        <div class="ibox-content">
            <form method="post"  id="form1" class="form-horizontal" action="{{route('osce.admin.postAddTrain')}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训名称:</label>
                        <div class="col-sm-10">
                            <input type="text"  id="" name="name" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训地点:</label>
                        <div class="col-sm-10">
                            <input type="text"  id="" name="address" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">开始时间:</label>
                        <div class="col-sm-10">
                        	<input class="laydate-icon" type="text" name="begin_dt" id="start" readonly="readonly" placeholder="YYYY/MM/DD hh:mm">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">结束时间:</label>
                        <div class="col-sm-10">
                        	<input class="laydate-icon" type="text" name="end_dt" id="end" readonly="readonly" placeholder="YYYY/MM/DD hh:mm">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训讲师:</label>
                        <div class="col-sm-10">
                            <input type="text"  id="" name="teacher" class="form-control"/>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" >内容:</label>
                        <div class="col-sm-10">
                            <script id="editor" type="text/plain" style="width:100%;height:500px;" name="content"></script>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">附件:</label>
                        <div class="col-sm-10">
                        	<span class="upload btn btn-white">
                        		上传附件
								<input type="file" name="file" id="file0"/>
							</span>
							<span class="file-msg">(上传文件类型为docx, xlsx，文件大小不得超过2M!)</span>
							<div class="upload_list upload_list_doc">
								<p>
									<input type="hidden" name="file" id="" value="" />
								</p>
							</div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <input class="btn btn-primary fabu_btn" type="submit" value="发布">
                            <a class="btn btn-white cancel" href="{{route("osce.admin.getTrainList")}}">取消</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}