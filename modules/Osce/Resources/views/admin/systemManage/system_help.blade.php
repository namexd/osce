@extends('osce::admin.layouts.admin_index')

@section('only_css')
	<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
	<style type="text/css">
		.ibox-title{min-height:34px;line-height:34px;margin:10px 0;background:#F3F7F8;}
		.control-label{text-align: right;height:34px;line-height:34px;font-weight: 100;}
		.ibox-title h5{line-height: 6px;}
		.col-sm-9 p{line-height: 30px;}
		.div_box{background:#e3e3e3;border-radius:5px;padding:20px;color:#000}
		.pdl2{padding-left: 2em}
		.pdl4{padding-left: 4em}
		.pdl6{padding-left: 6em}
		.pdl8{padding-left: 8em}
		.pdl10{padding-left: 10em}
		.pdl12{padding-left: 12em}
	</style>
@stop

@section('only_js')
	<link rel="stylesheet" type="text/txt" src="{{asset('osce/common/css/bootstrapValidator.css')}}"/>
	<script type="text/javascript" src="{{asset('osce/common/js/bootstrapValidator.js')}}"> </script>
	<script>
		$(function(){
//			$('.col-sm-4').click(function(){
//				var appid = $('.form-control').val();
//				if(appid==''){
//					$.alert({
//						title: '提示：',
//						content: '请输入在第一步中输入注册微信时的appID!',
//						confirmButton: '确定',
//						confirm: function(){
//						}
//					});
//				}else{
//
//					$(this).find('> a').attr('href','https://open.weixin.qq.com/connect/qrconnect?' +
//							'appid='+$('.form-control').val()+
//							'&scope=snsapi_login&redirect_uri=http%3A%2F%2Fmp.weixin.qq.com%2Fdebug%2Fcgi-bin%2Fsandbox%3Ft%3Dsandbox%2Flogin');
////						alert(	$(this).find('> a').attr('href'))
//				}
//
//			});


			$('.help').click(function(){
				var  appid= $('#appId').val();
				var  url= $('#urls').val();
				var encoding_key = $('#encoding_key').val();
				if(appid==''){
					$('#div_show').hide();
					$.alert({
						title: '提示：',
						content: '请输入注册微信时的appID!',
						confirmButton: '确定',
						confirm: function(){
						}
					});
				} else if(url==''){
					$('#div_show').hide();
					$.alert({
						title: '提示：',
						content: '请输入你的域名!',
						confirmButton: '确定',
						confirm: function(){
						}
					});
				}else if(encoding_key==''){
					$('#div_show').hide();
					$.alert({
						title: '提示：',
						content: '请输入系统设置中的encoding_key!',
						confirmButton: '确定',
						confirm: function(){
						}
					});
				}else{
					$('#div_show').show();
				}

				$('#http').html('"url":"https:\/\/open.weixin.qq.com\/connect\/oauth2\/authorize?appid='+appid+'&redirect_uri=http%3A%2F%2F'+url+'%2Fosce%2Fwechat%2Fuser%2Flogin&response_type=code&scope=snsapi_base&state='+encoding_key+'#wechat_redirect",');
			})
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
										<a href="http://mp.weixin.qq.com/debug/cgi-bin/sandbox?t=sandbox/login" target="_blank">点击此处进入微信公众平台接口测试帐号申请登陆页面</a>

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


									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第六步：</h5></label>
										<div class="col-sm-9">
											<p>请在（测试号管理->体验接口权限表->网页服务->网页帐号->点击修改）
												请在弹出的框中填上你的域名。请注意该域名不可以有http://
											</p>
										</div>
									</div>

									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第七步：</h5></label>
										<div class="col-sm-9">
											<p>请在（测试号管理->体验接口权限表->对话服务->基础支持->获取access_token）跳转到获取access_token页面
												请点击该页面最后的（使用网页调试工具调试该接口）跳转到（微信公众平台接口调试工具）页面。

											</p>
										</div>
									</div>

									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第八步：</h5></label>
										<div class="col-sm-9">
											<p>
												请在该页面中的（使用说明->（3)文本框中填入appid和secret，该值为（测试号管理）页面的（appID和appsecret））
												其他的值不要改变，填好后点击检查问题按钮。
											</p>
										</div>
									</div>
									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第九步：</h5></label>
										<div class="col-sm-9">
											<p>
												请把（基础支持：获取access_token接口/token）里面的（还回结果->access_token）里面的值复制下来。

											</p>
										</div>
									</div>
									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第十步：</h5></label>
										<div class="col-sm-9">
											<p>
												请把当前页面中的（使用说明->（3）->第一项：接口类型的值改为（自定义菜单），第二项：接口列表的值改为（自定义菜单创建接口/menu/create））
												然后将刚才的access_token值放入 （三、参数列表->access_token中）<br/>

												请输入appId<input style="width: 150px;"type="text"  class="form-control name edit-name" name="appid" id="appId"/>
												请输入公众号域名<input style="width: 150px;"type="text"  class="form-control name edit-name"  name="url" id="urls" value="{{$url}}"/>
												请输入用户设置的encoding_key<input style="width: 150px;"type="text"  class="form-control name edit-name"  name="encoding"   id="encoding_key"/><br/>
												  <input  class="help" type="button" value="点击生成(body)里需要的数据"/>
											</p>
											将下面生成的数据复制放入（body）中然后点击检查问题<br/>
												<div class="div_box" id="div_show" hidden>
													<p>{</p>
													<p class="pdl2">"button":[</p>
													<p class="pdl4">{</p>
													<p class="pdl6">"name":"Osce考试系统",</p>
													<p class="pdl8">"sub_button":[</p>
													<p class="pdl10">{</p>
													<p class="pdl12">"type":"view",</p>
													<p class="pdl12">"name":"登录",</p>
													<p class="pdl12" id="http">"url":"https:\/\/open.weixin.qq.com\/connect\/oauth2\/authorize?appid=【微信公众号ID】&redirect_uri=http%3A%2F%2F【公众号域名】%2Fosce%2Fwechat%2Fuser%2Flogin&response_type=code&scope=snsapi_base&state=【用户设置的encoding_key】#wechat_redirect",</p>
													<p class="pdl12">"sub_button":[]</p>
													<p class="pdl10">}</p>
													<p class="pdl6">]</p>
													<p class="pdl4">}</p>
													<p class="pdl2">]</p>
													<p>}</p>
												</div>
										</div>
									</div>

									<div class="clearfix form-group">
										<label class="col-sm-2 control-label"><h5>第十一步：</h5></label>
										<div class="col-sm-9">
											<p>
											如果第十步返回成功 ，这时到（测试号管理）中扫描二维码并关注公众号这时你的配置就已全部完成。<br/>
											如果失败，请检查步骤是否正确或者按照步骤重新配置一次。

											</p>
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