@extends('osce::wechat.layouts.admin')
@section('only_head_css')
    <link href="{{asset('msc/wechat/user/css/commons.css')}}"  rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('osce/common/css/bootstrapValidator.css')}}">
    <style>
		    .user_header{width:100%;height:45px;line-height:45px;text-align: center;background:#1ab394;color:#fff; font-size: 16px;}
		.user_header .header_btn{display:inline-block;width:45px;height:45px;text-align: center}

        .btn{
            margin-top: 0px;
            margin-bottom: 10px;
        }
        .mobile-box{
            margin-top: 10px;
        }
        .text-box{width:94%;margin:0 3%;}
        .jconfirm.white .jconfirm-box .buttons button.btn-default {background: #1ab394;}
        input[type=text],input[type=password]{padding-left: 10px!important;}
    </style>
@stop

@section('content')
    <div class="user_header">
        <a class="left header_btn" href="{{( is_null(session('referer'))? route('osce.wechat.user.getWebLogin'): session('referer') )}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
          忘记密码
    </div>
    <div class="text-box">
        <form id="forget" action="{{route('osce.wechat.user.postResetPassword')}}" method="post" >
	        <div class="form-group mobile-box">
	            <input type="text" class="form-control" placeholder="手机号码" id="mobile" name="mobile" value=""/>
	        </div>
	        <div  class="form-group">
	        	<input style="float:left;width:60%;" type="text" name="verify" class="form-control ipt_txt" placeholder="请输入验证码"/>
                <input type="button"class="right btn btn-default" style="width:38%;font-size:14px;padding:0;text-align:center;background:#1ab394;" id="btn" value="发送手机验证码" />
	        </div>
	        <div class="form-group">
	            <input type="password" name="password" class="form-control ipt_txt" placeholder="请输入新密码"/>
	        </div>
	        <div class="form-group">
	            <input type="password" name="password_confirmation" class="form-control ipt_txt" placeholder="请重复新密码"/>
	        </div>
            <input class="btn" type="submit" style="background:#1ab394;" value="提交审核" />
        </form>
    </div>
    <span id="url" style="display: none;">{{(isset($_GET['reUrl'])?$_GET['reUrl']:'')}}</span>
@stop
@section('footer_js')
	<script type="text/javascript" src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script >
        function getRegPasswordVerfiy(){
            var mobile  =   $('#mobile').val();
            //判断手机号为空
            if(mobile==''){
                $.alert({
                    title: '提示：',
                    content: '手机号不能为空！',
                    confirmButton: '确定',
                    confirm: function(){
                    }
                });
                return false;
            }
            var url     =   '{{route('osce.wechat.user.getResetPasswordVerify')}}?mobile='+mobile;

            SetTime();
            $.get(url,function(data){
                if(data.code==1)
                {
                    setTimeout(bindClick,60000);
                }
                else
                {
                    bindClick();
                }
            });
        }
        //时间计数
        var tim;
        function SetTime(){
            tim = 60;
            var self = setInterval(function(){
               tim -= 1;
               $('#btn').val(tim+'s后再次发送');
               $('#btn').css('background','#ddd'); 
               if(tim == 0){
                    $('#btn').val('发送手机验证码');
                    $('#btn').css('background','#1ab394');
                    clearInterval(self);
                }
            },1000); 
        }


        function bindClick(){
            $('#btn').one('click',getRegPasswordVerfiy);
        }
        $(function(){
            $('#btn').one('click',getRegPasswordVerfiy);
            
            $('#forget').bootstrapValidator({
		        message: 'This value is not valid',
		        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
		            valid: 'glyphicon glyphicon-ok',
		            invalid: 'glyphicon glyphicon-remove',
		            validating: 'glyphicon glyphicon-refresh'
		        },
		        fields: {/*验证*/
		            mobile: {
		                validators: {
		                    notEmpty: {/*非空提示*/
		                        message: '手机号码不能为空'
		                    },
		                    regexp: {
		                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
		                        message: '请输入11位正确的手机号码'
		                    }
		                }
		            }
		        }
		    });

            @if(isset($_GET['succ']) && $_GET['succ'] ==1)
                layer.msg('保存成功！',function (it) {

                    location.href = $('#url').text();
                });
            @endif
        })
    </script>
@stop