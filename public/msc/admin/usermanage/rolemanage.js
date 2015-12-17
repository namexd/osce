/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "rolemanage_detail":rolemanage_detail();break; //rolemanage_detail页面
        case "rolemanage":rolemanage();break; //rolemanage页面
    }
});

function rolemanage_detail(){
    /**
     *角色权限管理（设置权限）checkbox选择处理
     *曾洁
     *QQ：283020075
     *2015-12-15
     *update：zengjie（2015-12-17 10:46） （最近更新/更改 作者及时间）
     **/
    $(function(){
        var $check_label=$(".check_label");
        $check_label.unbind().click(function(){
            if($(this).children(".check_icon").hasClass("check")){
                $(this).children(".check_icon").removeClass("check");
                $(this).children("input").attr("checked",false);
                return false;
            }else{
                $(this).children(".check_icon").addClass("check");
                $(this).children("input").attr("checked",true);
                return false;
            }
        });
        $('#saveForm').click(function(){
            $('#authForm').submit();
        });
        $('.check_real').click(function(){
            $(this).find('input').val();
        })
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
                layer.confirm('你确定删除？', {
                    btn: ['是','否'] //按钮
                }, function(){
                    window.location.href="/auth/delete-role?id="+id;
                });
            });
        }
        choice_from();
        delete_user();
    });
}