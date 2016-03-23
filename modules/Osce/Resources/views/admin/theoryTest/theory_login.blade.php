<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" ng-app="myApp" class="no-js"> <!--<![endif]-->
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <title>OSCE理论考试</title>
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
</head>
<body>
		<div class="middle-box loginscreen animated fadeInDown">
			<div style="background:#fff;margin-top:60px;padding:20px;border-top:3px solid #1dc5a3;">
				<div class="logo">
					<img src="{{asset('osce/images/logo.png')}}" width="100%"/>
				</div>
				<form class="m-t" role="form" id="loginForm" method="post" action="{{ route('osce.admin.ApiController.LoginAuthInfo') }}">
					<h3 class="tt">
						用户名
					</h3>
					<div class="form-group">
						<input type="text" class="form-control" id="username" name="username"  placeholder="用户名">
					</div>
					<h3 class="tt">
						密码
					</h3>
					<div class="form-group">
						<input type="password" class="form-control" id="password" name="password" placeholder="密码">
					</div>
					<input type="hidden" name="grant_type" id="grant_type" value="password">
					<input type="hidden" name="client_id"  id="client_id" value="ios">
					<input type="hidden" name="client_secret" id="client_secret" value="111">
					<div class="clearfix">
						<label class="check_label checkbox_input" style="display: none">
                            <div class="check_icon check" style="display: inline-block"></div>
                            <input type="checkbox" name="message_type[]" value="1" data-bv-field="message_type[]">
                            <span class="check_name">记住密码</span>
                        </label>
						<a style="float:right" href="{{route('osce.wechat.user.getForgetPassword')}}">
							<small class="txt">忘记密码？</small>
						</a>
					</div>
					<button type="submit" class="btn btn-primary block full-width m-b">
						登 录
					</button>
				</form>
			</div>
		</div>
	</body>
</html>