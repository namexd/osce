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
    })
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





