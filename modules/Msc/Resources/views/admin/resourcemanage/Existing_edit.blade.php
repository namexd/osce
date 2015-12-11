@extends('msc::admin.layouts.admin')
@section('only_css')
 	<link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <style type="text/css">
    	.has-error .form-control{border-color: #ed5565!important;}
    	.has-error .form-control{border-color: #ed5565!important;}
    	.code_add,.code_del{position:absolute;right:15px;top:0;}
    	.add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
    </style>
@stop
@section('only_js')
<!-- Sweet alert -->
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
            $(".img_box").delegate(".del_img","click",function(){
            	$(this).parent("li").remove();
            })
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
			$(".select_code").delegate("select","change",function(){
		    	var id=$(this).val();
		    	//console.log(id);
		    	$("#cate_id").val(id);
		    });
		    
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
	                            message: '设备名不能为空'
	                        }
	                    }
	                },
	                manager_name: {
	                	validators: {
		                	notEmpty: {/*非空提示*/
	                            message: '负责人不能为空'
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
	                locationName: {
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
		    });
		    
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
        });
    </script>
@stop
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/sweetalert/sweetalert.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/resourcemanage/css/managedetail.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/css/fileinput.min.css')}}">
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>资源详情</h5>
            </div>
            <div class="ibox-content" >
            	
            	<form method="post" class="form-horizontal" id="sourceForm" action="{{ url('/msc/admin/resources-manager/edit-resources') }}">
            		<input type="hidden" name="resources_type" id="resources_type" value="TOOLS" />
	                <div class="row">
		                <div class="col-md-5">
		                	<ul class="img_box">
		                		@if(count($resource['image'])>0)
			                		@foreach($resource['image'] as $img)
			                			<li>
			                				<img name="images[]" src="{{ asset($img['url']) }}">
			                				<input type="hidden" name="images_path[]" value="{{ $img['url'] }}">
		                					<i class="fa fa-remove font16 del_img"></i>
			                			</li>
			                		@endforeach
		                		@endif
		                		<span class="images_upload">
		                    		<input type="file" name="images" id="file0"/>
		                    	</span>
		                	</ul>
		                </div>
	                    <div class="col-md-7 ">
                            <div class="hr-line-dashed"></div>
                            <input type="hidden" name="id" value="{{ $resource['id'] }}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="name" name="name" value="{{ $resource['name'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                            	<input type="hidden" name="cate_id" id="cate_id" value="{{$resource['cate_id']}}" />
                                <label class="col-sm-2 control-label">类别</label>
                                <div class="col-sm-10 select_code">
                                    <select id="select_Category"   class="form-control m-b" name="">
                                    	<option value="-1">请选择类别</option>
                                    	@foreach($cateTree as $cate)
											@if($resource['cate_pid']==null)
												<option  @if($cate['id'] == $resource['cate_id']) selected="selected" @endif  value="{{$cate['id']}}">{{$cate['name']}}</option>
											@else
												<option  @if($cate['id'] == $resource['cate_pid']) selected="selected" @endif  value="{{$cate['id']}}">{{$cate['name']}}</option>
											@endif
										@endforeach
                                    </select>
                                    @if($resource['cate_pid']!=null)
	                                    <select id="CategoryId" class="form-control" name="" multiple="">
	                                    	@foreach($cateTree as $cate)
	                                    		@if($cate['id']==$resource['cate_pid'])
	                                    			@foreach($cate['sub'] as $cate_list)
	                                    				<option  @if($cate_list['id'] == $resource['cate_id']) selected="selected" @endif  value="{{$cate_list['id']}}">{{$cate_list['name']}}</option>
	                                    			@endforeach
	                                    		@endif
											@endforeach
	                                	</select>
                                	@endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">负责人</label>
                                <div class="col-sm-10">
                                    <input type="text" id="manager_name" name="manager_name" class="form-control" value="{{ $resource['manager_name'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label" >负责人电话</label>
                                <div class="col-sm-10">
                                    <input type="text" id="manager_mobile" name="manager_mobile" class="form-control" value="{{ $resource['manager_mobile'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">功能描述</label>
                                <div class="col-sm-10">
                                    <input type="text" id="detail" name="detail" class="form-control" value="{{ $resource['detail'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>
                                <div class="col-sm-10">
                                    <input type="text" id="location" name="location" class="form-control" value="{{ $resource['locationName'] }}">
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
	                </div>
                </form>
            </div>
        </div>

    </div>
@stop{{-- 内容主体区域 --}}