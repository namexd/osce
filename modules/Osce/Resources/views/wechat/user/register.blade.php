@extends('osce::wechat.layouts.user')
@section('only_head_css')
    <link rel="stylesheet" href="{{asset('osce/common/css/bootstrapValidator.css')}}">

    {{--select输入效果--}}
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
    <style rel="stylesheet">
        .select2-container--default .select2-selection--single{ height: 36px;
            border: 1px solid #ccc;}
        .select2-container--default .select2-selection--single .select2-selection__arrow{top:3px;}
        .has-feedback label~.form-control-feedback {top: 2px;}
        .form-control{
            color: #333!important;
        }
        .user_header,.btn{background: #1ab394;}
        .must{
            color: #ff0000;
        }
        .layui-layer-title{
            background: #fff!important;
            color: #1ab394!important;
            font-size: 16px!important;
        }
        .layui-layer-btn {
            background: #fff !important;
            border-top: 1px #fff solid !important;
        }
        .layui-layer-btn0{
            border:1px solid #1ab394!important;
            background: #1ab394 !important;
        }
        i.form-control-feedback.glyphicon.glyphicon-ok, i.form-control-feedback.glyphicon.glyphicon-remove{display: none!important;}
    </style>
@stop

@section('only_head_js')
    <script type="text/javascript" src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('osce/wechat/user/js/commons.js')}}"></script>
    <script src="{{asset('osce/wechat/user/js/register.js')}}"></script>
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>
@stop
    
@section('content')
<div class="user_header">
    <a class="left header_btn" href="{{route('osce.wechat.user.getWebLogin')}}">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
    用户注册
    <a class="right header_btn" href="javascript:void(0)"></a>
</div>

<div class="clear"></div>
<div class="container" id="container">
    <div >
        {{-- RegTeacherOp --}}
        <form name="form" style="padding-top: 15px" method="post" id="sourceForm" action="{{route('osce.wechat.user.postRegister')}}" id="frmTeacher">
            <div class="form-group">
                <input type="text" name="nickname" class="form-control" id="name" placeholder="昵称"/>
            </div>
            <div class="form-group">
                <input  type="text" name="name" class="form-control" id="name" placeholder="姓名"/>
            </div>
            <div class="form-group">
                <div class="radio_box">
                    <label class="left radio_label" for="radio_1">
                        <div class="left radio_icon"></div>
                        <b class="left">男</b>
                        <input type="radio" id="radio_1" name="gender" value="1"/>
                    </label>
                    <label class="left radio_label" for="radio_2">
                        <div class="left radio_icon"></div>
                        <b class="left">女</b>
                        <input type="radio" id="radio_2" name="gender" value="2"/>
                    </label>
                </div>
            </div>
            <div class="clear"></div>
            <div class="form-group card-list" style="width:35%;height:36px;float: left;">
               <select class="form-control normal_select select_indent" name="type" id="card-list" style="height:36px;">
                   <option value="0">角色类型</option>
                   <option value="1">学生</option>
                   <option value="2">老师</option>
               </select>
               <label for="ipt_zjh"><span class="must"></span></label>
           </div>

            <div class="form-group card-list no_zjh" style="width:63%;float:right;" >
                <input type="text" class="form-control" style="padding-left:2px;" disabled="disabled"  placeholder="请选择角色类型" />
            </div>
            <div class="form-group card-list ipt_zjh" style="width:63%;float:right;display: none">
                <input style="padding-left:2px;"  class="form-control " name="idcard"   placeholder="请输入身份证号码" />
            </div>
            <div class="form-group card-list hz_zjh" style="width:63%;float:right;display: none">
                <input style="padding-left:2px;"  class="form-control " disabled="disabled" name="idcard2"/>
            </div>
            <div class="clear"></div>
            <div class="form-group">
                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="请输入手机号"/>
            </div>
            <div class="clear"></div>

            <div class="form-group">
                <input type="button" class="btn" value="点击发送验证码" id="send_code" style="width:40%;height:36px;color:#fff;text-align:center;padding:0;float:right;">
                <input type="text" name="code" class="form-control ipt_txt" placeholder="请输入验证码" style="width:59%;float:left;"/>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>

            <div class="form-group">
                <input type="password" name="password" class="form-control ipt_txt" placeholder="请输入密码"/>
            </div>
            <div class="form-group">
                <input type="password" name="password_confirmation" class="form-control ipt_txt" placeholder="请输入再次确认密码"/>
            </div>
            <!--<span class="error" ng-show="form.$dirty && form.name.$invalid">填写格式错误</span>-->
            <input type="hidden"  name="url" value="{{@$url}}">
            <input class="btn" style="width:100%;color:#fff;margin-bottom:20px;" type="submit" id="register" value="提交审核" />
        </form>

    </div>
</div>
<script>
    $(document).ready(function(){
        //点击发送验证码
        $("#send_code").click(function(){
            var phone = $('#mobile').val();
            var req=/^1[3|5|7|8]{1}[0-9]{9}$/;
            if(phone==''){
            	$.alert({
	                title: '提示：',
	                content: '请输入手机号!',
	                confirmButton: '确定',
	                confirm: function(){
	                }
	            });
	            return false;
            }
            if(!(req.test(phone))){
	            $.alert({
	                title: '提示：',
	                content: '手机号错误!',
	                confirmButton: '确定',
	                confirm: function(){
	                }
	            });
	            return false;
            }
            $.ajax({
                type:'post',
                url:'{{route("osce.wechat.user.postRevertCode")}}',
                data:{mobile:phone},
                success:function(res){
                    if(res.code==1){
                    	$.alert({
			                title: '提示：',
			                content: '发送成功!',
			                confirmButton: '确定',
			                confirm: function(){
			                }
			            });
                    }else{
                        layer.alert(res.message);
                    }
                }
            })
        });


        $(".radio_label").click(function(){
            if($(this).children("input").checked=="true"){
                $(this).children(".radio_icon").removeClass("check");
            }else{
                $(".radio_icon").removeClass("check");
                $(this).children(".radio_icon").addClass("check");
            }
        });

    /*mao 2015-11-26
     *表单验证 老师
     */
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {/*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '姓名不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 2,
                        max: 30,
                        message: '姓名长度必须在2到30之间'
                    }/*最后一个没有逗号*/
                }
            },
            nickname: {/*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '昵称不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 2,
                        max: 30,
                        message: '昵称长度必须在2到30之间'
                    }/*最后一个没有逗号*/
                }
            },
            gender: {
                validators: {
                    notEmpty: {
                        message: '请选择性别'
                    }
                }
            },
            code: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '验证码不能为空'
                    }
               }
            },
            professional: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '不能为空'
                    }                }
            },
            mobile: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    regexp: {
                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
                        message: '请输入正确的11位手机号码'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: '{{route('osce.wechat.user.getProofNumber')}}',//验证地址
                        message: '该手机号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'get'//请求方式
                    }
               }
            },
            idcard: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '证件号码不能为空'
                    },
                    regexp: {
                        regexp: /^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/,
                        message: '请输入正确的身份证号码'
                    }
                }
            },
            yz_num: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '验证码不能为空'
                    },
                    identical: {
                        field: '1',
                        message: '验证码错误'
                    },
                }
            },
            password: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请输入您的密码'
                    },
                    stringLength: {
                        required: true,
                        min:6,
                        message: '密码必须6个字符以上'
                    },

                }

            },
            password_confirmation: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请再次输入密码'
                    },
                    stringLength: {
                        required: true,
                        min:6,
                        message: '密码必须6个字符以上'
                    },
                    identical: {
                        field: 'password',
                        message: '两次输入的密码不一致'
                    }
                }

            }
        }
    });


    /*证件选择*/
    $('#card-list').change(function(){
        var type=$(this).val();
        if(type=="0"){
            $(".no_zjh").show();
            $(".ipt_zjh").hide();
            $(".hz_zjh").hide();
        }
       if(type=="1"){
           $(".ipt_zjh").show();
           $(".no_zjh").hide();
           $(".hz_zjh").hide();
        }
        if(type=="2"){
            $(".hz_zjh").show();
            $(".ipt_zjh").hide();
            $(".no_zjh").hide();
        }
    });



    })
    function formatRepo (repo) {
        if (repo.loading) return '没有相关信息';

        var markup = "<div class='select2-result-repository clearfix'>" +repo.name +"</div>";

        return markup;

    }

    function formatRepoSelection (repo) {

        return repo.name;

    }

    function initcard(){//表单切换

        $(".form-select div").unbind("click").click(function(){
            $(this).addClass("checked").siblings().removeClass("checked");
            var index=$(this).index();
            $("#container>div").eq(index).show().siblings("div").hide();

        });

    }

    $('#register').click(function(){
        var role_type=$('#card-list option:selected').val();//角色类型
        if(role_type==0){
            $.alert({
                title: '提示：',
                content: '角色类型必填',
                confirmButton: '确定',
                confirm: function(){
                }
            });
            return false;
        }

    })
</script>
@stop