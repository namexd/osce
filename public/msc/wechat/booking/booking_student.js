/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "booking_student":booking_student();break; //预约实验室（学生）详情页
        case "open_teacher_detail":open_teacher_detail();break; //老师预约开放实验室详情页
        case "open_teacher_write":open_teacher_write();break; //老师预约开放实验室填写页
        case "common_teacher_write":common_teacher_write();break; //老师预约普通实验室填写页
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
//老师预约开放实验室详情页
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
//老师预约开放实验室填写页
function open_teacher_write(){
    var $stu_num=$(".stu_num");
    $stu_num.change(function(){
        if($stu_num.val()<=0){
            $stu_num.val("1");
        }
    });
    //表单验证
    $("#myform").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            course: {
                message: 'The hospital is not valid',
                    validators: {
                    notEmpty: {/*非空提示*/
                        message: '课程名不能为空'
                    }
                }
            },
            num: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '学生人数不能为空'
                    }
                }
            }
        }
    })
}
//老师预约普通实验室填写页
function common_teacher_write(){
    var $stu_num=$(".stu_num");
    $stu_num.change(function(){
        if($stu_num.val()<=0){
            $stu_num.val("1");
        }
    });
    //表单验证
    $("#myform").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            /*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            time:{
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {
                        /*非空提示*/
                        message: '使用时段不能为空'
                    }
                }
            },
            course: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {
                        /*非空提示*/
                        message: '课程名不能为空'
                    }
                }
            },
            num: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {
                        /*非空提示*/
                        message: '学生人数不能为空'
                    }
                }
            }
        }
    });
}