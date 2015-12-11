@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style type="text/css">
    	.has-error .form-control{border-color: #ed5565!important;}
    	.code_add,.code_del{position:absolute;right:15px;top:0;}
    	.add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
    <script>
        // 取消增加
        $(function () {
            $('.cancel').click(function (){
                //history.go(-1);
                var url = '{{ url("/msc/admin/resources-manager/resources-list") }}';
                window.location.href = url;
            });
        });
    </script>
    <script>
		$(function() {
			$(".img_box").delegate(".del_img","click",function(){
				$(this).parent("li").remove();
			});
			
			$("#select_Category").change(function(){
				var id=$(this).val();
				$("#select_Category").siblings().remove();
				if(id==-1){
				}else{
					$.ajax('/msc/admin/resources-manager/ajax-resources-tools-cate',{
			            type: 'get',
			            data: {id:id},
			            success:function(data) {
			            	if(data.length>0){
				            	var str='<select id="CategoryId" class="form-control" name="account1" multiple="">';
				            	for(var i=0;i<data.length;i++){
				            		str+='<option value="'+data[i]['id']+'">'+data[i]['name']+'</option>';
				            	}
				            	str+='</select>';
				            	$("#select_Category").after(str);
			            	}
			            },
			            error:function() {
			              $.alert({
			                  title: '提示：',
			                  content: '通讯失败!',
			                  confirmButton: '确定',
			                  confirm: function(){
			                  }
			              });
			            },
			            dataType: "json"
			        });
				}
			})
			
			
		    /*{}{
		     * 下面是进行插件初始化
		     * 你只需传入相应的键值对
		     * */
		    $('#sourceForm').bootstrapValidator({
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
	                        }
	                    }
	                },
	                manager_name: {
	                	validators: {
		                	notEmpty: {/*非空提示*/
	                            message: '用户名不能为空'
	                        },
	                        stringLength: {
	                            min:2,
	                            message: '用户名长度必须大于2'
	                        }
	                    }
	                },
	                manager_mobile: {
	                	validators: {
		                	notEmpty: {/*非空提示*/
	                            message: '手机号码不能为空'
	                        },
	                        stringLength: {
		                        min: 11,
		                        max: 11,
		                        message: '请输入11位手机号码'
		                    },
	                        regexp: {
		                        regexp: /^1[3|5|8]{1}[0-9]{9}$/,
		                        message: '请输入正确的手机号码'
		                    }
                       }
	                },
	                location: {
	                	validators: {
		                	notEmpty: {/*非空提示*/
	                            message: '地址不能为空'
	                        }
                       }
	                }             
	            }
			});
			
			$(".images_upload").change(function(){
		        $.ajaxFileUpload
		        ({
		            url:'{{ url('commom/upload-image') }}',
		            secureuri:false,//
		            fileElementId:'file0',//必须要是 input file标签 ID
		            dataType: 'json',//
		            success: function (data, status)
		            {
		                if(data.code){
		                	var href=data.data.path;
		                	$('.images_upload').before('<li><img src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
		                }
		            },
		            error: function (data, status, e)
		            {
		                $.alert({
		                  	title: '提示：',
		                  	content: '通讯失败!',
		                  	confirmButton: '确定',
		                  	confirm: function(){
	                  		}
		              	});
		            }
		        });
		    }) ;
		    $(".code_add").click(function(){
		    	var  str='<div class="form-group">'+
                                '<label class="col-sm-2 control-label"></label>'+
                                '<div class="col-sm-10 add_box">'+
                                    '<input type="text" id="" name="code[]" class="code_txt left form-control" value="">'+
                                    '<input type="button" id="" name="" class="code_del left btn btn-danger" value="删 除" />'+
                                '</div>'+
                            '</div>';
		    	$("#code_list").append(str);
		    });
		    $("#code_list").delegate(".code_del","click",function(){
		    	$(this).parents(".form-group").remove();
		    	$(this).remove();
		    });
		    $(".select_code").delegate("select","change",function(){
		    	var id=$(this).val();
		    	//console.log(id);
		    	$("#cate_id").val(id);
		    });
	    });
	    //建立一個可存取到該file的url
	    var url='';
	    function getObjectURL(file) {
	        if (window.createObjectURL!=undefined) { // basic
	            url = window.createObjectURL(file) ;
	        } else if (window.URL!=undefined) { // mozilla(firefox)
	            url = window.URL.createObjectURL(file) ;
	        } else if (window.webkitURL!=undefined) { // webkit or chrome
	            url = window.webkitURL.createObjectURL(file) ;
	        }
	        return url;
	    }
    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>资源详情</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                	<form method="post" class="form-horizontal" id="sourceForm" action="{{url('/msc/admin/resources-manager/add-resources')}}">
                		<input type="hidden" name="resources_type" id="resources_type" value="TOOLS" />
	                    <div class="col-md-5">
	                    	<ul class="img_box">
	                    		<span class="images_upload">
	                        		<input type="file" name="images" id="file0"/>
	                        	</span>
	                    	</ul>
	                    </div>
	                    <div class="col-md-7 ">
	                        <div class="hr-line-dashed"></div>
	                        <div class="form-group">
	                            <label class="col-sm-2 control-label">名称</label>
	                            <div class="col-sm-10">
	                                <input type="text" class="form-control" name="name" id="name" />
	                            </div>
	                        </div>
	                        <div class="hr-line-dashed"></div>
	                        <div class="form-group">
	                        	<input type="hidden" name="cate_id" id="cate_id" value="-1" />
	                            <label class="col-sm-2 control-label">类别</label>
	                            <div class="col-sm-10 select_code">
	                                <select id="select_Category"   class="form-control m-b" name="account">
	                                    <option value="-1">请选择类别</option>
	                                    @foreach ($resourcesCateList as $item)
	                                    <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
	                                    @endforeach
	                                </select>
	                            </div>
	                        </div>
	                        <div class="hr-line-dashed"></div>
	                        <div class="form-group">
	                            <label class="col-sm-2 control-label">负责人</label>
	                            <div class="col-sm-10">
	                                <input type="text"  id="manager_name" name="manager_name" class="form-control">
	                            </div>
	                        </div>
	                        <div class="hr-line-dashed"></div>
	                        <div class="form-group">
	                            <label class="col-sm-2 control-label" >负责人电话</label>
	                            <div class="col-sm-10">
	                                <input type="text" id="manager_mobile" name="manager_mobile"  class="form-control">
	                            </div>
	                        </div>
	                        <div class="hr-line-dashed"></div>
	                        <div class="form-group">
	                            <label class="col-sm-2 control-label">功能描述</label>
	
	                            <div class="col-sm-10">
	                                <input type="text"  id="detail" name="detail" class="form-control">
	                            </div>
	                        </div>
	                        <div class="hr-line-dashed"></div>
	                        <div class="form-group">
	                            <label class="col-sm-2 control-label">地址</label>
	                            <div class="col-sm-10">
	                                <input type="text" id="location" name="location" class="form-control">
	                            </div>
	                        </div>
	                        <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">编码</label>
                                <div class="col-sm-10 add_box">
                                	<input  type="text" id="" name="code[]" class=" left form-control" value="">
                            		<input type="button" id="" name="" class="code_add left btn btn-info" value="添 加">
                                </div>
                            </div>
                            <div id="code_list">
                            	
                            </div>
	                        <div class="hr-line-dashed"></div>
	                        <div class="form-group">
	                            <div class="col-sm-4 col-sm-offset-2">
	                                <button class="btn btn-white cancel" type="button">取消</button>
	                                <button class="btn btn-primary" type="submit">保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button>
	                            </div>
	                        </div>
	                    </div>
	                </form>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}