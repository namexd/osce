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
        case "booking_student_form":booking_student_form();break;//学生申请表单填写
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
    //弹出资源清单
    $(".submit_box button").click(function () {
        get_layer();
    })
    //添加去除筛选时段
    $(".check_label").click(function(){
       var labid= $(this).parents().parents().attr("id");
        var make = false;
        var dateDocArr = $('.date_list').find('input');
        if(dateDocArr.length>0){
            dateDocArr.each(function(){

                if(labid == $(this).val()){
                    $(this).remove();
                    make = true;
                    return false;
                }
            })
        }
        if(make){
            return false;
        }
        $('.date_list').append('<input type="hidden" name="open_plan_id[]" class="labid" value="'+labid+'">');
    })
}
//预约实验室（学生）提交表单
function booking_student_form(){
    $(".submit_box button").click(function () {
        get_layer();
    })
    //表单验证
    $("#booking_student_form").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            description: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '课程名不能为空'
                    }
                },
                stringLength: {/*长度提示*/
                    min: 1,
                    max:512,
                    message: '申请原因不得超过512个字符'
                }
            },
        }
    })

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