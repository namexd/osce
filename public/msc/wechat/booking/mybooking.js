/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "mybooking_teacher":mybooking_teacher();break; //�ҵ�ԤԼ����ʦ��
        case "mybooking_student":mybooking_student();break; //�ҵ�ԤԼѧ��ҳ��
    }
});

//�ҵ�ԤԼ����ʦ��
function mybooking_teacher(){
    now_index="0";
    initcard();
    function initcard(){//���л�
        $("#mybooking li").unbind("click").click(function(){
            $(this).addClass("check").siblings().removeClass("check");
            now_index=$(this).index();
            $("#info_list>div").eq(now_index).show().siblings("div").hide();
        });
    }
}

function mybooking_student(){
    now_index="0";
    initcard();
    function initcard(){//�����л�
        $("#thelist2 li").unbind("click").click(function(){
            $(this).addClass("check").siblings().removeClass("check");
            now_index=$(this).index();
            $("#info_list>div").eq(now_index).show().siblings("div").hide();
        });
    }
}