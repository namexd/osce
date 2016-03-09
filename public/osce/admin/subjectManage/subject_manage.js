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
}




