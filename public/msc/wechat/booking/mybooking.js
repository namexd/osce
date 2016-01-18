/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "mybooking":mybooking();break; //我的预约页面
    }
});
//曾洁，2016.1.8修改
//我的预约页面
function mybooking(){
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