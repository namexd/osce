@extends('msc::wechat.layouts.user')


@section('only_head_css')
    <link href="{{asset('msc/wechat/user/css/commons.css')}}"  rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('msc/common/css/bootstrapValidator.css')}}">
    <style>
        /*表单验证提示部分*/
        .form-group{
            height: inherit;
            line-height: inherit;
        }
        .form-group label{line-height: 36px;}
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #444;
            line-height: 36px;
            margin-left: 38%;
        }
    </style>
@stop
@section('only_head_js')

    <script src="{{asset('msc/wechat/user/js/commons.js')}}"></script>
    <script type="text/javascript" src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script>


@stop
@section('content')
<div class="user_header">登录绑定</div>
<div class="login_box">
    <form name="form" method="post" id="bling"  action="{{ url('msc/wechat/user/user-binding-op') }}">
        <div class="form-group">
            <input type="password" name="password" class="form-control ipt ipt_pwd" placeholder="请输入新密码"/>
        </div>
        <div class="form-group">
            <input type="password" name="repassword" class="form-control ipt ipt_pwd" placeholder="请再次输入新密码"/>
        </div>
        <div class="form-group">
            <input type="text" class="form-control ipt ipt_phone" name="mobile" id="mobile"   placeholder="请输入你的手机号码"/>
        </div>
        <div class="form-group" style="width:35%;float:right;" >
            <input type="button" class="form-control ipt_huoqu" id="getVerificationButtonOne" value="获取验证码"/>
            <input type="hidden" name="yz_num" value="0">
        </div>

        <div class="form-group" style="width:60%;float: left;">
            <input type="text" name="input_yz" class="form-control ipt_code center" id="VerificationText" placeholder="请输入验证码"/>
        </div>

        <input type="hidden" name="id" value="{{ $user['id'] }}">
        <input class="btn btn2" id="bling_submit" type="submit" value="提 交"/>
    </form>
</div>
<script type="text/javascript">
    $(function(){

        $('#bling').bootstrapValidator({
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
                password: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '请输入您的密码'
                        },
                        stringLength: {
                            required: true,
                            minlength:12,
                            message: '密码必须6个字符以上'
                        },

                    }

                },
                repassword: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '请再次输入密码'
                        },
                        stringLength: {
                            required: true,
                            minlength:12,
                            message: '密码必须6个字符以上'
                        },
                        identical: {
                            field: 'password',
                            message: '两次输入的密码不一致'
                        },
                    }

                },

            }
        });

        $('#bling_submit').submit(function(){
            var yz_num = $('input[name="yz_num"]').val();
            if(yz_num=="0"){
                $.alert({
                    title: '提示：',
                    content: '验证码错误!',
                    confirmButton: '确定',
                    confirm: function(){
                    }
                });
                return false;

            }else{

            }

        })

        $('#getVerificationButtonOne').click(function(){
            var moblie = $('#mobile').val();
            var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
            if(reg.test(moblie)){
                duMiao($('#getVerificationButtonOne'));
                $('#VerificationText').attr('disabled',false);
                $('#getVerificationButtonOne').next('input').val(0);
                $.ajax("{{ url('/api/1.0/public/msc/user/reg-moblie-verify') }}",{
                    type: 'get',
                    data: {mobile:moblie},
                    success:function(data, textStatus, jqXHR) {
                        //console.log(data);
                    },
                    error:function(result) {
                        //console.log(result);
                    },
                    dataType: "json"
                });
            }else{
                $.alert({
                    title: '提示：',
                    content: '手机号错误!',
                    confirmButton: '确定',
                    confirm: function(){
                    }
                });
            }
        })

        $('#VerificationText').blur(function(){
            var moblie = $('#mobile').val();
            var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
            if(reg.test(moblie)) {
                var obj = {mobile: $('#mobile').val(), code: $('#VerificationText').val()};
                $.ajax("{{ url('/api/1.0/public/msc/user/reg-check-mobile-verfiy') }}", {
                    type: 'get',
                    data: obj,
                    success: function (data, textStatus, jqXHR) {
                        if (data.code == 1) {
                            $('#VerificationText').attr('disabled', 'disabled');
                            $('#getVerificationButtonOne').next('input').val(1);
                        } else {
                            $.alert({
                                title: '提示：',
                                content: data.message,
                                confirmButton: '确定',
                                confirm: function () {
                                }
                            });
                        }
                    },
                    error: function (result) {
                        $.alert({
                            title: '提示：',
                            content: "手机号码有误！或者该手机号码已经被注册",
                            confirmButton: '确定',
                            confirm: function () {
                            }
                        });
                    },
                    dataType: "json"
                });
            }
        })
    })

</script>
@stop