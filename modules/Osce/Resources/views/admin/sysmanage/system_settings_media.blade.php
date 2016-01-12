@extends('osce::admin.layouts.admin_index')

@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<style type="text/css">
	.ibox-title h5{margin-top:10px;}
	.control-label{text-align: right;height:34px;line-height:34px;font-weight: 100;}
	.form-control-feedback{right:15px;}
</style>
@stop

@section('only_js')
	<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script type="text/javascript">
    	$(function(){
   			/**
		     * 下面是进行插件初始化
		     * 你只需传入相应的键值对
		     * */
		    $('#list_form').bootstrapValidator({
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
	                            message: '不能为空'
	                        }
	                    }
	                    
	                },
	                country1: {
		                validators: {
		                    notEmpty: {
		                        message: '请选择协议'
		                    }
		                }
		            },
		            country2: {
		                validators: {
		                    notEmpty: {
		                        message: '请选择协议'
		                    }
		                }
		            },
		            country3: {
		                validators: {
		                    notEmpty: {
		                        message: '请选择协议'
		                    }
		                }
		            }
	            }
	        });
       	});
    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">系统设置</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
        </div>
    </div>
    <form class="container-fluid ibox-content" action="" id="list_form">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#">媒体设置</a></li>
                        <li class=""><a href="#">场所类型</a></li>
                        <a href="" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
                    </ul>
                </div>
                <div class="ibox float-e-margins">
			        <div class="ibox-title" style="border:none;">
			            <h5>媒体分享</h5>
			        </div>
			        <div class="ibox-content">
			            <div class="row">
			                <div class="col-md-12 ">
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">分享媒体1：</label>
		                            <div class="col-sm-4">
		                            	<input class="form-control" type="text" name="name" id="" value="" />
		                            </div>
		                        </div>
			                </div>
			            </div>
			        </div>
			        <div class="ibox-title" style="border:none;">
			            <h5>短信网关</h5>
			        </div>
			        <div class="ibox-content">
			            <div class="row">
			                <div class="col-md-12 ">
			                	<div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">短信网关协议：</label>
		                            <div class="col-sm-4">
		                            	<select class="form-control" name="country1">
		                            		<option value="">--请选择--</option>
		                            		<option value="1">1</option>
		                            		<option value="2">2</option>
		                            		<option value="3">3</option>
		                            		<option value="4">4</option>
	                            		</select>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">短信网关服务器IP：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="name" name="name">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">设置1：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="name" name="name">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">设置2：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="name" name="name">
		                            </div>
		                        </div>
			                </div>
			            </div>
			        </div>
			        <div class="ibox-title" style="border:none;">
			            <h5>邮件服务器</h5>
			        </div>
			        <div class="ibox-content">
			            <div class="row">
			                <div class="col-md-12 ">
			                	<div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">接收邮件服务器协议：</label>
		                            <div class="col-sm-4">
		                            	<select class="form-control" name="country2">
		                            		<option value="">--请选择--</option>
		                            		<option value="1">POP3</option>
		                            		<option value="2">2</option>
		                            		<option value="3">3</option>
		                            		<option value="4">4</option>
	                            		</select>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">邮件接收服务器地址：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="name" name="name">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">邮件发送服务器协议：</label>
		                            <div class="col-sm-4">
		                            	<select class="form-control" name="country3">
		                            		<option value="">--请选择--</option>
		                            		<option value="1">SMTP</option>
		                            		<option value="2">2</option>
		                            		<option value="3">3</option>
		                            		<option value="4">4</option>
	                            		</select>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">邮件发送服务器地址：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="name" name="name">
		                            </div>
		                        </div>
			                </div>
			                <div class="form-group">
                                <div class="col-sm-6 col-sm-offset-2">
                                	<input class="btn btn-primary"  type="submit" name="" id="" value="保 存" />
                                	<input class="btn btn-white" type="button" name="" id="" value="取 消" />
                                </div>
                            </div>
			            </div>
			        </div>
				</div>
            </div>
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}