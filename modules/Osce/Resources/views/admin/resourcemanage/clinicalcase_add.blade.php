@extends('osce::admin.layouts.admin_index')
@section('only_css')
    
@stop

@section('only_js')
    <script src="{{asset('osce/plugins/js/plugins/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('osce/plugins/js/plugins/messages_zh.min.js')}}"></script>
    <script>
        $("#select_Category").change( function(){
            if($(this).val()=="Classroom") {
                $(".select-floor").show();
            }else{
                $(".select-floor").hide();
            }
        })

        var uploader = WebUploader.create({
            // 选完文件后，是否自动上传。
            auto: false,

            // swf文件路径
            swf: BASE_URL + '/js/Uploader.swf',

            // 文件接收服务端。
            server: 'http://webuploader.duapp.com/server/fileupload.php',

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#filePicker',

            // 只允许选择图片文件。
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,bmp,png',
                mimeTypes: 'image/*'
            }
        });

        $("#sourceForm").validate({
            rules: {
                name: "required",
                select_Category: "required",

                code: {required: true, minlength: 5},
                confirm_password: {required: true, minlength: 5, equalTo: "#password"},
                email: {required: true, email: true},
                topic: {required: "#newsletter:checked", minlength: 2},
                agree: "required"
            },
            messages: {
                firstname: a + "请输入你的姓",
                lastname: a + "请输入您的名字",
                username: {required: a + "请输入您的用户名", minlength: a + "用户名必须两个字符以上"},
                password: {required: a + "请输入您的密码", minlength: a + "密码必须5个字符以上"},
                confirm_password: {required: a + "请再次输入密码", minlength: a + "密码必须5个字符以上", equalTo: a + "两次输入的密码不一致"},
                email: a + "请输入您的E-mail",
                agree: {required: a + "必须同意协议后才能注册", element: "#agree-error"}
            }
        });
        $("#username").focus(function () {
            var c = $("#firstname").val();
            var b = $("#lastname").val();
            if (c && b && !this.value) {
                this.value = c + "." + b
            }
        })

  </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>病例新增</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm">

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">病例名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" value="">
                            </div>
                        </div>




                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="location" id="location" class="form-control" name="description">
                            </div>

                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-white" type="submit">取消</button>
                                <button class="btn btn-primary" type="submit">保&nbsp;&nbsp;&nbsp;存</button>

                            </div>
                        </div>


                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}