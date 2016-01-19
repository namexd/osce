/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "booking_teacher":booking_teacher();break; //预约实验室（老师）搜索页
        case "booking_teacher_open_detail":booking_teacher_open_detail();break; //老师预约开放实验室详情页
        case "booking_teacher_open_form":booking_teacher_open_form();break; //老师预约开放实验室填写页
        case "booking_teacher_ordinary_form":booking_teacher_ordinary_form();break; //老师预约普通实验室填写页
    }
});

function booking_teacher(){
    var type=pars.type;
    if(type=="open"){
        var url=pars.url;
        var target_url=pars.target_url;
    }else {
        var url=pars.url2;
        var target_url=pars.target_url2;
    }
    //加载入页面的时候，自动加载隔天的实验室
    var now_page=1;
    $("#order_time").val(nextday);
    var qj={DateTime:nextday,page:now_page};
    getlist(qj);
    //end
    //日期改变时
    $("#order_time").change(function(){
        var qj=getqj();
        getlist(qj);
    });
    $("#select_submit").click(function(){
        select_ban();
        get_layer();
    });
    //翻页插件
    $(window).scroll(function(e){//判定到底底部
        if(away_top >= (page_height - window_height)&&now_page<totalpages){
            now_page++;
            qj.page=now_page;//设置页码
            getlist(qj);
            /*加载显示*/
        }
    });
    //弹出层选择楼层
    function select_ban(){
        $("#ban").change(function(){
            var floor_top =$(this).find("option:selected").attr("floor_top");
            var floor_bottom = parseInt($(this).find("option:selected").attr("floor_bottom"));
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
        $("#submit_layer").click(function(){//执行筛选
            getlist(qj);
        });
    }
    function getqj(){//得到所有的查询条件内容
        $(".manage_list").empty();
        var floor_id= $("#ban").find("option:selected").attr("value");
        var floor_num= $("#floor").find("option:selected").attr("value");
        var DateTime=$("#order_time").val();
        var qj={floor_id:floor_id,floor_num:floor_num,DateTime:DateTime,page:"1"}
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
                    totalpages=Math.ceil(result.data.total/result.data.pagesize);
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
//预约实验室（老师）详情页
function booking_teacher_open_detail(){
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
    $("#submit").click(function(){
        if($(".date_list").children("input").size()=="0"){

            $.alert({
                title: '提示：',
                content: '您尚未选择时间段!',
                confirmButton: '确定',
                confirm: function(){

                }
            });
            return false;
        }
    })

}
//老师预约开放实验室填写页
function booking_teacher_open_form(){
    var $stu_num=$(".stu_num");
    $stu_num.change(function(){
        if($stu_num.val()<=0){
            $stu_num.val("1");
        }
    });
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
            course_name: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '课程名不能为空'
                    }
                }
            },
            total: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '学生人数不能为空'
                    }
                }
            },
            description: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '申请原因不能为空'
                    }
                },
                stringLength: {/*长度提示*/
                    min: 1,
                    max:512,
                    message: '申请原因不得超过512个字符'
                }
            }
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

//老师预约普通实验室填写页
function booking_teacher_ordinary_form(){
    $(".submit_box button").click(function () {
        get_layer();
    })
    /*课程人数最少为1*/
    var $stu_num=$(".stu_num");
    $stu_num.change(function(){
        if($stu_num.val()<=0){
            $stu_num.val("1");
        }
    });
    /*选择时间段插件*/
    var opt={};
    opt.date = {preset : 'date'};
    opt.datetime = {preset : 'datetime'};
    opt.time = {preset : 'time'};
    opt.default = {
        theme: 'android-ics light', //皮肤样式
        display: 'bottom',//显示方式
        mode: 'scroller', //日期选择模式
        lang:'zh',
    };
    var optTime = $.extend(opt['time'], opt['default']);
    $("#begintime_set").mobiscroll(optTime).time(optTime);
    $("#endTime_set").mobiscroll(optTime).time(optTime);
    /*验证时间段是否被选中*/
    var Take = [];
    $('#Take').find('p').each(function(){
        var data = [];
        var time = $(this).find('span:first').html().split("-");
        data['begintime'] =time[0].replace(":","").toString();
        data['endtime'] = time[1].replace(":","").toString();
        data['name'] = $(this).find('span:last').html();
        Take.push(data);
    })
    var beginTime;
    var endTime;
    $('#begintime_set').change(function(){
        beginTime= $(this).val().replace(":","").toString();
        if(!Take){
            $(Take).each(function () { //循环判定 开始时间是否在已选定的时间段内
                if (beginTime>=this.begintime&&beginTime<=this.endtime){
                    $.alert({
                        title: '提示：',
                        content: '您选择的时间段已被占用，请重新选择其他时间段！',
                        confirmButton: '确定',
                    });
                    $('#begintime_set').val("");
                }else{
                    if(beginTime>=endTime){
                        $.alert({
                            title: '提示：',
                            content: '结束时间必须晚于开始时间！',
                            confirmButton: '确定',
                        });
                        $('#begintime').val("");
                        $('#begintime_set').val("");
                        return false;
                    }else{
                        $("#begintime").val($('#begintime_set').val());
                    }
                }

            })
        }else{

            if(beginTime>=endTime){
                $.alert({
                    title: '提示：',
                    content: '结束时间必须晚于开始时间！',
                    confirmButton: '确定',
                });
                $('#begintime').val("");
                $('#begintime_set').val("");
                return false;
            }else{
                $("#begintime").val($('#begintime_set').val());
            }

        }

    })

    $('#endTime_set').change(function(){
         endTime= $(this).val().replace(":","").toString();
        if(!Take){
            $(Take).each(function () { //循环判定 结束时间时间是否在已选定的时间段内
                if (endTime>=this.begintime&&endTime<=this.endtime){
                    $.alert({
                        title: '提示：',
                        content: '您选择的时间段已被占用，请重新选择其他时间段！',
                        confirmButton: '确定',
                    });
                    $('#endTime_set').val("");
                    return false;
                }
                else{
                    if(beginTime>=endTime){
                        $.alert({
                            title: '提示：',
                            content: '结束时间必须晚于开始时间！',
                            confirmButton: '确定',
                        });
                        $('#endTime_set').val("");
                        $('#endTime_set').val("");
                        return false;
                    }else{
                        $("#endtime").val($('#endTime_set').val());
                    }

                }
            })

        }else{
            if(beginTime>=endTime){
                $.alert({
                    title: '提示：',
                    content: '结束时间必须晚于开始时间！',
                    confirmButton: '确定',
                });
                $('#endTime_set').val("");
                $('#endTime_set').val("");
                return false;
            }else{
                $("#endtime").val($('#endTime_set').val());
            }

        }

    })


    //表单验证
    $("#booking_teacher_form input").focus(function(){
        if(!beginTime||!endTime){
            $.alert({
                title: '提示：',
                content: '请先选择使用时间段！',
                confirmButton: '确定',
            });
        }

    })
    $("#booking_teacher_form").bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            course_name: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '课程名不能为空'
                    }
                }
            },
            total: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '学生人数不能为空'
                    }
                }
            },
            description: {
                message: 'The hospital is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '申请原因不能为空'
                    }
                },
                stringLength: {/*长度提示*/
                    min: 1,
                    max:512,
                    message: '申请原因不得超过512个字符'
                }
            }
        }
    })
    /*日期控件修改*/

}