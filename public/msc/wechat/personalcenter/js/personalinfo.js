/**
 * Created by DELL on 2015/11/25.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "myborrow":myborrow();break; //myborrow
        case "phone_change":phone_change();break; //phone_change
    }

});
function myborrow(){
    now_index="0";
    initcard();//表单切换
    var now_page=1;
    var qj={page:now_page};//设置页码
    var cancel_borrow = pars.cancel_borrow;
    var url = pars.url;
    var getdetail = pars.getdetail;
    gethistory(qj,url,getdetail);
    //判定到底底部
    $(window).scroll(function(e){
        //判定是历史列表时执行翻页
        if(away_top >= (page_height - window_height)&&now_page<totalpages&&now_index=="2"){
            now_page++;
            var qj={page:now_page};//设置页码
            gethistory(qj,url,getdetail)
            /*加载显示*/
        }
    })
    $(".cancle").click(function(){
        var $this = $(this);
        var id = $this.attr('BorrowingId');
        $.confirm({
            title: '提示：',
            content: '是否取消此次外借申请？',
            confirmButton: '　　　是　　　 ' ,
            cancelButton: '　　　否　　　',
            confirmButtonClass: 'btn-info',
            cancelButtonClass: 'btn-danger',
            confirm:function(){
                $.ajax({
                    url:cancel_borrow,
                    type:"post",
                    dataType:"json",
                    cache:false,
                    data:{id:id},
                    success: function(result) {
                        if(result.code == 1){
                            $this.parents('.add_main').remove();
                        }else{
                            alert(result.message);
                        }
                    },

                });
            }
        })

    });

    function initcard(){//表单切换
        $("#thelist2 li").unbind("click").click(function(){
            $(this).addClass("check").siblings().removeClass("check");
            now_index=$(this).index();
            $("#info_list>div").eq(now_index).show().siblings("div").hide();
            //判定是历史列表时执行翻页
        });
    }

    $(".more_txt").click(function(){
        var h=parseInt($(".gn_txt").height());
        var height=parseInt($(".gn_txt span").height()+6);
        if(h==18){
            $(".more_txt").css({transform:"rotate(90deg)"});
            $(".gn_txt").animate({height:height},300);
        }else{
            $(".more_txt").css({transform:"rotate(0deg)"});
            $(".gn_txt").animate({height:"30px"},300);
        }
    })

    function gethistory(qj,url,getdetail){
        $.ajax({
            url:url, /*${ctx}/*/
            type:"get",
            dataType:"json",
            cache:false,
            data:qj,
            success: function(result) {
                totalpages=result.data.rows.historyList.last_page;
                $(result.data.rows.historyList.data).each(function(){

                    if(this.tool_item!=null){
                        if(this.tool_item.status=="2"){
                            var status='<span class="State3">已借出</span>';
                        }else if(this.tool_item.status=="1"){
                            var status='<span class="State1">正常</span>';
                        }else if(this.tool_item.status=="-1"){
                            var status='<span class="State2">不允许借出</span>';
                        }else{
                            var status='<span class="State2">已报废</span>';
                        }
                    }else{
                        var status='<span class="State3">状态未知</span>';
                    }
                    $(".detail_list ul").append(
                        '<li><a href="'+getdetail+'?id='+this.id+'">'
                        +'<div><span>'+this.resources_tool.name+'</span> </div>'
                        +'<div>'+this.code+'</div>'
                        +'<div class="Time_slot">'
                        +'<p>'+this.real_begindate+'</p>'
                        +'<p><span class="State1">'+this.real_begindate+'</span></p>'
                        +'</div>'
                        +' </a></li>'
                    );

                });

            },

        });

    }
}

function phone_change(){
    $('#info_list').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            mobile: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|5|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
        }
    });
    $('#change_submit').submit(function(){
        var yz_num = $('input[name="yz_num"]').val();
        if(yz_num=="0"){
            $.alert({
                title: '提示：',
                content: '验证码错误!',
                confirmButton: '确定',
                confirm: function(){
                }
            });
            return false;

        }else{

        }

    })
    $('#getVerificationButtonOne').click(function(){
        var moblie = $('#mobile').val();
        var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if(reg.test(moblie)){
            duMiao($('#getVerificationButtonOne'));
            $('#VerificationText').attr('disabled',false);
            $('#getVerificationButtonOne').next('input').val(0);
            $.ajax("{{ url('/api/1.0/public/msc/user/reg-moblie-verify') }}",{
                type: 'get',
                data: {mobile:moblie},
                success:function(data, textStatus, jqXHR) {
                    //console.log(data);
                },
                error:function(result) {
                    //console.log(result);
                },
                dataType: "json"
            });
        }else{
            $.alert({
                title: '提示：',
                content: '手机号错误!',
                confirmButton: '确定',
                confirm: function(){
                }
            });
        }
    })

    $('#VerificationText').blur(function(){
        var moblie = $('#mobile').val();
        var reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if(reg.test(moblie)) {
            var obj = {mobile: $('#mobile').val(), code: $('#VerificationText').val()};
            $.ajax("{{ url('/api/1.0/public/msc/user/reg-check-mobile-verfiy') }}", {
                type: 'get',
                data: obj,
                success: function (data, textStatus, jqXHR) {
                    if (data.code == 1) {
                        $('#VerificationText').attr('disabled', 'disabled');
                        $('#getVerificationButtonOne').next('input').val(1);
                    } else {
                        $.alert({
                            title: '提示：',
                            content: data.message,
                            confirmButton: '确定',
                            confirm: function () {
                            }
                        });
                    }
                },
                error: function (result) {
                    $.alert({
                        title: '提示：',
                        content: "手机号码有误！或者该手机号码已经被注册",
                        confirmButton: '确定',
                        confirm: function () {
                        }
                    });
                },
                dataType: "json"
            });
        }
    })

}

