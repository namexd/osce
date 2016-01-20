@extends('msc::wechat.layouts.user')
@section('only_head_css')
    <link rel="stylesheet" href="{{asset('msc/common/css/bootstrapValidator.css')}}">

    {{--select输入效果--}}
    <link href="{{asset('msc/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
    <style rel="stylesheet">
        .select2-container--default .select2-selection--single{ height: 36px;
            border: 1px solid #ccc;}
        .select2-container--default .select2-selection--single .select2-selection__arrow{top:3px;}
        .has-feedback label~.form-control-feedback {top: 2px;}
        .form-control{
            color: #333!important;
        }
        .select_indent{text-indent: 65px}
    </style>
@stop

@section('only_head_js')
    <script type="text/javascript" src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('msc/wechat/user/js/commons.js')}}"></script>
    <script src="{{asset('msc/wechat/user/js/register.js')}}"></script>

@stop
    
@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        用户注册
        <a class="right header_btn" href="javascript:void(0)"></a>
    </div>
<div class="form-select">
    <div class="teacher checked">
       老师
    </div>
    <div class="student">
        学生
    </div>

</div>

<script type="text/javascript">
    $(function(){

        var url = "{{ route('msc.Dept.PidGetDept') }}?pid=";
        console.log(url+0);

        $(".radio_label").click(function(){
            if($(this).children("input").checked=="true"){
                $(this).children(".radio_icon").removeClass("check");
            }else{
                $(".radio_icon").removeClass("check");
                $(this).children(".radio_icon").addClass("check");
            }
        });
    })
    /*
    * " => ""
     "Category" => ""
     "
     "passport" => ""
    * */
</script>
<div class="clear"></div>
<div class="container" id="container">
    <div >
        {{-- RegTeacherOp --}}
        <form name="form" method="post" id="sourceForm" action="{{ url('/msc/wechat/user/reg-teacher-op') }}" id="frmTeacher">
            <div class="form-group">
                <label for="name">姓 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名<span>*</span></label>
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
            <div class="form-group">
                <label for="code">胸牌工号<span>*</span></label>
                <input type="text"  id="code" name="code" class="form-control" />
            </div>
            <div class="form-group">
                <label for="teacher_dept">职 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;称<span>*</span></label>
                <select name="professionalTitle"  class="form-control normal_select select_indent">
                    <option value="">请选择职称</option>
                    @if(!empty($ProfessionalTitleList))
                        @foreach($ProfessionalTitleList as $v)
                            <option value="{{ $v['id'] }}">{{ $v['name'] }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group">
                <label for="teacher_dept">科 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;室<span>*</span></label>
                <select name="department" id="department1" class="form-control normal_select select_indent">
                    <option value="">请选择科室</option>
                </select>
                <select name="department" id="department2" class="form-control normal_select select_indent" style="display: none;margin: 10px 0 5px;">
                    <option value="">请选择科室</option>
                </select>
                <select name="department" id="department3" class="form-control normal_select select_indent" style="display: none;margin: 5px 0;">
                    <option value="">请选择科室</option>
                </select>
                <input type="hidden" name="professional" value="" id="input_hidden">
            </div>

            <div class="form-group">
                <label for="mobile">手机号码<span>*</span></label>
                <input type="number" class="form-control" id="mobile" name="mobile" />
            </div>
            <div class="form-group"style="width:60%;float: left;">
                <input type="text"  class="form-control ipt_code" id="VerificationText" placeholder="请输入验证码"/>
            </div>
            <div class="form-group" style="width:35%;float:right;">
                <input type="button" class="form-control ipt_huoqu" id="getVerificationButtonOne"  value="获取验证码" />
                <input type="hidden" name="yz_num" value="0">
            </div>
            <div class="clear"></div>

            <div class="form-group">
                <input type="password" name="password" class="form-control ipt_txt" placeholder="请输入密码"/>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control ipt_txt" placeholder="请输入再次确认密码"/>
            </div>
            <!--<span class="error" ng-show="form.$dirty && form.name.$invalid">填写格式错误</span>-->
            <input class="btn" type="submit" id="#bling" value="提交审核" />
        </form>

    </div>
    <div style=" display:none;">
        <form name="form2" method="post" id="sourceForm-student" action="{{ url('/msc/wechat/user/reg-student-op') }}" >
            <div class="form-group">
                <label for="name2">姓&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名<span>*</span></label>
                <input  type="text" class="form-control" name="name" id="name2"/>
            </div>
            <div class="form-group">
                <div class="radio_box">
                    <label class="left radio_label" for="radio_3">
                        <div class="left radio_icon"></div>
                        <b class="left">男</b>
                        <input type="radio" id="radio_3" name="sex2" value="1"/>
                    </label>
                    <label class="left radio_label" for="radio_4" style="margin-left:50px">
                        <div class="left radio_icon"></div>
                        <b class="left">女</b>
                        <input type="radio" id="radio_4" name="sex2" value="2"/>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="student_id">学号/胸牌<span>*</span></label>
                <input type="text"  id="student_id" name="code" name="student_id" class="form-control" />
            </div>
            <div class="form-group">
                <label for="Grade">年级</label>
                <select name="grade" id="Grade" class="form-control normal_select">
                    <option value="">选择年级</option>
                    @foreach($GreadeList as $val)
                        <option value="{{ $val['id'] }}" >{{ $val['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="Category">类别<span>*</span></label>
                <select  id="class" class="form-control normal_select" name="student_type">
                    <option value="">选择类别</option>
                    @foreach($StudentTypeList as $val)
                        <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                    @endforeach
                </select>

            </div>
            <div class="form-group">

                <label for="work_id">专业<span>*</span></label>

                <select name="professional" id="Professional" style="width: 100%"   class="form-control normal_select">
                    <option value="">选择专业</option>
                    @foreach($StudentProfessionalList as $v)
                        <option value="{{ $v['id'] }}">{{ $v['name'] }}</option>
                    @endforeach
                </select>

            </div>

            <div class="form-group">

                <label for="phone_number">手机号码<span>*</span></label>
                <input type="number" id="phone_number"  class="form-control" name="mobile"/>


            </div>


            <div class="form-group"style="width:60%;float: left;">
                <input type="text"   class="form-control ipt_code" id="VerificationTextTwo" placeholder="请输入验证码"/>
            </div>
            <div class="form-group" style="width:35%;float:right;">
                <input type="button" class="form-control ipt_huoqu"  id="getVerificationButtonTwo"  value="获取验证码"/>
                <input type="hidden" name="yz_num" value="0">
            </div>

           <div class="form-group card-list" style="width:35%;float: left;">
               <select name="idcard_type" id="card-list">
                   <option value="0">证件类型</option>
                   <option value="1">身份证</option>
                   <option value="2">护照</option>
               </select>
               <label for="ipt_zjh"><span>*</span></label>
           </div>

            <div class="form-group card-list no_zjh" style="width:65%;float:right;" >

                <input type="text" class="form-control" style="padding-left:2px;" disabled="disabled"  placeholder="请选择证件类型" />
            </div>
            <div class="form-group card-list ipt_zjh" style="width:65%;float:right;display: none">

                <input style="padding-left:2px;"  class="form-control " name="idcard"   placeholder="请输入身份证号码" />
            </div>
            <div class="form-group card-list hz_zjh" style="width:65%;float:right;display: none">
                <input style="padding-left:2px;"  class="form-control " name="idcard2"   placeholder="请输入护照编号" />
            </div>
            <div class="clear"></div>
            <div class="form-group">
                <input type="password" name="password" class="form-control ipt_txt" placeholder="请输入密码"/>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" class="form-control ipt_txt" placeholder="请输入再次确认密码"/>
            </div>
            <input class="btn" type="submit" id="#bling2" value="提交审核" />

        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
        initcard();//表单切换
        var url="{{ route('msc.Dept.PidGetDept') }}?pid=";
        console.log(url+0);
        $.ajax({
            type:"get",
            url:url+0,
            dataType:"json",
            cache:false,
            success:function(result){
                $(result.data.total).each(function(){
                    $("#department1").append(
                            " <option value="+this.id+">"+this.name+"</option>"
                    )
                })
            }
        });
        $("#department1").change(function(){
            $("#department2").empty().append(
                    "<option value=''>请选择科室</option>"
            );
            var $thisId=$(this).val();
            $.ajax({
                type:"get",
                url:url+$thisId,
                dataType:"json",
                cache:false,
                async:false,
                success:function(result){
                    $(result.data.total).each(function(){
                        $("#department2").append(
                                " <option value="+this.id+">"+this.name+"</option>"
                        )
                    })
                }
            });
            if($("#department2").children().length == 1){
                $("#department2").hide();
                $("#input_hidden").attr("value",$thisId);
            }else{
                $("#department2").show();
            }
        });

        $("#department2").change(function(){
            $("#department3").empty().append(
                    "<option value=''>请选择科室</option>"
            );
            var $thisId=$(this).val();
            $.ajax({
                type:"get",
                url:url+$thisId,
                dataType:"json",
                cache:false,
                async:false,
                success:function(result){
                    $(result.data.total).each(function(){
                        $("#department3").append(
                                " <option value="+this.id+">"+this.name+"</option>"
                        )
                    })
                }
            });
            if($("#department3").children().length == 1){
                $("#department3").hide();
                $("#input_hidden").attr("value",$thisId);
            }else{
                $("#department3").show();
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
            gender: {
                validators: {
                    notEmpty: {
                        message: '性别不能为空'
                    }
                }
            },
            professionalTitle: {
                validators: {
                    notEmpty: {
                        message: '职称不能为空'
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
            department: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '科室不能为空'
                    }
                }
            },
            professional: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '不能为空'
                    }
                }
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
            confirm_password: {
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
                         minlength:12,
                         message: '密码必须6个字符以上'
                    },

               }
               
            },
            confirm_password: {
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

</script>
@stop