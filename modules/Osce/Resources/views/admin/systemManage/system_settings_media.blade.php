@extends('osce::admin.layouts.admin_index')

@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<style type="text/css">
	.ibox-title{min-height:34px;line-height:34px;margin:10px 0;background:#F3F7F8;}
	.ibox-content{border:none;}
	.ibox-title h5{height:16px;line-height:16px;margin:0;margin-top:-4px;border:0;}
	.control-label{text-align: right;height:34px;line-height:34px;font-weight: 100;}
	.form-control-feedback{right:25px;}
	.checkbox_input{margin:10px 10px 0 0;font-weight:100;cursor:pointer;}
	.check_name{padding:0;height:16px;position: relative;top:-3px;}
	#checkbox_div .glyphicon{display:none!important;}
	.fa-question-circle{
		float: left;
	    line-height: 10px;
	    margin-left: 10px;
	    font-size: 16px;
	    color: #16beb0;
	}
</style>
@stop

@section('only_js')
	<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
	<script src="{{asset('osce/admin/systemManage/system_manage.js')}}" ></script> 
    <script type="text/javascript">
		//提示保存成功
		$(function(){
			@if(isset($_GET['succ']) && !empty($_GET['succ']))
                layer.msg('保存成功！',{skin:'msg-success',icon:1});
			@endif
        })
    </script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'system_settings_media'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">系统设置</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
        </div>
    </div>
    <form class="container-fluid ibox-content" action="{{route('osce.admin.config.postStore')}}" id="list_form" method='post'>
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs" style="margin-left: 0">
						<li class="active"><a href="{{route('osce.admin.config.getIndex')}}">媒体设置</a></li>
                        <li class=""><a href="{{route('osce.admin.config.getSysparam')}}">场所类型</a></li>
                    </ul>
                </div>
                <div class="ibox float-e-margins">
			        <div class="ibox-title" style="border:none;">
			            <h5>媒体分享</h5>
			        </div>
			        <div class="ibox-content">
			            <div class="row">
			                <div class="col-md-12">
		                        <div class="clearfix form-group" style="margin-bottom: 0;">
		                            <label class="col-sm-2 control-label">通知方式：</label>
		                            <div class="col-sm-10" id="checkbox_div">
		                            	<label class="check_label checkbox_input">
			                                <div class="check_icon @if(array_search('1',$tempDB[0]['value']) !== false) check @endif" style="display: inline-block"></div>
			                                <input type="checkbox" @if(array_search('1',$tempDB[0]['value']) !== false) checked="checked" @endif name="message_type[]" value="1">
			                                <span class="check_name">微信</span>
			                            </label>
			                            <label class="check_label checkbox_input">
			                                <div class="check_icon @if(array_search('2',$tempDB[0]['value']) !== false) check @endif" style="display: inline-block"></div>
			                                <input type="checkbox" @if(array_search('2',$tempDB[0]['value']) !== false) checked="checked" @endif name="message_type[]" value="2" >
			                                <span class="check_name">短信</span>
			                            </label>
			                            <label class="check_label checkbox_input">
			                                <div class="check_icon @if(array_search('3',$tempDB[0]['value']) !== false) check @endif" style="display: inline-block"></div>
			                                <input type="checkbox" @if(array_search('3',$tempDB[0]['value']) !== false) checked="checked" @endif name="message_type[]" value="3">
			                                <span class="check_name">邮件</span>
			                            </label>
			                            <label class="check_label checkbox_input">
			                                <div class="check_icon @if(array_search('4',$tempDB[0]['value']) !== false) check @endif" style="display: inline-block"></div>
			                                <input type="checkbox" @if(array_search('4',$tempDB[0]['value']) !== false) checked="checked" @endif name="message_type[]" value="4">
			                                <span class="check_name">系统消息</span>
			                            </label>
		                            </div>
		                        </div>
			                </div>
			            </div>
			        </div>
			        <div class="ibox-title" style="border:none;">
			            <h5>短信</h5>
			        </div>
			        <div class="ibox-content">
			            <div class="row">
			                <div class="col-md-12 ">
			                	<div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">方式选择：</label>
		                            <div class="col-sm-4">
		                            	
		                            	<select class="form-control" name="sms_cnname">
		                            		<option value="1">{{$tempConfig['messages']['sms']['cnname']}}</option>
	                            		</select>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">地址：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="sms_url" value="{{$tempConfig['messages']['sms']['url']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">用户名：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="sms_username" value="{{$tempConfig['messages']['sms']['username']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">密码：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="sms_password" value="{{$tempConfig['messages']['sms']['password']}}">
		                            </div>
		                        </div>
			                </div>
			            </div>
			        </div>
			        <div class="ibox-title" style="border:none;">
			            <h5>微信</h5><a href="{{route('osce.admin.config.getWeChatHelp')}}" target="_blank"><i class="fa fa-question-circle"></i></a>
			        </div>
			        <div class="ibox-content">
			            <div class="row">
			                <div class="col-md-12 ">
			                	<div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">use_alias：</label>
		                            <div class="col-sm-4">
		                            	<select class="form-control" name="wechat_use_alias">
		                            		@if($tempConfig['messages']['wechat']['use_alias']=="1")
		                            		<option value="true"  selected="selected">true</option>
		                            		<option value="false">false</option>
		                            		@else
		                            		<option value="true">true</option>
		                            		<option value="false" selected="selected">false</option>
		                            		@endif
	                            		</select>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">app_id：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="wechat_app_id" value="{{$tempConfig['messages']['wechat']['app_id']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">secret：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="wechat_secret" value="{{$tempConfig['messages']['wechat']['secret']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">token：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="wechat_token" value="{{$tempConfig['messages']['wechat']['token']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">encoding_key：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="wechat_encoding_key" value="{{$tempConfig['messages']['wechat']['encoding_key']}}">
		                            </div>
		                        </div>
			                </div>
			            </div>
			        </div>
			        <div class="ibox-title" style="border:none;">
			            <h5>邮箱</h5>
			        </div>
			        <div class="ibox-content">
			            <div class="row">
			                <div class="col-md-12 ">
			                	<div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">服务器地址：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="email_server" value="{{$tempConfig['messages']['email']['server']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">服务器协议：</label>
		                            <div class="col-sm-4">
		                            	<select class="form-control" name="email_protocol">
		                            		@if($tempConfig['messages']['email']['protocol']=="POP3")
		                            		<option value="POP3" selected="selected">POP3</option>
		                            		<option value="IMAP">IMAP</option>
		                            		@else
		                            		<option value="POP3">POP3</option>
		                            		<option value="IMAP" selected="selected">IMAP</option>
		                            		@endif
	                            		</select>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">端口：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="email_port" value="{{$tempConfig['messages']['email']['port']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">SSL网关协议：</label>
		                            <div class="col-sm-4">
		                            	<select class="form-control" name="email_ssl">
		                            		@if($tempConfig['messages']['email']['ssl']=="1")
		                            		<option value="true"  selected="selected">true</option>
		                            		<option value="flase">flase</option>
		                            		@else
		                            		<option value="true">true</option>
		                            		<option value="flase" selected="selected">false</option>
		                            		@endif
	                            		</select>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">用户名：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="email_username" value="{{$tempConfig['messages']['email']['username']}}">
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">密码：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="email_password" value="{{$tempConfig['messages']['email']['password']}}">
		                            </div>
		                        </div>
			                </div>
			                <div class="form-group">
			                	<label class="col-sm-2 control-label"></label>
                                <div class="col-sm-9">
                                	<input class="btn btn-primary" style="margin-left:10px;"  type="submit" name="" id="" value="保 存" />
									<!-- <a class="btn btn-white" href="{{url('osce/admin/config/index')}}">取消</a> -->
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