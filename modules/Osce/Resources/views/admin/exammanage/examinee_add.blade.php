@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style type="text/css">
        .has-error .form-control{border-color: #ed5565!important;}
        .code_add,.code_del{position:absolute;right:15px;top:0;}
        .add_box .glyphicon-remove,.add_box .glyphicon-ok{display:none!important;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>
    <script>
        $(function() {
            $(".img_box").delegate(".del_img","click",function(){
                $(this).parent("li").remove();
            });
            /*{}{
             * 下面是进行插件初始化
             * 你只需传入相应的键值对
             * */
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
                                message: '用户名不能为空'
                            }
                        }
                    },
                    examinee_id: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '学号不能为空'
                            },
                            regexp:{
                                regexp: /^\d+$/,
                                message: '请输入正确的学号'
                            }
                        }
                    },
                    idcard: {
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
                    tell: {
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
                    email:{
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '邮箱不能为空'
                            },
                            regexp: {
                                regexp: /^[a-z\d]+(\.[a-z\d]+)*@([\da-z](-[\da-z])?)+(\.{1,2}[a-z]+)+$/,
                                message: '请输入正确的邮箱'
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
                        $.alert({
                            title: '提示：',
                            content: '通讯失败!',
                            confirmButton: '确定',
                            confirm: function(){
                            }
                        });
                    }
                });
            }) ;
        });
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
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'examinee_add','preUrl':'{{route('osce.admin.exam.getExamineeManage')}}?id={{$id}}'}"/>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增考生</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postAddExaminee')}}">
                        <input type="hidden" name="exam_id" value="{{$id}}" />
                        <input type="hidden" name="resources_type" id="resources_type" value="TOOLS" />
                        <div class="col-md-3 col-sm-3">
                            <ul class="img_box">
	                    		<span class="images_upload">
	                        		<input type="file" name="images" id="file0"/>
	                        	</span>
                            </ul>
                        </div>
                        <div class="col-md-9 col-sm-9">
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">姓名:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" />
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <input type="hidden" name="" id="cate_id" value="-1" />
                                <label class="col-sm-2 control-label">性别:</label>
                                <div class="col-sm-10 select_code">
                                    <select id="gender"   class="form-control m-b" name="gender">
                                        <option value="1">男</option>
                                        <option value="2">女</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">学号:</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="code" name="code" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >身份证号:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="idcard" name="idcard"  class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">联系电话:</label>

                                <div class="col-sm-10">
                                    <input type="text"  id="mobile" name="mobile" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">电子邮箱:</label>
                                <div class="col-sm-10">
                                    <input type="text" id="email" name="email" class="form-control">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">备注:</label>
                                <div class="col-sm-10">
                                    <textarea name="note" id="" cols="" rows="" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <button class="btn btn-white return-pre" type="button">取消</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}