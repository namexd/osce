@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
    .img_box{
        width:197px;
        height:251px;
        margin: auto;
    }
    .img_box li img{
        width: 197px;
        height: 251px;
    }
</style>
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
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.invigilator.postCodeUnique')}}',//验证地址
                                message: '该教师编号已经存在',//提示消息
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                /*自定义提交数据，默认值提交当前input value*/
                                data: function(validator) {
                                    $(".btn-primary").css({"background":"#16beb0","border":"1px solid #16beb0","color":"#fff","opacity":"1"});

                                    return {
                                        id:  '{{$_GET['id']}}',
                                        code: $('[name="whateverNameAttributeInYourForm"]').val()
                                    }
                                }
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
                                regexp: /^1[3|7|5|8]{1}[0-9]{9}$/,
                                message: '请输入正确的手机号码'
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.invigilator.postSelectTeacher')}}',//验证地址
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                message: '号码已经存在',//提示消息
                                data: function(validator) {
                                    return {
                                        id: '{{$item->id}}',
                                        mobile: $('#mobile').val()
                                    }
                                }
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
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.invigilator.postSelectTeacher')}}',//验证地址
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                message: '身份证号已存在',//提示消息
                                /*自定义提交数据，默认值提交当前input value*/
                                data: function(validator) {
                                    $(".btn-primary").css({"background":"#16beb0","border":"1px solid #16beb0","color":"#fff","opacity":"1"});

                                    return {
                                        code: $('[name="whateverNameAttributeInYourForm"]').val()
                                    }
                                }
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
                    }
                }
            });
            $("#images_upload").change(function(){
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
                            $('#images_upload').before('<li><img style="width:197px;height:250px;" src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
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

            /**
             * 删除
             * @author mao
             * @version 1.0
             * @date    2016-02-19
             */
            $(".img_box").delegate(".del_img","click",function(){
                $(this).parent("li").remove();
                $('#images_upload').attr("class","images_upload");
            });
        })

    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>编辑监巡考老师</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.invigilator.postEditInvigilator')}}">
                        <div class="col-md-3 col-sm-3">
                            <ul class="img_box">
                                <li>
                                    <img src="{{$item->userInfo->avatar}}"/>
                                    <input type="hidden" value="{{$item->userInfo->avatar}}" name="images_path[]">
                                    <i class="fa fa-remove font16 del_img"></i>
                                </li>
                                <span class="images_upload1" id="images_upload">
                                    <input type="file" name="images" id="file0"/>选择图片
                                </span>
                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-9">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name" name="name" value="{{$item->name}}">
                                    <input type="hidden" required class="form-control" id="id" name="id" value="{{$item->id}}">
                                    <input type="hidden" required class="form-control" id="is_sp" name="is_sp" value="2">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">性别</label>
                                <div class="col-sm-10">
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="1" {{$item->userInfo->gender=='男'? 'selected="selected"':''}}>男</option>
                                        <option value="2" {{$item->userInfo->gender=='女'? 'selected="selected"':''}}>女</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">教师编号</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="code" id="code" value="{{$item->code}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">身份证号</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="idcard" id="idcard" value="{{$item->userInfo->idcard}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">联系电话</label>
                                <div class="col-sm-10">
                                    <input type="text" ng-model="mobile" id="mobile" class="form-control" name="mobile" value="{{$item->userInfo->mobile}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">老师类型</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="type">
                                        <option value="1" {{$item->type==1? 'selected="selected"':''}}>监考老师</option>
                                        <option value="3" {{$item->type==3? 'selected="selected"':''}}>巡考老师</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">电子邮箱</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="email" id="email" value="{{$item->userInfo->email}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">备注</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="description" id="note" value="{{$item->description}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="{{route("osce.admin.invigilator.getInvigilatorList")}}">取消</a>
                                    {{--<button class="btn btn-white" type="submit">取消</button>--}}
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

@stop{{-- 内容主体区域 --}}