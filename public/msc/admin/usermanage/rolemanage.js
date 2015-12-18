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
     *��ɫȨ�޹�������Ȩ�ޣ�checkboxѡ����
     *����
     *QQ��283020075
     *2015-12-15
     *update��zengjie��2015-12-18 17:30�� ���������/���� ���߼�ʱ�䣩
     **/
    $(function(){
        var $check_label=$(".check_label");
        var $btn_padding=$(".btn_padding");
        $check_label.click(function(){
            if($(this).children(".check_icon").hasClass("check")){
                $(this).children(".check_icon").removeClass("check");
                $(this).children("input").attr("checked",false);
                $(this).siblings(".ibox-content").children("button").attr("checked",false);
                $(this).siblings(".ibox-content").children("button").removeClass("btn_focus");
                $(this).siblings(".ibox-content").children("button").addClass("btn-default2");
                return false;
            }else{
                $(this).children(".check_icon").addClass("check");
                $(this).children("input").attr("checked",true);
                $(this).siblings(".ibox-content").children("button").attr("checked",true);
                $(this).siblings(".ibox-content").children("button").addClass("btn_focus");
                $(this).siblings(".ibox-content").children("button").removeClass("btn-default2");
                return false;
            }
        });
        $btn_padding.click(function(){
            if($(this).hasClass("btn_focus")){
                $(this).removeClass("btn_focus");
                $(this).addClass("btn-default2");
                $(this).attr("checked",false);
            }else{
                $(this).addClass("btn_focus");
                $(this).removeClass("btn-default2");
                $(this).attr("checked",true);
            }
        });

        //保存提交
        $('#Form1').delegate('#sure','click',function(){
            $('#Form1').submit();
        });

        $('#Form2').delegate('#sure-notice','click',function(){
            $('#Form2').submit();
        });
    })
}

function rolemanage(){
    /**
     *��ɫȨ�޹���������
     *����ü
     *QQ��2632840780
     *2015-12-15
     *update��wulengmei��2015-12-15 17:25�� ���������/���� ���߼�ʱ�䣩
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
                layer.confirm('您確定刪除嗎？', {
                    btn: ['是','否'] //��ť
                }, function(){
                    window.location.href="/auth/delete-role?id="+id;
                });
            });
        }
        choice_from();
        delete_user();
    });
}