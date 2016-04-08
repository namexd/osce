/**
 * Created by Administrator on 2016/3/7 zengjie
 * 题库管理
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "monitor_test":monitor_test();break;//考试监控正在考试页面
        case "monitor_late":monitor_late();break;//考试监控迟到页面
    }
});

//考试监控正在考试页面
function monitor_test(){
    //终止考试
    $(".stop").click(function () {
        //终止考试弹出层标题赋值
        var $student = $(this).parent().parent().siblings(".student").text();
        var $station = $(this).parent().parent().siblings(".station").text();
        $(".stuName").text($student);
        $(".stationName").text($station);
        //终止考试需要的当前数据
        var $examId = $(this).parent().attr("examId");
        var $studentId = $(this).parent().attr("studentId");
        var $examScreeningId = $(this).parent().attr("examScreeningId");
        var $stationId = $(this).parent().attr("stationId");
        var $userId = $(this).parent().attr("userId");
        var $examScreeningStudentId = $(this).parent().attr("examScreeningStudentId");
        $("#data").attr("examId",$examId);
        $("#data").attr("studentId",$studentId);
        $("#data").attr("examScreeningId",$examScreeningId);
        $("#data").attr("stationId",$stationId);
        $("#data").attr("userId",$userId);
        $("#data").attr("examScreeningStudentId",$examScreeningStudentId);
    });
    //终止考试提交
    $("#stopSure").click(function(){
        var stopUrl = pars.stopUrl;
        var reason = $("#reason option:selected").val();
        $.ajax({
            url:stopUrl,
            cache:false,
            data:{examId:$("#data").attr("examId"),
                studentId:$("#data").attr("studentId"),
                examScreeningId:$("#data").attr("examScreeningId"),
                stationId:$("#data").attr("stationId"),
                userId:$("#data").attr("userId"),
                examScreeningStudentId:$("#data").attr("examScreeningStudentId"),
                status:3,
                description:reason
            },
            dataType:"json",
            success:function(res){
                if(res == true){
                    window.location.href=window.location.href;
                    layer.msg("提交成功！",{skin:'msg-success',icon:1});
                }else{
                    layer.msg(res,{skin:'msg-error',icon:1});
                }
            }
        })
    });
    //确认弃考
    $(".abandon").click(function(){
        var $student = $(this).parent().parent().siblings(".student").text();
        var $idCard = $(this).parent().parent().siblings(".idCard").text();
        layer.confirm("确认当前考生"+$student+"（"+$idCard+"）放弃考试？",function(){

        });
    });
    //确认替考
    $(".replace").click(function(){
        var $student = $(this).parent().parent().siblings(".student").text();
        var $idCard = $(this).parent().parent().siblings(".idCard").text();
        layer.confirm("确认当前考生"+$student+"（"+$idCard+"）替考？",function(){

        });
    });
    //查看视频
    $(".look").click(function(){
        var videoUrl = pars.videoUrl;
        var examId = $(this).attr("examId");
        var stationId = $(this).attr("stationId");
        layer.open({
            type: 2,
            title: '视频查看',
            area: ['800px', '500px'],
            fix: false, //不固定
            maxmin: true,
            content: videoUrl+"?examId="+examId+"&stationId="+stationId
        })
    });
}

//考试监控迟到页面
function monitor_late(){
    //确认弃考
    $(".abandon").click(function(){
        var $student = $(this).parent().parent().siblings(".student").text();
        var $idCard = $(this).parent().parent().siblings(".idCard").text();
        layer.confirm("确认当前考生"+$student+"（"+$idCard+"）放弃考试？",function(){

        });
    });
}





