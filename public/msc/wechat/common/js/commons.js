/**
 * Created by Administrator on 2016/1/3 0008.
 */


$(function(){
    /*虚拟滚动条调用may-add*/
    var content=$(window).height()-80;
    $(".inner-content").slimScroll({
        height: content,
    });
});

/*右侧弹出层may-add*/
function get_layer(){
    $("#sidepopup_layer").animate({right:"0"});//将右边弹出
    hide_layer();
}
function hide_layer(){//将右边隐藏
    $(".box_hidden").click(function(){
        $("#sidepopup_layer").animate({right:"-100%"});
    });
    $("#submit_layer").click(function(){
        $("#sidepopup_layer").animate({right:"-100%"});
    });
}

/*右侧弹出层end*/

