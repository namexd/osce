@extends('osce::admin.layouts.admin_index')

@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<style type="text/css">
	.ibox-title{min-height:34px;line-height:34px;margin:10px 0;background:#F3F7F8;}
	.control-label{text-align: right;height:34px;line-height:34px;font-weight: 100;}
	.ibox-title h5{line-height: 6px;}
	.col-sm-9 p{line-height: 30px;}
</style>
@stop

@section('only_js')
	
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">系统设置说明</h5>
        </div>
    </div>
    <div class="container-fluid ibox-content">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="ibox float-e-margins">
			        <div class="ibox-title" style="border:none;">
			            <h5>微信</h5>
			        </div>
			        <div class="ibox-content" style="border:none;">
			            <div class="row">
			                <div class="col-md-12 ">
								<div class="clearfix form-group">
									<label class="col-sm-2 control-label"><h5>第一步：</h5></label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="" name="email_server" >
									</div>
								</div>
			                	<div class="clearfix form-group">
		                            <label class="col-sm-2 control-label"><h5>第二步：</h5></label>
		                            <div class="col-sm-4">
										<a href="http://www.cnblogs.com/A-Song/archive/2011/12/14/2288215.html" target="_blank">请先点击此处在微信中获取到（appID，appsecret）</a>

		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label"><h5>第三步：</h5></label>
		                            <div class="col-sm-9">
		                                <p>请将获取到的appID放在系统设置->微信->app_id,</p>
		                                <p>请将获取到的appsecret放在系统设置->微信->secret</p>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label"><h5>第四步：</h5></label>
		                            <div class="col-sm-9">
										<p>请将获取到的appID放在系统设置->微信->app_id,</p>
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">token：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="wechat_token" >
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">encoding_key：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="wechat_encoding_key" >
		                            </div>
		                        </div>
			                </div>
			            </div>
			        </div>
			        <div class="ibox-title" style="border:none;">
			            <h5>微信对应设置</h5>
			        </div>
			        <div class="ibox-content" style="border:none;">
			            <div class="row">
			                <div class="col-md-12 ">
			                	<div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">app_id：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="email_server" >
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">secret：</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="" name="email_server" >
									</div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">token：</label>
		                            <div class="col-sm-9">
		                                <input type="text" class="form-control" id="" name="email_port" >
		                            </div>
		                        </div>
		                        <div class="clearfix form-group">
		                            <label class="col-sm-2 control-label">encoding_key：</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="" name="email_server" >
									</div>
		                        </div>
		                        {{--<div class="clearfix form-group">--}}
		                            {{--<label class="col-sm-2 control-label">用户名：</label>--}}
		                            {{--<div class="col-sm-9">--}}
		                                {{--<input type="text" class="form-control" id="" name="email_username" >--}}
		                            {{--</div>--}}
		                        {{--</div>--}}
		                        {{--<div class="clearfix form-group">--}}
		                            {{--<label class="col-sm-2 control-label">密码：</label>--}}
		                            {{--<div class="col-sm-9">--}}
		                                {{--<input type="text" class="form-control" id="" name="email_password" >--}}
		                            {{--</div>--}}
		                        {{--</div>--}}
			                </div>
			            </div>
			        </div>
				</div>
            </div>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}