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
	    .upload_list{padding-top:10px;line-height:1em;color:#4f9fcf;}
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
<script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>
<script src="{{asset('osce/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
<script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
 <script type="text/javascript">
 	$(function(){
 		var start={
			elem: '#start',
		   	event: 'click',
			format: 'YYYY/MM/DD hh:mm',
			min: laydate.now(),
		    max: '2099-06-16 23:59',
		    istime: true,
		    istoday:false,
		    choose: function(datas){
		        end.min = datas;
	    	}
		}
		var end={
			elem: '#end',
		   	event: 'click',
			format: 'YYYY/MM/DD hh:mm',
			min: laydate.now(),
		    max: '2099-06-16 23:59',
		    istime: true,
		    istoday:false,
		    choose: function(datas){
		        start.max = datas;
		    }
		}
		laydate.skin('molv');
		laydate(start);
		laydate(end);
		
 		$('#form1').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                name: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '用户名不能为空'
                        },
                        stringLength: {
                            max:64,
                            message: '用户名必须少于64字符'
                        }
                    }
                },
                address: {
                	validators: {
	                	notEmpty: {/*非空提示*/
                            message: '地址不能为空'
                        },
                        stringLength: {
                            max:64,
                            message: '地址长度必须少于64字符'
                        }
                    }
                },
                teacher: {
                	validators: {
	                	notEmpty: {/*非空提示*/
                            message: '讲师不能为空'
                        }
                   }
                },
                content: {
                	validators: {
	                	notEmpty: {/*非空提示*/
                            message: '内容不能为空'
                        }
                   }
                }
            }
		});
 		$(".upload").change(function(){
	        $.ajaxFileUpload
	        ({
	            url:'{{url('/osce/admin/train/upload-file')}}',
	            secureuri:false,//
	            fileElementId:'file0',//必须要是 input file标签 ID
	            dataType: 'json',//
	            success: function (data, status)
	            {
					if(data.code!=1){
						layer.msg('只能上传后缀为".xlsx"或".docx"的文件！',{skin:'msg-error',icon:1});
					}else{
						var val=data.url;
						var point = val.lastIndexOf(".");
						var type = val.substr(point);
						var str='<p><input type="hidden" name="file[]" id="" value="'+data.url+'" /><i class="fa fa-2x fa-delicious"></i>&nbsp;'+data.title+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
						$(".upload_list_doc").append(str);
					}
	                /*if(data.state=='SUCCESS'){
	                	var val=data.url;
	                	var point = val.lastIndexOf(".");
     					var type = val.substr(point);
     					console.log(type);
	                	if(type===".xlsx"|type===".doc"|type===".docx"){
	                		var str='<p><input type="hidden" name="file[]" id="" value="'+data.url+'" /><i class="fa fa-2x fa-delicious"></i>&nbsp;'+data.title+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                			$(".upload_list_doc").append(str);
	                	}else{
	                		layer.msg('只能上传后缀为".xlsx"或".docx"的文件！',{skin:'msg-error',icon:1});
	                	}
	                }*/
	            },
	            error: function (data, status, e)
	            {
	                layer.msg('上传失败！',{skin:'msg-error',icon:1});
	            }
	        });
	    }) ;
	    $(".upload_list").on("click",".fa-remove",function(){
	    	$(this).parent("p").remove();
	    });
	    $(".fabu_btn").click(function(){
	    	var start=$("#start").val();
	    	var end=$("#end").val();
	    	if(start==""){
	    		layer.alert('你还没有选择开始时间!',function(its){layer.close(its)});
              	return false;
	    	}
	    	if(end==""){
	    		layer.alert('你还没有选择结束时间!',function(its){layer.close(its)});
              	return false;
	    	}
	    })

 	})
 </script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_notice_add'}" />
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
							<span class="file-msg">(上传文件类型为docx, xlsx)</span>
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