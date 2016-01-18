/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "mybooking":mybooking();break; //我的预约页面
    }
});
//曾洁，2016.1.8 17:26修改
//我的预约页面
function mybooking(){
    $(document).ajaxSuccess(function(event, request, settings) {
        add();
    });
    var now_page=1;
    var qj={page:now_page};//设置页码
    ajaxApply(qj);
    now_index="0";
    initcard();
    function initcard(){//表单切换
        $("#mybooking li").unbind("click").click(function(){
            $(this).addClass("check").siblings().removeClass("check");
            now_index=$(this).index();
            $("#info_list>div").eq(now_index).show().siblings("div").hide();
            if(now_index==2){
                scroll();//判定在已完成页面，允许执行翻页
            }
        });
    }
    //取消申请
    $(".submit_box").unbind().click(function(){
        var apply_id=$(this).attr("apply_id");
        var submitBox=$(this);
        var url=pars.cancelUrl+"?apply_id="+apply_id;
        $.confirm({
            title: '提示',
            content: '是否取消此次预约？',
            confirmButton: '确定',
            cancelButton: '取消',
            confirm: function(){
                $.ajax({
                    url:url,
                    type:'GET',
                    cache:false,
                    dataType:"json",
                    success:function(data){
                        if(data.code == 1){
                            submitBox.parent().parent().remove();
                        }else{
                            $.alert({
                                title: '提示：',
                                content: '取消失败！!',
                                confirmButton: '确定',
                                confirm: function(){
                                    ajaxApply(qj);
                                }
                            });
                        }
                    }
                });
            }
        });
        return false;
    });
    //查看详情弹出层
    function add(){
        $(".add_main").unbind().click(function(){
            get_layer();
            $(".box_content").empty();
            var apply_id=$(this).attr("apply_id");
            var url=pars.detailUrl+"?apply_id="+apply_id;
            $.ajax({
                url:url,
                dataType:"json",
                type:"GET",
                cache:false,
                success:function(result){
                    var roll=result.data.rows.HistoryLaboratoryApplyList;
                    if(roll.status == "1"){
                        var str="待审核";
                    }else if(roll.status == "2"){
                        var str="已通过";
                    }else if(roll.status=="3"){
                        var str="未通过";
                    }else if(roll.status=="4"){
                        var str="已取消";
                    }else{
                        var str="已过期";
                    }
                    if(roll.type=="1"){
                        var apply_time=roll.begintime+"-"+roll.endtime;
                    }else{
                        var apply_time="";
                        $(roll.plan_apply).each(function(){
                            apply_time= apply_time + this.open_plan.begintime+"-"+this.open_plan.endtime+"<br/>";
                        });
                    }
                    var check_item = '';
                    if(roll.status == "2"){
                        check_item='<div class="form_title" style="font-weight: 700;">审核信息</div><div class="show_detail"> ' +
                            '<div class="add_main"> ' +
                            '<div class="form-group"> ' +
                            '<label for="">审核原因</label> ' +
                            '<div class="txt">请按时使用该教室 ' +
                            '</div> ' +
                            '</div> ' +
                            '<div class="form-group"> ' +
                            '<label for="">审核人</label> ' +
                            '<div class="txt">'+roll.audit_user+'('+roll.audit_time+')'+
                            '</div> ' +
                            '</div> ' +
                            '</div> ' +
                            '</div>'
                    }else if(roll.status == "3"){
                        check_item='<div class="form_title" style="font-weight: 700;">审核信息</div><div class="show_detail"> ' +
                            '<div class="add_main"> ' +
                            '<div class="form-group"> ' +
                            '<label for="">拒绝原因</label> ' +
                            '<div class="txt">'+roll.refuse_reason+
                            '</div> ' +
                            '</div> ' +
                            '<div class="form-group"> ' +
                            '<label for="">审核人</label> ' +
                            '<div class="txt">'+roll.audit_user+'('+roll.audit_time+')'+
                            '</div> ' +
                            '</div> ' +
                            '</div> ' +
                            '</div>'
                    }
                    $(".box_content").append('<div class="form_title" style="font-weight: 700;">预约信息</div> ' +
                        '<div class="show_detail"> ' +
                        '<div class="add_main"> ' +
                        '<div class="form-group"> ' +
                        '<label for="">实验室名称</label> ' +
                        '<div class="txt">'+roll.laboratory.name+
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label for="">地址</label> ' +
                        '<div class="txt">'+roll.laboratory.floor_info.name+roll.laboratory.floor+"楼"+roll.laboratory.code+
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label for="">预约日期</label> ' +
                        '<div class="txt">'+roll.apply_time+
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label for="">预约时段</label> ' +
                        '<div class="txt">' +apply_time+
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label for="">教学课程</label> ' +
                        '<div class="txt">'+roll.course_name +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label for="">学生人数</label> ' +
                        '<div class="txt">'+roll.laboratory.total +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label for="">备注</label> ' +
                        '<div class="txt">'+roll.description +
                        '</div> ' +
                        '</div> ' +
                        '<div class="form-group"> ' +
                        '<label for="">状态</label> ' +
                        '<div class="txt">'+str +
                        '</div> ' +
                        '</div> ' +
                        '</div> ' +
                        '</div> ' + check_item+
                        '</div> ' +
                        '</div>');
                }
            })
        });
    }
    //已完成页面
    function scroll(){
        //判定到底底部
        $(window).scroll(function(e){
            if(away_top >= (page_height - window_height)&&now_page<totalpages){

                now_page++;
                qj.page=now_page;//设置页码
                ajaxApply(qj);
                /*加载显示*/
            }
        });
    }
    function ajaxApply(qj){
        var endUrl=pars.endUrl;
        $.ajax({
            url:endUrl,
            type:"GET",
            dataType:"json",
            cache:false,
            data:qj,
            success:function(result){
                var roll=result.data.rows.HistoryLaboratoryApplyList;
                totalpages=Math.ceil(roll.total/roll.per_page);
                $(roll.data).each(function(){
                    var $this = this;
                    if(this.type == "1"){
                        var apply_time=this.begintime+"-"+this.endtime;
                    }else{
                        var apply_time="";
                        $($this.plan_apply).each(function(){
                            apply_time = apply_time+this.open_plan.begintime+"-"+this.open_plan.endtime+"<br/>";
                        })
                    }
                    if(this.status == "2"){
                        var statusStr='<div class="state_btn1">已通过</div>';
                    }else if(this.status == "3"){
                        var statusStr='<div class="state_btn2">未通过</div>';
                    }else if(this.status == "4"){
                        var statusStr='<div class="state_btn3">已取消</div>';
                    }else if(this.status == "5"){
                        var statusStr='<div class="state_btn3">已过期</div>';
                    }
                    $("#complete").append('<div class="add_main" apply_id="'+this.id+'"> ' +
                    '<div class="form-group"> ' +
                        '<label for="">实验室名称</label> ' +
                        '<div class="txt">'+this.laboratory.name+
                        '</div> ' + statusStr +
                        '</div> ' +
                    '<div class="form-group"> ' +
                        '<label for="">地址</label> ' +
                        '<div class="txt">'+this.laboratory.floor_info.name +this.laboratory.floor+"楼"+this.laboratory.code+
                        '</div> ' +
                        '</div> ' +
                    '<div class="form-group"> ' +
                        '<label for="">预约日期</label> ' +
                        '<div class="txt">'+this.apply_time+
                        '</div> ' +
                        '</div> ' +
                    '<div class="form-group"> ' +
                        '<label for="">预约时段</label> ' +
                        '<div class="txt">'+apply_time +
                        '</div> ' +
                        '</div> ' +
                        '</div>')
                })
            }
        })
    }
}