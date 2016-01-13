/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "mybooking_teacher":mybooking_teacher();break; //我的预约（老师）
    }
});

//我的预约（老师）
function mybooking_teacher(){
    now_index="0";
    initcard();
    function initcard(){//表单切换
        $("#mybooking li").unbind("click").click(function(){
            $(this).addClass("check").siblings().removeClass("check");
            now_index=$(this).index();
            $("#info_list>div").eq(now_index).show().siblings("div").hide();
        });
    }
}