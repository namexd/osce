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
	<script>
		$(function(){
			$('.col-sm-4').click(function(){
				var appid = $('.form-control').val();
				if(appid==''){
					$.alert({
						title: '提示：',
						content: '请输入在第一步中输入注册微信时的appID!',
						confirmButton: '确定',
						confirm: function(){
						}
					});
				}else{

					$(this).find('> a').attr('href','https://open.weixin.qq.com/connect/qrconnect?' +
							'appid='+$('.form-control').val()+
							'&scope=snsapi_login&redirect_uri=http%3A%2F%2Fmp.weixin.qq.com%2Fdebug%2Fcgi-bin%2Fsandbox%3Ft%3Dsandbox%2Flogin');
//						alert(	$(this).find('> a').attr('href'))
				}

			});
		})
	</script>





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
										<label class="col-sm-2 control-label"><h5>微信配置声明：</h5></label>
										<div class="col-sm-4">
											<p>此份说明，只是用于测试公众号申请的配置。如果你已有微信公众号请按照您的公众号进行配置。</p>

										</div>
									</div>

									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第一步：</h5></label>
										<div class="col-sm-9">
										<a href="http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login " target="_blank">点击此处进入微信公众平台接口测试帐号申请登陆页面</a>

											<p>
												点击登陆->用手机扫描二维码->手机上点击确认登陆->进入测试号管理
											</p>

										</div>
									</div>
									{{--<div class="clearfix form-group">--}}
										{{--<label class="col-sm-2 control-label"><h5>第二步：</h5></label>--}}
										{{--<div class="col-sm-4">--}}
											{{--<a target="_blank">请先点击此处在微信中获取到（appID，appsecret）</a>--}}

										{{--</div>--}}
									{{--</div>--}}
									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第二步：</h5></label>
										<div class="col-sm-9">
											<p>请将获取到的appID放在系统设置->微信->app_id的输入框中;</p>
											<p>请将获取到的appsecret放在系统设置->微信->secret的输入框中;</p>
										</div>
									</div>
									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第三步：</h5></label>
										<div class="col-sm-9">
											<p>请将此下面url地址，放入微信中的接口配置中的url中</p>
											<input type="text" class="form-control" id="" name="email_server" value='http://{{$url}}/api/1.0/public/osce/wechat/token'>

										</div>
									</div>
									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第四步：</h5></label>
										<div class="col-sm-9">
											<p>
												请在系统设置中设置token值，然后将这个token值放入微信中的接口配置中，两个token值一定要相同
												请保存好该token值，以便后面使用。
											</p>
										</div>
									</div>
									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第五步：</h5></label>
										<div class="col-sm-9">
											<p>请根据自己需要在系统设置中设置encoding_key值，请保存好该encoding_key值，以便后面使用</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	</div>
	</div>
@stop{{-- 内容主体区域 --}}