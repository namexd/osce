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
	<script type="text/javascript">
		$(function(){
			$(".checkbox_input").click(function(){
    			if($(this).find("input").is(':checked')){
					$(this).find(".check_icon ").addClass("check");
				}else{
					$(this).find(".check_icon").removeClass("check");
				}
    		});
    		
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
                            message: '用户名不能为空'
                        },
                        stringLength: {
                        	min:2,
                            max:64,
                            message: '用户名2~64个字符'
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
                }
            }
		});
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
</head>
<body>
		<div class="middle-box loginscreen animated fadeInDown">
			<div style="background:#fff;margin-top:60px;padding:20px;border-top:3px solid #1dc5a3;">
				<div class="logo">
					<h3>忘记密码</h3>
					<a href="javascript:void(0)" style="float: right;">返回></a>
				</div>
				<form class="m-t" role="form" id="loginForm" method="post" action="{{ route('osce.admin.postIndex') }}">
					<div class="form-group">
						<input type="text" class="form-control" id="username" name="username"  placeholder="手机号码">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" id="password" name="valid" placeholder="请输入验证码" style="width: 60%;float: left;">
						<input type="button" class="btn btn-primary block m-b" value="发送验证码" name="valid" style="margin-left:3%;width: 37%;float: left;margin-top: 0;margin-bottom: 0;">
						<div class="clearfix"></div>
					</div>
					<div class="form-group">
						<input type="password" class="form-control" id="username" name="password"  placeholder="请输入新密码">
					</div>
					<div class="form-group">
						<input type="password" class="form-control" id="password" name="re_password" placeholder="请重复新密码">
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">
						提交审核
					</button>
				</form>
			</div>
		</div>
	</body>
</html>