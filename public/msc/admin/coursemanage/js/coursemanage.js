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
/*�γ̼����ҳ����
lizhiyuan
qq:973261287
2015/12/15*/
function course_observe(){
    //�����˵�չ��
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
    //�����˵�
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

