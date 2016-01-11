/**
 * Created by Administrator on 2016/1/11 0011.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "booking_examine":booking_examine();break; //预约记录审核页面
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
        var url="";
        layer.confirm("确定通过预约？", {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });
    //不通过弹窗
    $(".refuse").click(function(){
        $("#refuse_from").show();
        $("#detail_from").hide();
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
        $("#refuse_from").hide();
        $("#detail_from").show();
    });
    //批量不通过
    $(".all_refuse").click(function(){
        if($(".check_label").children(".check").size()==0){
            layer.confirm("请至少选择一条记录进行操作！", {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=window.location.href;
            });
        }else{

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
            
        }
    })
}