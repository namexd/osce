/**
 * Created by Administrator on 2016/3/7 zengjie
 * 题库管理
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "subject_check_tag":subject_check_tag();break;//考核标签
        case "subject_manage":subject_manage();break;//题库管理
        case "subject_manage_add":subject_manage_add();break;//题库管理新增
        case "subject_papers":subject_papers();break;//试卷管理
    }
});

//考核标签
function subject_check_tag(){
    //新增
    //显示
    $("#add").click(function(){
        $("#editForm").hide();
        $("#addForm").show();
    });
    //验证
    $("#addForm").bootstrapValidator({
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
                        message: '标签名称不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 1,
                        max: 10,
                        message: '标签名称长度必须在1到10之间'
                    },
                    threshold :  4 ,
                    remote:{
                        url: '/osce/admin/exam/exam-verify',//验证地址
                        message: '标签名称已存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        dataType: 'json'//请求方式
                    }
                }
            },
            describe: {/*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '描述不能为空'
                    }
                }
            }
        }
    });
    //编辑
    //显示
    $(".edit").click(function(){
        var $editId = $(this).attr("dataId");
        $("#editForm").show();
        $("#addForm").hide();
        $.ajax({
            url:'/osce/admin/exam/exam-getlabel?id='+$editId,
            type:"get",
            cache:false,
            dataType:"json",
            success:function(res){
                var res = res.data.data.examQuestionDetail;
                $(".edit_name").val(res.name);
                $(".edit_type").val(res.label_type_id);
                $(".edit_des").val(res.describe);
                $(".edit_id").val(res.id);
            }
        });
        edit($editId);
    });
    //验证
    function edit(editId){
        $("#editForm").bootstrapValidator({
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
                            message: '标签名称不能为空'
                        },
                        stringLength: {/*长度提示*/
                            min: 1,
                            max: 10,
                            message: '标签名称长度必须在1到10之间'
                        },
                        remote:{
                            url: '/osce/admin/exam/exam-verify',//验证地址
                            message: '标签名称已存在',//提示消息
                            delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                            type: 'POST',//请求方式
                            data: function (validator) {
                                return{
                                    id:editId
                                }
                            }
                        }
                    }
                },
                describe: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '描述不能为空'
                        }
                    }
                }
            }
        });
    }
    //删除
    $(".delete").click(function(){
        var id = $(this).attr("dataId");
        var delUrl = pars.delUrl;
        layer.confirm('是否确定删除该标签？',{
            title:'删除',
            btn: ['确定','取消']
        },function(){
            $.ajax({
                url:delUrl+'?id='+id,
                type:"get",
                cache:false,
                success:function(res){
                    if(res.code == "1"){
                        location.href = location.href;
                    }else{
                        layer.msg('删除失败',{'skin':'msg-error',icon:1})
                    }
                }
            })
        })
    });
}
//题库管理
function subject_manage(){
    //删除
    $(".delete").click(function(){
        var id = $(this).attr("dataId");
        var url = pars.delUrl;
        layer.confirm('是否确定删除该试题？',{
            title:'删除',
            btn: ['确定','取消']
        },function(){
            $.ajax({
                url:url+'?id='+id,
                type:"post",
                dataType:"json",
                cache:false,
                success:function(res){
                    if(res == true){
                        location.href = location.href;
                    }else{
                        layer.msg('删除失败',{'skin':'msg-error',icon:1})
                    }
                }
            })
        })
    });
    //新增
    $("#add").click(function(){
        var add = pars.add;
        location.href=add;
    });
    //编辑
    $(".edit").click(function () {
        var edit = pars.edit;
        location.href=edit;
    })
}
//题库管理新增
function subject_manage_add(){
    $(function(){
        //调select2插件
        $(".tag").select2({});
        //新增选项
        var strToInt = {
            A:'B',
            B:'C',
            C:'D',
            D:'E',
            E:'F',
            F:'G',
            G:'H',
            H:'I',
            I:'J'
        };
        $("#addChose").click(function(){
            $('#sourceForm').data('bootstrapValidator').destroy();
            var old= $("tbody").find("tr").last().children().first().html();
            if(old == "J"){
                layer.alert("最多只能有10个选项！");
            }else{
                $("tbody").find("tr").last().before().children().last().empty();
                $("tbody").append('<tr>' +
                    '<td>'+strToInt[old]+'</td>' +
                    '<input type="hidden" name="examQuestionItemName[]" value="'+strToInt[old]+'"/>' +
                    '<td>' +
                    '<div class="form-group">' +
                    '<div class="col-sm-12">' +
                    '<input type="text" class="form-control" name="content[]"/>' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '<td>' +
                    '<a href="javascript:void(0)" class="delete">' +
                    '<span class="read state2 detail">' +
                    '<i class="fa fa-trash-o fa-2x"></i>' +
                    '</span>' +
                    '</a>' +
                    '</td>' +
                    '</tr>');
                $("#checkbox_div").append('<label class="check_label checkbox_input check_top">' +
                    '<div class="check_icon check_other"></div>' +
                    '<input type="checkbox" name="answer[]" value="'+strToInt[old]+'"/>' +
                    '<span class="check_name">'+strToInt[old]+'</span>' +
                    '</label>');
            }

            if($("#subjectType").val()==1){
                oneValidator();
            }else if($("#subjectType").val()==2){
                moreValidator();
            }else if($("#subjectType").val()==3){
                noSureValidator();
            }else{
                chooseValidator();
            }
        });
        //删除选项
        $("tbody").delegate(".delete","click",function(){
            $('#sourceForm').data('bootstrapValidator').destroy();
            $(this).parent().parent().remove();
            $("#checkbox_div").children("label").last().remove();
            if($(".table tbody").children().size() == "2"){

            }else{
                $(".table tbody").children().last("tr").children().last('td').append('<a href="javascript:void(0)" class="delete">' +
                    '<span class="read state2 detail">' +
                    '<i class="fa fa-trash-o fa-2x"></i>' +
                    '</span>' +
                    '</a>');
            }
            if($("#subjectType").val()==1){
                oneValidator();
            }else if($("#subjectType").val()==2){
                moreValidator();
            }else if($("#subjectType").val()==3){
                noSureValidator();
            }else{
                chooseValidator();
            }
        });
        //选项选中
        $("#checkbox_div").delegate(".checkbox_input","change",function(){
            if($(this).find("input").is(':checked')){
                $(this).find(".check_icon ").addClass("check");
            }else{
                $(this).find(".check_icon").removeClass("check");
            }
        });
        $("#radiobox_div").delegate(".radio_label","change",function(){
            if($(this).children("input").checked=="true"){
                $(this).children(".radio_icon").removeClass("check");
            }else{
                $(".radio_icon").removeClass("check");
                $(this).children(".radio_icon").addClass("check");
            }
        });
        if($("#subjectType option:selected").val()==1){
            oneValidator();
        }else if($("#subjectType option:selected").val()==2){
            moreValidator();
        }else if($("#subjectType option:selected").val()==3){
            noSureValidator();
        }else{
            chooseValidator();
        }
        //单选验证
        function oneValidator(){
            $("#sourceForm").bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    name : {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '题目不能为空'
                            }
                        }
                    },
                    'content[]' : {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '选项内容不能为空'
                            }
                        }
                    },
                    'answer[]': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '正确答案不能为空'
                            },
                            choice: {/*长度提示*/
                                message: '只能选中一个正确答案',
                                min:1,
                                max:1
                            }
                        }
                    }
                    //'tag[]': {/*键名username和input name值对应*/
                    //    message: 'The username is not valid',
                    //    validators: {
                    //        callback: {
                    //            message: '至少选择一个标签',
                    //            callback:function(){
                    //                var $tagVal = $(".tag option:selected");
                    //                if($tagVal&&$tagVal.length>0){
                    //                    return true;
                    //                }
                    //                else{
                    //                    return false;
                    //                }
                    //            }
                    //        }
                    //    }
                    //}
                }
            })
        }
        //不定项验证
        function noSureValidator(){
            $("#sourceForm").bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    name : {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '题目不能为空'
                            }
                        }
                    },
                    'content[]' : {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '选项内容不能为空'
                            }
                        }
                    },
                    'answer[]': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '正确答案不能为空'
                            }
                        }
                    }
                    //'tag[]': {/*键名username和input name值对应*/
                    //    message: 'The username is not valid',
                    //    validators: {
                    //        callback: {
                    //            message: '至少选择一个标签',
                    //            callback:function(){
                    //                var $tagVal = $(".tag option:selected");
                    //                if($tagVal&&$tagVal.length>0){
                    //                    return true;
                    //                }
                    //                else{
                    //                    return false;
                    //                }
                    //            }
                    //        }
                    //    }
                    //}
                }
            });
        }
        //多选验证
        function moreValidator(){
            $("#sourceForm").bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    name : {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '题目不能为空'
                            }
                        }
                    },
                    'content[]' : {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '选项内容不能为空'
                            }
                        }
                    },
                    'answer[]': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '正确答案不能为空'
                            },
                            choice: {/*长度提示*/
                                message: '至少选中两个正确答案',
                                min:2
                            }
                        }
                    }
                    //'tag[]': {/*键名username和input name值对应*/
                    //    message: 'The username is not valid',
                    //    validators: {
                    //        callback: {
                    //            message: '至少选择一个标签',
                    //            callback:function(){
                    //                var $tagVal = $(".tag option:selected");
                    //                if($tagVal&&$tagVal.length>0){
                    //                    return true;
                    //                }
                    //                else{
                    //                    return false;
                    //                }
                    //            }
                    //        }
                    //    }
                    //}
                }
            })
        }
        //判断验证
        function chooseValidator(){
            $("#sourceForm").bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    name : {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '题目不能为空'
                            }
                        }
                    },
                    'judge': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '正确答案不能为空'
                            }
                        }
                    }
                    //'tag[]': {/*键名username和input name值对应*/
                    //    validators: {
                    //        //notEmpty: {/*非空提示*/
                    //        //    message: '至少选择一个标签'
                    //        //}
                    //        callback: {
                    //            message: '至少选择一个标签',
                    //            callback:function(){
                    //                var $tagVal = $(".tag option:selected");
                    //                if($tagVal&&$tagVal.length>0){
                    //                    return true;
                    //                }
                    //                else{
                    //                    return false;
                    //                }
                    //            }
                    //        }
                    //    }
                    //}
                }
            });
        }


        $("#subjectType").change(function(){
            var type=$(this).val();
            if(type==1){
                $(".chooseLine").show();
                $(".choose").show();
                $("#radiobox_div").hide();
                $("#checkbox_div").show();
                $('#sourceForm').data('bootstrapValidator').destroy();
                oneValidator();
            }else if(type==2){
                $(".chooseLine").show();
                $(".choose").show();
                $("#radiobox_div").hide();
                $("#checkbox_div").show();
                $('#sourceForm').data('bootstrapValidator').destroy();
                moreValidator();
            }else if(type==3){
                $(".chooseLine").show();
                $(".choose").show();
                $("#radiobox_div").hide();
                $("#checkbox_div").show();
                $('#sourceForm').data('bootstrapValidator').destroy();
                noSureValidator();
            }else{
                $(".chooseLine").hide();
                $(".choose").hide();
                $("#radiobox_div").show();
                $("#checkbox_div").hide();
                $('#sourceForm').data('bootstrapValidator').destroy();
                chooseValidator();
            }
        });
        //图片上传
        $("#file").change(function(){
            var url = pars.imgUrl;
            //获取图片对象
            var files=document.getElementById("picFile").files;
            //图片大小
            var kb=Math.floor(files[0].size/1024);
            //图片类型
            var type = files[0].name.split(".")[1];
            if(type != "jpg" && type != "png" && type != "jpeg"){
                layer.alert('图片格式不正确!');
                return false;
            }
            if(kb>2048){
                layer.alert('图片大小不得超过2M!');
                $("#picFile").val('');
                return false;
            }
            if($(".picBox p").length > 4){
                layer.alert('最多只能上传5张图片!');
                return false;
            }
            $.ajaxFileUpload
            ({
                url:url,
                secureuri:false,//
                fileElementId:'picFile',//必须要是 input file标签 ID
                dataType: 'json',
                type:"POST",
                success: function (data)
                {
                    if(data.data.status){
                        var path=data.data.path;//图片存放路径
                        var picName = data.data.name;//图片名称
                        $(".picBox").append('<p><input type="hidden" name="image[]" value="'+path+'"/><input type="hidden" name="imageName[]" value="'+picName+'"/>"'+picName+'"<i class="fa fa-2x fa-remove clo6"></i></p>');
                    }else{
                        layer.alert('图片格式错误!');
                    }
                }
            });
        });
        //删除图片选项
        $(".picBox").on("click",".fa-remove",function(){
            $(this).parent().remove();
        });
        //验证标签至少选择一个
        $("#sure").click(function(){
            var $tagVal = $(".tag option:selected");
            if($tagVal&&$tagVal.length>0){
                    return true;
                }
                else{
                    layer.alert("至少选择一个标签！");
                    return false;
                }
        });
    });
}
//试卷管理
function subject_papers(){
    $(".fa-trash-o").click(function(){
        var url = pars.delUrl;
        var id = $(this).attr("data");
        layer.confirm('是否确定删除该试卷？',{
            title:'删除',
            btn: ['确定','取消']
        },function(){
            location.href=url+"?id="+id;
        })
    });
}





