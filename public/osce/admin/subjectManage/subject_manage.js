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
        //case "subject_manage_edit":subject_manage_edit();break;//题库管理编辑
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
            url:'/osce/admin/exam/exam-getLabel?id='+$editId,
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
                }
            }
        });
    }
    //删除
    $(".delete").click(function(){
        var id = $(this).attr("dataId");
        layer.confirm('是否确定删除该标签？',{
            title:'删除',
            btn: ['确定','取消']
        },function(){
            $.ajax({
                url:'/osce/admin/exam/exam-deleteLabel?id='+id,
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
                    },
                    'tag[]': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '标签不能为空'
                            }
                        }
                    }
                }
            })
        }
        oneValidator();
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
                    },
                    'tag[]': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '标签不能为空'
                            }
                        }
                    }
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
                    },
                    'tag[]': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '标签不能为空'
                            }
                        }
                    }
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
                    },
                    'tag[]': {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '标签不能为空'
                            }
                        }
                    }
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
        })

    });
}






