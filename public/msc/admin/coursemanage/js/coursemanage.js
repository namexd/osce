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
        /*if($(this).attr("flag")=="false"){
            $(this).attr("flag","true");
            $(this).find(".glyphicon-chevron-right").hide();
            $(this).find(".glyphicon-chevron-down").show();
            $(this).next().show();
        }else{
            $(this).attr("flag","false");
            $(this).find(".glyphicon-chevron-right").show();
            $(this).find(".glyphicon-chevron-down").hide();
            $(this).next().hide();
        }*/
        $(".second-level>p").removeClass("active");
        $(this).addClass("active");
        var $classroomId=$(this).attr("id");
        getLesson($classroomId,pars.lessonUrl);
    })
    //ajax��ȡ�γ̺���ʦ��Ϣ
    function getLesson(id,url){
        $.ajax({
            url:url,
            type:"get",
            dataType:"json",
            data:{
                id:id
            },
            success: function(result){
                $("#lesson").html(result.content);
                $("#teacher").html(result.teacher);
            }
        });
    }
    //��ȡ��ǰʱ��
    function getCurrentTime(){
        var d = new Date();
        var year = d.getFullYear()+"-"+d.getMonth()+"-"+d.getDate();
        if(d.getSeconds()<10){
            var hour= d.getHours()+":"+ d.getMinutes()+":"+ "0"+d.getSeconds();
        }else{
            var hour= d.getHours()+":"+ d.getMinutes()+":"+ d.getSeconds();
        }
        $("#year").html(year);
        $("#hour").html(hour);
        setTimeout(getCurrentTime,1000);
    }
    //ҳ����ؾ�ִ�в���
    getCurrentTime();
}

