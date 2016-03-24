/**
 * Created by Administrator on 2016/3/7 zengjie
 * 题库管理
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "theory_validate":theory_validate();break;//理论考试进入验证页面
    }
});

//考核标签
function theory_validate(){
    //请求接口获取学生信息
    function queryAjax(){
        var stationId = $(".allData").attr("data");
        var timer = setInterval(function(){
            $.ajax({
                url:'/osce/api/invigilatepad/authentication?station_id='+stationId,
                type:'get',
                cache:false,
                dateType:'json',
                success:function(res){
                    if(res.code == 1){
                        $(".showImf").show();
                        $(".wait").hide();
                        $(".stuName").text(res.data.name);
                        $(".stuNum").text(res.data.code);
                        $(".idNum").text(res.data.idcard);
                        $(".admissionNum").text(res.data.exam_sequence);
                        $(".myImg").attr("src",res.data.avator);
                        $(".goTest").attr("studentId",res.data.student_id);
                        clearInterval(timer);
                    }
                }
            })
        },5000);
    }
    queryAjax();
    //进入考试
    $(".goTest").click(function(){
        var paperUrl = pars.paperUrl;
        var examUrl = pars.examUrl;
        var stationId = $(".allData").attr("data");
        var userId = $(".allData").attr("userId");
        var studentId = $(this).attr("studentId");
        var examId = $(".allData").attr("examId");
        $.ajax({
            url:"/osce/api/invigilatepad/start-exam?student_id="+studentId+"&station_id="+stationId+"&user_id="+userId,
            dataType:"json",
            cache:false,
            type:"get",
            success:function(res){
                if(res.code == 1){
                    $.ajax({
                        url:paperUrl+"?examId="+examId+"&stationId="+stationId,
                        dataType:"json",
                        cache:false,
                        type:"get",
                        success:function(res){
                            if(res){
                                location.href=examUrl+"?id="+res+"&stationId="+stationId+"&userId="+userId+"&studentId="+studentId;
                            }else{
                                //layer.msg('没有对应的试卷信息！',{skin:'msg-error',type:1});
                                layer.confirm('没有对应的试卷信息！',{
                                    closeBtn:0
                                });
                            }
                        }
                    })
                }else{
                    layer.confirm('开始考试失败！',{
                        closeBtn:0
                    });
                }
            }
        })
    });
}






