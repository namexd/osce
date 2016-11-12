/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "rolemanage_detail":rolemanage_detail();break; //rolemanage_detailҳ��
        case "rolemanage":rolemanage();break; //rolemanageҳ��
    }

});

function rolemanage_detail(){
    /**
     *角色权限管理（设置权限）checkbox选择处理
     *曾洁
     *QQ：283020075
     *2015-12-15
	update：zengjie（2015-12-22 10:56） （最近更新/更改 作者及时间）     **/
    $(function(){
        var $check_label=$(".check_label");
        var $btn_padding=$(".btn_padding");
        $check_label.click(function(){
            var hidevalue;
            var thisvalue=$(this).attr("hidevalue");
            $(this).siblings(".ibox-content").children("button").each(function(){
                hidevalue= $(this).attr("hidevalue");
                if($(this).next("input").size()=="0"){
                    $(this).after("<input type='hidden' name='permission_id[]' value=''>");
                    $(this).next("input").attr("value",hidevalue);
                }
            });
            if($(this).children(".check_icon").hasClass("check")){
                $(this).children(".check_icon").removeClass("check");
                $(this).children(".check_icon").next("input").remove();
                $(this).siblings(".ibox-content").children("button").attr("checked",false);
                $(this).siblings(".ibox-content").children("button").removeClass("btn_focus");
                $(this).siblings(".ibox-content").children("button").addClass("btn-default2");
                $(this).siblings(".ibox-content").children("input").remove();
                return false;
            }else{
                $(this).children(".check_icon").addClass("check");
                $(this).children(".check_icon").after("<input type='hidden' name='permission_id[]' value=''>");
                $(this).children(".check_icon").next("input").attr("value",thisvalue);
                $(this).siblings(".ibox-content").children("button").attr("checked",true);
                $(this).siblings(".ibox-content").children("button").addClass("btn_focus");
                $(this).siblings(".ibox-content").children("button").removeClass("btn-default2");
                return false;
            }
        });
        $btn_padding.click(function(){
            var hidevalue= $(this).attr("hidevalue");
            var thisvalue=$(this).parent(".ibox-content").siblings(".check_label").attr("hidevalue");
            if($(this).hasClass("btn_focus")){
                $(this).removeClass("btn_focus");
                $(this).addClass("btn-default2");
                $(this).attr("checked",false);
                $(this).next("input").remove();
                if($(this).parent(".ibox-content").children("button").hasClass("btn_focus")){
                    return false;
                }else{
                    $(this).parent(".ibox-content").siblings(".check_label").children(".check_icon").removeClass("check").next("input").remove();
                }
            }else{
                $(this).addClass("btn_focus");
                $(this).removeClass("btn-default2");
                $(this).attr("checked",true);
                $(this).after("<input type='hidden' name='permission_id[]' value=''>");
                $(this).next("input").attr("value",hidevalue);
                if($(this).parent(".ibox-content").siblings(".check_label").children(".check_icon").next("input").size() == "0"){
                    $(this).parent(".ibox-content").siblings(".check_label").children(".check_icon").addClass("check").after("<input type='hidden' name='permission_id[]' value=''>");
                    $(this).parent(".ibox-content").siblings(".check_label").children(".check_icon").next("input").attr("value",thisvalue);
                }
            }
        });
        
        //保存提交
        $("#saveForm").click(function(){
            $("#authForm").submit();
        });
    })
}

function rolemanage(){
    /**
     *角色权限管理弹出框处理
     *吴冷眉
     *QQ：2632840780
     *2015-12-15
     *update：wulengmei（2015-12-15 17:25） （最近更新/更改 作者及时间）
     **/
    $(function(){
        function  choice_from(){
            $("#add_role").click(function(){

                $("#Form1").show();
                $("#Form2").hide();
            });
            $(".edit_role").click(function(){

                $("#edit_id").val($(this).parent().siblings(".open-id").text());
                $("#edit_name").val($(this).parent().siblings(".role_name").text());
                $("#edit_des").val($(this).parent().siblings(".role_descrip").text())

                $("#Form2").show();
                $("#Form1").hide();
            })
        }
        function  delete_user(){
            $('.delete').click(function(){
                var id = $(this).attr('data');
                layer.confirm('确认删除？', {
                    title:'删除',
                    btn: ['是','否'] //��ť
                }, function(){
                    window.location.href="/auth/delete-role?id="+id;
                });
            });
        }
        choice_from();
        delete_user();
        $('#Form1').delegate('#sure','click', function () {
            $("#Form1").submit();
        });
        $('#Form2').delegate('#sure-notice','click', function () {
            $("#Form2").submit();
        });
        //$('#Form1').bootstrapValidator({
        //    message: 'This value is not valid',
        //    feedbackIcons: {/*输入框不同状态，显示图片的样式*/
        //        valid: 'glyphicon glyphicon-ok',
        //        invalid: 'glyphicon glyphicon-remove',
        //        validating: 'glyphicon glyphicon-refresh'
        //    },
        //    fields: {/*验证*/
        //        name: {/*键名username和input name值对应*/
        //            message: 'The username is not valid',
        //            validators: {
        //                notEmpty: {/*非空提示*/
        //                    message: '用户名不能为空'
        //                }
        //            }
        //        },
        //        description: {
        //            validators: {
        //                notEmpty: {
        //                    /*非空提示*/
        //                    message: '地址不能为空'
        //                }
        //            }
        //        }
        //    }
        //});
    });
}