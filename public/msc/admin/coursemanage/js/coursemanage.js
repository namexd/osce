/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars=JSON.parse($("#parameter").val().split("'").join('"'));
    switch(pars.pagename){
        case "course_observe":course_observe();break;
    }
})
/*课程监管首页引用
lizhiyuan
qq:973261287
2015/12/15*/
function course_observe(){
    //二级菜单展开
    $(".first-level>p").click(function(){
        if($(this).attr("flag")=="false"){
            $(this).attr("flag","true");
            $(this).find(".glyphicon-chevron-right").hide();
            $(this).find(".glyphicon-chevron-down").show();
            $(this).next().show();
        }else{
            $(this).attr("flag","false");
            $(this).find(".glyphicon-chevron-right").show();
            $(this).find(".glyphicon-chevron-down").hide();
            $(this).next().hide();
        }
    })
    //三级菜单
    $(".second-level>p").click(function(){
        if($(this).attr("flag")=="false"){
            $(this).attr("flag","true");
            $(this).find(".glyphicon-chevron-right").hide();
            $(this).find(".glyphicon-chevron-down").show();
            $(this).next().show();
        }else{
            $(this).attr("flag","false");
            $(this).find(".glyphicon-chevron-right").show();
            $(this).find(".glyphicon-chevron-down").hide();
            $(this).next().hide();
        }
    })
}

