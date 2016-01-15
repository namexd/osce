/**
 * Created by Administrator on 2016/1/11 0011.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "booking_examine":booking_examine();break; //预约记录审核页面
        case "booking_examine_other":booking_examine_other();break; //预约记录审核已处理页面
    }
});
//预约记录审核页面
function booking_examine(){
    //选项框切换
    var $check_all=$(".check_all");
    var $check_one=$(".check_one");
    $check_all.click(function(){
        if($(this).children(".check_icon").hasClass("check")){
            $(this).children(".check_icon").removeClass("check");
            $(".check_one").children(".check_icon").removeClass("check");
        }else{
            $(this).children(".check_icon").addClass("check");
            $(".check_one").children(".check_icon").addClass("check");
        }
    });
    $check_one.click(function(){
        if($(this).children(".check_icon").hasClass("check")){
            $(this).children(".check_icon").removeClass("check");
            $check_all.children(".check_icon").removeClass("check");
        }else{
            $(this).children(".check_icon").addClass("check");
            if($check_one.size()==$check_one.children(".check").size()){
                $check_all.children(".check_icon").addClass("check");
            }
        }
    });
    //通过弹窗
    $(".pass").click(function(){
        var id = $(this).attr('data-id');
        var url="/msc/admin/laboratory/lab-order-check?id="+id+"&type=2";
        layer.confirm("确定通过预约？", {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });

    //不通过弹窗
    $(".refuse").click(function(){
        var id = $(this).attr('data-id');
        $('#refuse_from').show().append('<input type="hidden" name="id" value="'+id+'">');
        $("#detail_from").hide();
        $("#choose_from").hide();
    });
    //不通过验证
    $('#refuse_from').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            reason: {/*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '原因不能为空'
                    }
                }
            }
        }
    });
    //详情弹窗
    $(".detail").click(function(){
        var id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: "/msc/admin/laboratory/lab-order-detail",
            data: {id:id},
            success: function(msg){
                $('.labname').val(msg.labname);
                $('.address').val(msg.labname+msg.floor+'楼'+msg.code);
                $('.ordertime').val(msg.apply_time);
                if(!msg.begintime && !msg.endtime){
                    $('.orderdate').val(msg.playdate);
                }else{
                    $('.orderdate').val(msg.begintime+'~'+msg.endtime);
                }

                $('.total').val(msg.total);
                $('.class').val(msg.course_name);
                $('.player').val(msg.name);
                $('.reason').val(msg.description);
                $('.applytime').val(msg.created_at);

            }
        });
        $("#choose_from").hide();
        $("#refuse_from").hide();
        $("#detail_from").show();
    });
    //批量不通过
    $(".all_refuse").click(function(){
        if($(".check_label").children(".check").size()==0){
            $("#refuse_from").hide();
            $("#detail_from").hide();
            $("#choose_from").show();
        }else{
            $("#refuse_from").show();
            $("#detail_from").hide();
            $("#choose_from").hide();
        }
    });

    //批量通过
    $(".all_pass").click(function(){
        if($(".check_label").children(".check").size()==0){
            layer.confirm("请至少选择一条记录进行操作！", {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=window.location.href;
            });
        }else{
            var idstr = '';
            $(".check_label").children(".check").each(function(i){
                if($(this).attr('data-id')){
                    idstr += $(this).attr('data-id')+',';
                }
            });
            $.ajax({
                type: "GET",
                url: "/msc/admin/laboratory/lab-order-allcheck",
                data: {idstr:idstr},
                success: function(msg){
                    if(msg.status == 1){
                        layer.msg(msg.info, {icon: 2,time: 2000});
                        window.location.href=window.location.href;
                    }else if(msg.status == 2){
                        layer.msg(msg.info, {icon: 1,time: 2000});
                    }else if(msg.status == 3){
                        layer.msg(msg.info, {icon: 1,time: 2000});
                    }else if(msg.status == 4){
                        layer.msg(msg.info, {icon: 1,time: 2000});
                    }else{
                        layer.confirm(msg.info, {
                            btn: ['是','否'] //按钮
                        }, function(){
                            $.ajax({
                                type: "GET",
                                url: "/msc/admin/laboratory/lab-order-allcheck",
                                data: {idstr:idstr,teacher:1},
                                success: function(msg){
                                    if(msg.status == 1){
                                        layer.msg(msg.info, {icon: 2,time: 2000});
                                        window.location.href=window.location.href;
                                    }else{
                                        layer.msg(msg.info, {icon: 1,time: 2000});
                                    }
                                }
                            });
                        });
                    }
                }
            });
        }
    })
}

//预约记录审核已处理页面
function booking_examine_other(){
    //详情弹窗
    $(".detail").click(function(){
        var id = $(this).attr('data-id');
        $.ajax({
            type: "POST",
            url: "/msc/admin/laboratory/lab-order-detail",
            data: {id:id},
            success: function(msg){
                $('.labname').val(msg.labname);
                $('.address').val(msg.labname+msg.floor+'楼'+msg.code);
                $('.ordertime').val(msg.apply_time);
                if(!msg.begintime && !msg.endtime){
                    $('.orderdate').val(msg.playdate);
                }else{
                    $('.orderdate').val(msg.begintime+'~'+msg.endtime);
                }

                $('.total').val(msg.total);
                $('.class').val(msg.course_name);
                $('.player').val(msg.name);
                $('.reason').val(msg.description);
                $('.applytime').val(msg.created_at);

            }
        });
        $("#choose_from").hide();
        $("#refuse_from").hide();
        $("#detail_from").show();
    });
}