<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" ng-app="myApp" class="no-js"> <!--<![endif]-->
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <title>OSCE考试智能管理系统</title>
 <meta name="description" content="">
 <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="{{asset('osce/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
	<link href="{{asset('osce/admin/css/animate.css')}}" rel="stylesheet">
	<link href="{{asset('osce/admin/css/style.css')}}" rel="stylesheet">
 	<link rel="stylesheet" type="text/css" href="{{asset('osce/admin/css/login.css')}}"/>
	<link href="{{asset('osce/admin/css/style.css')}}" rel="stylesheet">
	<script type="text/javascript" src="{{asset('osce/admin/plugins/js/jquery-2.1.1.min.js')}}" ></script>
	<link rel="stylesheet" type="text/txt" src="{{asset('osce/common/css/bootstrapValidator.css')}}"/>
	<script type="text/javascript" src="{{asset('osce/common/js/bootstrapValidator.js')}}"> </script>
	<script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
	<script type="text/javascript">
		$(function(){
    		$('#loginForm').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                username: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
	                        message: '手机号码不能为空'
	                    },
	                    regexp: {
	                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
	                        message: '请输入11位正确的手机号码'
	                    }
                    }
                },
                password: {
                	validators: {
	                	notEmpty: {/*非空提示*/
                            message: '密码不能为空'
                        },
                        stringLength: {
                        	min:6,
                            message: '密码为6~20个字符'
                        }
                    }
                },
	            password_confirmation: {
	                validators: {
	                    notEmpty: {
	                        message: '密码不能为空'
	                    },
                        stringLength: {
                        	min:6,
                            message: '密码为6~20个字符'
                        },
	                    identical: {
	                        field: 'password',
	                        message: '密码不一致'
	                    }
	                }
	            }
            }
		});

    	
    	/**
    	 * 发送短息验证
    	 * @author mao
    	 * @version 3.4
    	 * @date    2016-05-04
    	 * @return  {[type]}   [description]
    	 */
    	function sendMessage() {
    		$('#valid').one('click', function() {
    			if($('#mobile').val() == '') {
    				layer.alert('请输入手机号');
    				sendMessage();
    				return;
    			}
    			var url = '{{route('osce.wechat.user.getResetPasswordVerify')}}?mobile='+$('#mobile').val();

    			$.ajax({
    				type:'get',
    				url:url,
    				success:function(data) {
	    				if(data.code == 1) {
	    					//设置时间
				    		var count = 60,
				    			$that = $(this);

				    		$that.attr('disabled','disabled');  //不可点
				    		var self = setInterval(function() {
				    			$that.text(count + 's');
				    			count --;

				    			//时间到重绑定，状态更新
				    			if(count < 0) {
				    				clearInterval(self);
				    				$that.removeAttr('disabled');
				    				$that.text('发送验证码');
				    				sendMessage();
				    			}
				    		}, 1000);
	    				} else {
	    					layer.msg(data.message,{skin:'msg-error',icon:1});
	    					sendMessage();
	    				}
	    			},
	    			error:function(data) {
	    				layer.msg(data,{skin:'msg-error',icon:1});
	    				sendMessage();
	    			}
    		    });
	    	})
    	}
    	//初始化
    	sendMessage();


		})
	</script>
	<?php
		$errorsInfo =(array)$errors->getMessages();
		if(!empty($errorsInfo)){
			$errorsInfo = array_shift($errorsInfo);
		}
	?>
	@forelse($errorsInfo as $errorItem)
		<div class="pnotice" style="display: none;">{{$errorItem}}</div>
	@empty
	@endforelse
	<script>
		$(function(){
			//错误提示
			var msg = $('.pnotice').text();
			if(msg==''){
				$("#passwdTip").css('display','none');
				return;
			}else{
				$("#passwdTip").css('display','block');
			}
		})
	</script>
	<style>
		.logo {margin: 0;}
		.logo strong{
			margin: 0;
			font-weight: 600;
			font-size: 18px;
		}
		.logo a {
			float: right;
			display: inline-block;
			color: #676a6c;
			margin-right: 10px;
			margin-top: 10px;
			font-weight: 600;
		}
		.btn:active{background-color: #1dc5a3!important;}
		.btn:visited{background-color: #1dc5a3!important;}
		.btn:hover{background-color: #1dc5a3!important;}
		.btn:focus{background-color: #1dc5a3!important;}
		
		/*layer alert*/
		.layui-layer-title{
		    background: #fff!important;
		    color: #ed5565!important;
		    font-size: 16px!important;
		}
		.layui-layer-btn {
		    background: #fff !important;
		    border-top: 1px #fff solid !important;
		}
		.layui-layer-btn .layui-layer-btn0 {
		    border-color: #ed5565;
		    background-color: #ed5565;
		}
	</style>
</head>
<body>
		<div class="middle-box loginscreen animated fadeInDown">
			<div style="background:#fff;margin-top:60px;padding:20px;border-top:3px solid #1dc5a3;">
				<div class="logo">
					<strong>忘记密码</strong>
					<a href="{{route('osce.admin.getIndex')}}">返回></a>
				</div>
				<form class="m-t" role="form" id="loginForm" action="{{route('osce.admin.user.postResetPassword')}}" method="post" >
					<div class="form-group">
						<input type="text" class="form-control" id="mobile" name="mobile"  placeholder="手机号码">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" name="verify" placeholder="请输入验证码" style="width: 60%;float: left;">
						<a class="btn btn-primary" id="valid" href="javascript:void(0);" style="margin-left:3%;width: 37%;float: left;margin-top: 0;margin-bottom: 0;">发送验证码</a>
						<div class="clearfix"></div>
					</div>
					<div class="form-group">
						<input type="password" class="form-control" id="username" name="password"  placeholder="请输入新密码">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" id="password" name="password_confirmation" placeholder="请重复新密码">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">
						提交审核
					</button>
				</form>
			</div>
		</div>
	</body>
</html>