/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "booking_student":booking_student();break; //预约实验室（学生）详情页
        case "open_teacher_detail":open_teacher_detail();break; //老师预约开放实验室详情页
    }
});

//预约实验室（学生）详情页
function booking_student(){
    var $check_one=$(".check_one");
    $check_one.click(function(){
        if($(this).children(".check_icon").hasClass("check")){
            $(this).children(".check_icon").removeClass("check");
        }else{
            $(this).children(".check_icon").addClass("check");
        }
    });
}
function open_teacher_detail(){
    var $check_one=$(".check_one");
    $check_one.click(function(){
        if($(this).children(".check_icon").hasClass("check")){
            $(this).children(".check_icon").removeClass("check");
        }else{
            $(this).children(".check_icon").addClass("check");
        }
    });
}