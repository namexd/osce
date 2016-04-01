/**
 * Created by Administrator on 2016/3/7 zengjie
 * 题库管理
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "theory_validate":theory_validate();break;//理论考试老师登录进入验证页面
        case "theory_complete":theory_complete();break;//理论考试完成页面
        case "theory_student_validate":theory_student_validate();break;//理论考试学生登录验证页面
    }
});

//考核标签
function theory_validate(){
    //请求接口获取学生信息
    function queryAjax(){
        var stationId = $(".allData").attr("data");
        var examId = $(".allData").attr("examId");
        var timer = setInterval(function(){
            $.ajax({
                url:'/osce/admin/api/exam-paper-status?station_id='+stationId+'&exam_id='+examId,
                type:'get',
                cache:false,
                dateType:'json',
                success:function(res){
                    console.log(1111);
                    if (res.code == 2) {
                        console.log(22222);
                        $('#examinfo').html('理论考试已结束');
                        clearInterval(timer);
                    } else {
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
                                location.href=examUrl+"?id="+res+"&stationId="+stationId+"&userId="+userId+"&studentId="+studentId+"&examId="+examId;
                            }else{
                                //layer.msg('没有对应的试卷信息！',{skin:'msg-error',type:1});
                                layer.alert('没有对应的试卷信息！');
                            }
                        }
                    })
                }else{
                    layer.confirm('开始考试失败！');
                }
            }
        })
    });
}
//理论考试老师登录进入验证页面
function theory_complete(){
    $("#sure").click(function(){
        var url = pars.goUrl;
        location.href=url;
    })
}
//理论考试学生登录验证页面
function theory_student_validate(){
    $('.examing').click(function(){
        var station_id = $(this).attr('station');
        var teacher_id = $(this).attr('teacher');
        var paper_id = $(this).attr('paper');
        var exam_id = $(this).attr('exam');
        var student_id = $(this).attr('student');
        $.ajax({
            type: "GET",
            url: "/osce/api/invigilatepad/start-exam",
            data: {station_id:station_id,teacher_id:teacher_id,student_id:student_id},
            success: function(msg){
                if(msg.code){
                    window.location.href="/osce/admin/answer/formalpaper-list?stationId="+station_id+"&userId="+teacher_id+"&studentId="+student_id+"&id="+paper_id+"&examId="+exam_id;
                }else{
                    alert(msg.message);
                }
            }
        });
    });
}




