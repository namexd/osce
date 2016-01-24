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
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
    用户注册
    <a class="right header_btn" href="javascript:void(0)"></a>
</div>

<div class="clear"></div>
<div class="container" id="container">
    <div >
        {{-- RegTeacherOp --}}
        <form name="form" method="post" id="sourceForm" action="{{route('osce.wechat.user.postRegister')}}" id="frmTeacher">
            <div class="form-group">
                <label for="name">昵 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;称<span class="must">*</span></label>
                <input  type="text" name="nickname" class="form-control" id="name"/>
            </div>
            <div class="form-group">
                <label for="name">姓 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名<span class="must">*</span></label>
                <input  type="text" name="name" class="form-control" id="name"/>
            </div>
            <div class="form-group">
                <div class="radio_box">
                    <label class="left radio_label" for="radio_1">
                        <div class="left radio_icon"></div>
                        <b class="left">男</b>
                        <input type="radio" id="radio_1" name="gender" value="1"/>
                    </label>
                    <label class="left radio_label" for="radio_2" style="margin-left:50px">
                        <div class="left radio_icon"></div>
                        <b class="left">女</b>
                        <input type="radio" id="radio_2" name="gender" value="2"/>
                    </label>
                </div>
            </div>
            <div class="form-group card-list" style="width:35%;float: left;">
               <select name="type" id="card-list">
                   <option value="0">角色类型</option>
                   <option value="1">学生</option>
                   <option value="2">老师</option>
               </select>
               <label for="ipt_zjh"><span class="must">*</span></label>
           </div>

            <div class="form-group card-list no_zjh" style="width:65%;float:right;" >
                <input type="text" class="form-control" style="padding-left:2px;" disabled="disabled"  placeholder="请选择角色类型" />
            </div>
            <div class="form-group card-list ipt_zjh" style="width:65%;float:right;display: none">
                <input style="padding-left:2px;"  class="form-control " name="idcard"   placeholder="请输入身份证号码" />
            </div>
            <div class="form-group card-list hz_zjh" style="width:65%;float:right;display: none">
                <input style="padding-left:2px;"  class="form-control " disabled="disabled" name="idcard2"/>
            </div>
            <div class="clear"></div>
            <div class="form-group">
                <label for="mobile">手机号码<span class="must">*</span></label>
                <input type="number" class="form-control" id="mobile" name="mobile" />
            </div>
            <div class="clear"></div>

            <div class="form-group">
                <label for="code">验证码<span class="must">*</span></label>&nbsp;&nbsp; <input type="button" value="点击发送验证码" id="send_code">
                <input type="text" name="code" class="form-control ipt_txt" placeholder="请输入验证码"/>
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
            <input class="btn" type="submit" id="register" value="提交审核" />
        </form>

    </div>
</div>
<script>
    $(document).ready(function(){
        //点击发送验证码
        $("#send_code").click(function(){
            var phone = $('#mobile').val();
            var status = false;
            if(phone=='')layer.alert('请输入手机号！',function(its){
                status = true;
                layer.close(its);
            })
            if(status)return;
            $.ajax({
                type:'post',
                url:'{{route("osce.wechat.user.postRevertCode")}}',
                data:{mobile:phone},
                success:function(res){
                    if(res.code==1){
                        layer.alert('发送成功！');
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


        initcard();//表单切换
        var url = "{{ url('api/1.0/public/msc/user/teacher-dept-list') }}";
        $("#teacher_dept").select2({
            ajax: {
                url: "{{ url('api/1.0/public/msc/user/teacher-dept-list') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: 1
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: data.data.rows
                    }
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
        $("#Professional").select2({
            ajax: {
                url: "{{ url('/api/1.0/public/msc/user/professional-list') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        page: 1
                    };
                },
                processResults: function (data, page) {
                    return {
                        results: data.data.rows
                    }
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
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
                        message: '性别不能为空'
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
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
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

    /*mao 2015-11-26
     *表单验证 学生
     */
    $('#sourceForm-student').bootstrapValidator({
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
            sex2: {
                validators: {
                    notEmpty: {
                        message: '性别不能为空'
                    }
                }
            },
            code: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '描述不能为空'
                    }
               }
            },
            catergory: {
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
            idcard2: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '证件号码不能为空'
                    },
                    regexp: {
                        regexp: /^1[45][0-9]{7}|G[0-9]{8}|P[0-9]{7}|S[0-9]{7,8}|D[0-9]+$/,
                        message: '请输入正确的护照号码'
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
                      },
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
    $('#bling').submit(function(){
        var yz_num = $('input[name="yz_num"]').val();

        if(yz_num=="0"||role_type=="0"){
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
    //发送手机验证码 老师
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
    $('#bling2').submit(function(){
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
    //发送手机验证码 学生
    $('#getVerificationButtonTwo').click(function(){
        var moblie = $('#phone_number').val();
        var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if(reg.test(moblie)){
            duMiao($('#getVerificationButtonTwo'));
            $('#VerificationTextTwo').attr('disabled',false);
            $('#getVerificationButtonTwo').next('input').val(0);
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

    //验证 短信验证码 老师
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

    //验证 短信验证码 学生
    $('#VerificationTextTwo').blur(function(){
        var moblie = $('#phone_number').val();
        var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if(reg.test(moblie)) {
            var obj = {mobile: $('#phone_number').val(), code: $('#VerificationTextTwo').val()};
            $.ajax("{{ url('/api/1.0/public/msc/user/reg-check-mobile-verfiy') }}", {
                type: 'get',
                data: obj,
                success: function (data, textStatus, jqXHR) {
                    if (data.code == 1) {
                        $('#VerificationTextTwo').attr('disabled', 'disabled');
                        $('#getVerificationButtonTwo').next('input').val(1);
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