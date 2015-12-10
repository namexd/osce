@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/personalcenter/css/phone_change.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')
    <script src="{{asset('msc/wechat/user/js/commons.js')}}"></script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
     更换关联手机
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>

</div>
<div class="w_94 phone_icon mart_15">
    <img src="{{asset('msc/wechat/personalcenter/img/phone.png')}}"/>
    <p>您现在的手机号：<span class="this_phone">{{ $user['mobile'] }}</span></p>
</div>
<form id="info_list" name="info_list" method="post" action="{{ url('msc/wechat/personal-center/save-phone') }}" class="mart_5" >
    <div class="add_main">
        <div class="form-group">
            <input type="number" id="mobile"  class="form-control" name="mobile" placeholder="请输入新的手机号码"/>
        </div>
        <div class="form-group">
            <input type="text" class="form-control ipt_code" id="VerificationText" placeholder="请输入验证码"/>
        </div>
        <div class="submit_box">
            <input type="button" class="form-control ipt_huoqu" id="getVerificationButtonOne"   value="获取验证码"/>
            <input type="hidden" name="yz_num" value="0">
        </div>
    </div>
    <div class=" btn-submit">
        <input type="hidden" name="id" value="{{ $user['id'] }}">
        <input  id="change_submit" class="btn"  type="submit" value="确认修改密码" />
    </div>
</form>
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
            }
        });
        $('#change_submit').submit(function(){
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