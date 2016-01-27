@extends('osce::admin.layouts.admin_index')
@section('only_css')
    
@stop

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script>
        $(function(){

            $('#sourceForm').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    name: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '名称不能为空'
                            },
                            stringLength: {
                                max:20,
                                message: '名称字数不超过20个'
                            }
                        }
                    },
                    code: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '教师编号不能为空'
                            },
                            regexp: {
                                regexp: /^\w+$/,
                                message: '教师编号应该由数字，英文或下划线组成'
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
                                regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
                                message: '请输入正确的手机号码'
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.invigilator.postSelectTeacher')}}',//验证地址
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                message: '号码已经存在'//提示消息
                            }
                        }
                    },
                    idcard: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '身份证号不能为空'
                            },
                            regexp: {
                                regexp: /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
                                message: '请输入正确的身份证号'
                            }
                        }
                    },
                    email: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '邮箱不能为空'
                            },
                            regexp: {
                                regexp: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ,
                                message: '请输入正确的邮箱'
                            }
                        }
                    },
                    images: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '请上传图片'
                            }
                        }
                    }
                }
            });
            $(".images_upload").change(function(){
                $.ajaxFileUpload
                ({

                    url:'{{ url('commom/upload-image') }}',
                    secureuri:false,//
                    fileElementId:'file0',//必须要是 input file标签 ID
                    dataType: 'json',//
                    success: function (data, status)
                    {
                        if(data.code){
                            var href=data.data.path;
                            $('.img_box').find('li').remove();
                            $('.images_upload').before('<li><img src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
                        }
                    },
                    error: function (data, status, e)
                    {
                        layer.msg("通讯失败");
                    }
                });
            }) ;

            //建立一個可存取到該file的url
            var url='';
            function getObjectURL(file) {
                if (window.createObjectURL!=undefined) { // basic
                    url = window.createObjectURL(file) ;
                } else if (window.URL!=undefined) { // mozilla(firefox)
                    url = window.URL.createObjectURL(file) ;
                } else if (window.webkitURL!=undefined) { // webkit or chrome
                    url = window.webkitURL.createObjectURL(file) ;
                }
                return url;
            }
            $(".img_box").delegate(".del_img","click",function(){
                $(this).parent("li").remove();
            });
            $(".image-box").find(".help-block").css({"color":"#a94442","text-align":"center","width":"280px"});//图片未选择提示语言颜色
        })

    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增SP老师</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.invigilator.postAddSpInvigilator')}}">

                    <div class="col-md-3 col-sm-3 image-box">
                        <ul class="img_box">
                            <span class="images_upload">
                                <input type="file" name="images" id="file0"/>
                                图片大小为280X180
                            </span>
                        </ul>
                    </div>
                    <div class="col-md-9 col-sm-9">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">姓名</label>
                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="name">
                                <input type="hidden" required class="form-control" id="type" name="type" value="2">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">性别</label>
                            <div class="col-sm-10">
                                <select name="gender" id="gender" class="form-control">
                                    <option value="1">男</option>
                                    <option value="2">女</option>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">教师编号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="code" id="code">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">身份证号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="idcard" id="idcard">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">联系电话</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="mobile" id="mobile">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">电子邮箱</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="email" id="email">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="select-floor">
                                <label class="col-sm-2 control-label">病例</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="case_id">
                                        @forelse($list as $option)
                                            <option value="{{$option->id}}">{{$option->name}}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="description" id="note">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit" id="save">保存</button>
                                <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
{{--								<a class="btn btn-white" href="{{route('osce.admin.invigilator.getSpInvigilatorList')}}">取消</a>--}}
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}