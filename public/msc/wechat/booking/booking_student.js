/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "booking_student":booking_student();break; //预约实验室（学生）搜索页
        case "booking_student_detail":booking_student_detail();break; //预约实验室（学生）详情页
        case "booking_student_form":booking_student_form();break;//学生申请表单填写
    }
});

//预约实验室（学生）
function booking_student(){
    var url=pars.url;
    var target_url=pars.target_url;
    $("#order_time").val(nextday);
    var qj={DateTime:nextday};
    getlist(qj);
    //加载入页面的时候，自动加载隔天的实验室
    $("#order_time").change(function(){
        var qj=getqj();
        getlist(qj);
    });
    $("#select_submit").click(function(){
        select_ban();
        get_layer();
    });
    //弹出层选择楼层
    function select_ban(){
        $("#ban").change(function(){
            var floor_top =$(this).find("option:selected").attr("floor_top");
            var floor_bottom = parseInt($(this).find("option:selected").attr("floor_buttom"));
            $("#floor").empty();
            $("#floor").append('<option value="">全部楼层</option>');
            for(var i=1;i<=floor_bottom;i++){
                $("#floor").append('<option value="-'+i+'">-'+i+' 楼</option>');
            }
            for(var i=1;i<=floor_top;i++){
                $("#floor").append('<option value="'+i+'">'+i+' 楼</option>');
            }
            submit_select();//改变之后允许执行筛选
        })
    }
    function submit_select(){
        $("#submit_layer").click(function(){
            getlist(qj);
        });
    }
    function getqj(){//得到所有的查询条件内容
        var floor_id= $("#ban").find("option:selected").attr("value");
        var floor_num= $("#floor").find("option:selected").attr("value");
        var DateTime=$("#order_time").val();
        var qj={floor_id:floor_id,floor_num:floor_num,DateTime:DateTime}
        return qj;
    }

    function  getlist(qj){ //查询ajax查询
        $.ajax({
            url:url,
            type: "get",
            dataType: "json",
            cache: false,
            data:qj,
            success: function (result) {
                if(result.code==1){
                    $(".manage_list").empty();
                    $(result.data.rows.ClassroomApplyList.data).each(function(){
                        $(".manage_list").append('<a href="'+target_url+'?DateTime='+qj.DateTime+'&id='+this.id+'">'
                            +'<div class="all_list">'
                            +'<div class="w_85 left">'
                            +'<p>'+this.name+'</p>'
                            +'<p><span>'+this.floor_info.address+this.floor_info.name+this.floor+"楼"+this.code+'</span></p></div>'
                            +'<div class="w_15 right"><i class="fa fa-angle-right i_right" style="margin-top: 10px"></i>'
                            +'</div></div></a>');
                    })
                }else{

                }
            }
        })
    }
}
//预约实验室（学生）详情页
function booking_student_detail(){
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
