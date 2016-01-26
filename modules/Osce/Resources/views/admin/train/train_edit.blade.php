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
    </style>
@stop


@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.config.js')}}"></script>
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
			format: 'YYYY/MM/DD hh:mm:ss',
			min: laydate.now(),
		    max: '2099-06-16 23:59:59',
		    istime: true,
		    choose: function(datas){
		        end.min = datas;
		        end.start = datas
	    	}
		}
		var end={
			elem: '#end',
		   	event: 'click',
			format: 'YYYY/MM/DD hh:mm:ss',
			min: laydate.now(),
		    max: '2099-06-16 23:59:59',
		    istime: true,
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
                    if(data.state=='SUCCESS'){
                        str='<p><input type="hidden" name="file[]" id="" value="'+data.url+'" />'+data.title+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                        var ln=$(".upload_list").children("p").length;
                        if(ln<=1){
                            $(".upload_list").append(str);
                        }else{
                        	layer.alert('最多上传2个文件！',function(index1){layer.close(index1);});
                        }
                    }
                },
	            error: function (data, status, e)
	            {
	                layer.alert('上传失败！',function(index2){layer.close(index2);});
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
        <div class="ibox-title">
            <h5>编辑考前培训</h5>
        </div>
        <div class="ibox-content">
            <form method="post" id="form1" class="form-horizontal" action="{{route('osce.admin.postEditTrain')}}">
            		<input type="hidden" name="id" value="{{$data['id'] }}" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训名称:</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{ $data['name']  }}" id="" name="name" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训地点:</label>
                        <div class="col-sm-10">
                            <input type="text" value="{{ $data['address']  }}"  id="" name="address" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">开始时间:</label>
                        <div class="col-sm-10">
                        	<input class="laydate-icon" value="{{ $data['begin_dt']  }}" type="text" name="begin_dt" id="start" readonly="readonly" placeholder="YYYY/MM/DD hh:mm:ss">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">结束时间:</label>
                        <div class="col-sm-10">
                        	<input class="laydate-icon" value="{{ $data['end_dt']  }}" type="text" name="end_dt" id="end" readonly="readonly" placeholder="YYYY/MM/DD hh:mm:ss">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训讲师:</label>
                        <div class="col-sm-10">
                            <input type="text"  value="{{ $data['teacher']  }}" id="" name="teacher" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" >内容:</label>
                        <div class="col-sm-10">
                            <script id="editor"  type="text/plain" style="width:100%;height:500px;" name="content">{!! $data['content']  !!}  </script>
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
							@if($data['attachments'])
                                <div class="upload_list">
                                    @foreach($data['attachments'] as $data)
                                        <p>
                                            <input type="hidden" name="file[]" id="" value="{{ $data }}" />
                                                <i class="fa fa-2x fa-delicious"></i>&nbsp;{{ substr ($data,27)  }}&nbsp;<i class="fa fa-2x fa-remove clo6"></i>
                                        </p>
                                    @endforeach
                                </div>
                            @else
                                <p>
                                    <input type="hidden" name="file[]" id="" value="" />
                                    <i class="fa fa-2x fa-delicious"></i>&nbsp;&nbsp;<i class="fa fa-2x fa-remove clo6"></i>
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <input class="btn btn-primary fabu_btn" type="submit" value="发布">
                            <a class="btn btn-white cancel" href="javascript:history.back(-1)">取消</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}