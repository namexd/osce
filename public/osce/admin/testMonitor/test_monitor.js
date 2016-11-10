/**
 * Created by Administrator on 2016/3/7 zengjie
 * 题库管理
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    $('#abandonExam').bootstrapValidator({//验证
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            /*验证*/
            reason: {
                validators: {
                    notEmpty: {
                        /*非空提示*/
                        message: '弃考理由不能为空！'
                    }
                }
            }/*,
            radio: {
                validators: {
                    notEmpty: {
                        /!*非空提示*!/
                        message: '弃考处理不能为空！'
                    }
                }
            }*/
        }
    });
    switch(pars.pagename){
        case "monitor_test":monitor_test();break;//考试监控正在考试页面
        case "monitor_late":monitor_late();break;//考试监控迟到页面
    }
});

//考试监控正在考试页面
function monitor_test(){
    //刷新页面
    $("#refresh").click(function(){
        window.location.href=window.location.href;
    });
    $(".radio").click(function(){
        $(".radio").removeAttr("checked");
        $(this).find('.radio').attr("checked","checked");
        $(this).find('.radio').prop("checked","checked");
    });
    //终止考试
    $(".stop").click(function () {
        //终止考试弹出层标题赋值
        var $student = $(this).parent().parent().siblings(".student").text();
        var $station = $(this).parent().parent().siblings(".station").text();
        $(".stuName").text($student);
        $(".stationName").text($station);
        //终止考试需要的当前数据
        var $examId = $(this).parent().attr("examId");
        var $studentId = $(this).parent().attr("studentId");
        var $examScreeningId = $(this).parent().attr("examScreeningId");
        var $stationId = $(this).parent().attr("stationId");
        var $userId = $(this).parent().attr("userId");
        var $examScreeningStudentId = $(this).parent().attr("examScreeningStudentId");
        $("#data").attr("examId",$examId);
        $("#data").attr("studentId",$studentId);
        $("#data").attr("examScreeningId",$examScreeningId);
        $("#data").attr("stationId",$stationId);
        $("#data").attr("userId",$userId);
        $("#data").attr("examScreeningStudentId",$examScreeningStudentId);
    });
    //终止考试提交
    $("#stopSure").click(function(){
        var stopUrl = pars.stopUrl;
        var description = $("#description option:selected").val();
        var reason = $(".textReason").val();//弃考理由
        $.ajax({
            url:stopUrl,
            cache:false,
            data:{examId:$("#data").attr("examId"),
                studentId:$("#data").attr("studentId"),
                examScreeningId:$("#data").attr("examScreeningId"),
                stationId:$("#data").attr("stationId"),
                userId:$("#data").attr("userId"),
                examScreeningStudentId:$("#data").attr("examScreeningStudentId"),
                status:3,
                description:description,
                reason:reason
            },
            dataType:"json",
            success:function(res){
                if(res){
                   window.location.href=window.location.href;
                    layer.msg("提交成功！",{skin:'msg-success',icon:1});
                }else{
                    layer.msg(res,{skin:'msg-error',icon:1});
                }
            }
        })
    });
    //确认弃考
    $(".abandon").click(function(){
        var stopUrl = pars.stopUrl;
        var $student = $(this).parent().parent().siblings(".student").text();
        var $idCard = $(this).parent().parent().siblings(".idCard").text();
        //弃考需要的当前数据
        var $examId = $(this).parent().attr("examId");
        var $studentId = $(this).parent().attr("studentId");
        var $examScreeningId = $(this).parent().attr("examScreeningId");
        var $stationId = $(this).parent().attr("stationId");
        var $userId = $(this).parent().attr("userId");
        var $examScreeningStudentId = $(this).parent().attr("examScreeningStudentId");
        var html  =  "确认当前考生"+$student+"（"+$idCard+")放弃考试？";
        $('#student').text("");
        
        $('#student').append(html);
        layer.open({
            type: 1, //page层
            area: ['500px', '245px'],
            title: '确认弃考',
            closeBtn: 1,
            shade: 0.6, //遮罩透明度
            moveType: 1, //拖拽风格，0是默认，1是传统拖动
            shift: 0, //0-6的动画形式，-1不开启
            shadeClose: true,
            content:$('#abandonExam'),
            // content:html,
            btn: ['保存','取消'], //按钮
            scrollbar: false,//禁用滚动条
            yes: function(index){
                var reason = $(".text").val();//弃考理由
                //var replace = $("input[name='radio'][checked]").val();//单选 option1  option2
                if ( reason.replace(/\s/g,'') == ''){
                    layer.alert('原因未填写！');
                    return false;
                }
                $.ajax({
                    url:stopUrl,
                    type:"get",
                    data:{
                        examId:$examId,
                        studentId:$studentId,
                        examScreeningId:$examScreeningId,
                        stationId:$stationId,
                        userId:$userId,
                        examScreeningStudentId:$examScreeningStudentId,
                        status:1,
                        reason :reason
                        // replace :replace
                    },
                    cache:false,
                    success:function(res){
                        if(res == true){
                            window.location.href = window.location.href;
                            layer.msg("提交成功！",{skin:'msg-success',icon:1});
                        }else{
                            window.location.href = window.location.href;
                            layer.msg(res,{skin:'msg-error',icon:1});
                        }
                    }
                })
                /*这里可以调用回调layer.msg('验证通过成功', {icon: 1,time: 2000})
                 layer.close(index);*/
            },
            cancel:function () {
                $(".text").val("");
            }
        });





    });
    //确认替考
    $(".replace").click(function(){
        var stopUrl = pars.stopUrl;
        var $student = $(this).parent().parent().siblings(".student").text();
        var $idCard = $(this).parent().parent().siblings(".idCard").text();
        //替考需要的当前数据
        var $examId = $(this).parent().attr("examId");
        var $studentId = $(this).parent().attr("studentId");
        var $examScreeningId = $(this).parent().attr("examScreeningId");
        var $stationId = $(this).parent().attr("stationId");
        var $userId = $(this).parent().attr("userId");
        var $examScreeningStudentId = $(this).parent().attr("examScreeningStudentId");

        console.log($userId);
        layer.confirm("确认当前考生"+$student+"（"+$idCard+"）替考？",function(){
            $.ajax({
                url:stopUrl,
                cache:false,
                data:{examId:$examId,
                    studentId:$studentId,
                    examScreeningId:$examScreeningId,
                    stationId:$stationId,
                    userId:$userId,
                    examScreeningStudentId:$examScreeningStudentId,
                    status:2
                },
                dataType:"json",
                success:function(res){
                    if(res == true){
                        window.location.href=window.location.href;
                        layer.msg("提交成功！",{skin:'msg-success',icon:1});
                    }else{
                        layer.msg(res,{skin:'msg-error',icon:1});
                    }
                }
            })
        });
    });
    //查看视频
    $(".look").click(function(){
        var videoUrl = pars.videoUrl;
        var examId = $(this).attr("examId");
        var stationId = $(this).attr("stationId");
        layer.open({
            type: 2,
            title: '视频查看',
            area: ['800px', '500px'],
            fix: false, //不固定
            maxmin: true,
            content: videoUrl+"?examId="+examId+"&stationId="+stationId
        })
    });
}

//考试监控迟到页面
function monitor_late(){
    //刷新
    $("#refresh").click(function(){
        window.location.href=window.location.href;
    });

    $(".radio").click(function(){
        $(".radio").removeAttr("checked");
        $(this).find('.radio').attr("checked","checked");
        $(this).find('.radio').prop("checked","checked");
    });

    //确认弃考
    $(".abandon").click(function(){
        var $student = $(this).parent().parent().siblings(".student").text();
        var $idCard = $(this).parent().parent().siblings(".idCard").text();
        var $examId = $(this).parent().attr("examId");
        var $studentId = $(this).parent().attr("studentId");
        var $examScreeningStudentId = $(this).parent().attr("examScreeningStudentId");
        var lateUrl = pars.lateUrl;
        var html  =  "确认当前考生"+$student+"（"+$idCard+")放弃考试？";
        $('#student').text("");
        
        $('#student').append(html);
        layer.open({
            type: 1, //page层
            area: ['500px', '245px'],
            title: '确认弃考',
            closeBtn: 1,
            shade: 0.6, //遮罩透明度
            moveType: 1, //拖拽风格，0是默认，1是传统拖动
            shift: 0, //0-6的动画形式，-1不开启
            shadeClose: true,
            content:$('#abandonExam'),
            // content:html,
            btn: ['保存','取消'], //按钮
            scrollbar: false,//禁用滚动条
            yes: function(index){
                var reason = $(".text").val();//弃考理由
                var replace = $("input[name='radio'][checked]").val();//单选 option1  option2
                if ( reason.replace(/\s/g,'') == ''|| replace == undefined){
                    layer.alert('原因未填写或处理方式未选择！');
                    return false;
                }
                $.ajax({
                    url:lateUrl,
                    type:"get",
                    data:{
                        examId:$examId,
                        studentId:$studentId,
                        examScreeningStudentId:$examScreeningStudentId,
                        reason :reason,
                        replace :replace
                    },
                    cache:false,
                    success:function(res){
                        if(res == true){
                            window.location.href = window.location.href;
                            layer.msg("提交成功！",{skin:'msg-success',icon:1});
                        }else{
                            window.location.href = window.location.href;
                            layer.msg(res,{skin:'msg-error',icon:1});
                        }
                    }
                });
                /*这里可以调用回调layer.msg('验证通过成功', {icon: 1,time: 2000})
                layer.close(index);*/
            },
            cancel:function () {
                $(".text").val("");
            }
        });

    });
}





