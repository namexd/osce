/**
 * Created by Administrator on 2015/12/21 0021.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "lab-add":lab_add();break; //lab-add页面
        case "lab-exist-detail":lab_exist_detail();break; //lab-exist-detail页面
        case "lab-exist-list":lab_exist_list();break;//lab-exist-list页面
        case "lab-history":lab_history();break;//lab-history页面
    }
});

function lab_add(){
    // var url = pars.ajaxurl;
    //时间选择
    var start = $('#start').val().substring(0,2);
    var end = $('#end').val().substring(0,2);

    $("#ionrange_4").ionRangeSlider({
        values: ["00:00","01:00","02:00","03:00","04:00","05:00","06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00","24:00"],
        type: "double",
        from:start,
        to:end,
        hasGrid: true,
        onChange: function(obj){        // function-callback, is called on every change

            $('#start').val(obj.fromValue);
            $('#end').val(obj.toValue);
        },

    });

    $('.cancel').click(function (){
        var url = pars.returnUrl;
        window.location.href = url;
    });
    /*{}{
     * 下面是进行插件初始化
     * 你只需传入相应的键值对
     * */
    $('#labForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {/*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '用户名不能为空'
                    }
                }
            },
            manager_name: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '负责人不能为空'
                    },
                    stringLength: {
                        min:2,
                        message: '用户名长度必须大于2'
                    }
                }
            },
            manager_mobile: {
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
            location: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '地址不能为空'
                    }
                }
            },
            opened: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请选择实验室类别'
                    },

                }
            },
            status: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请选择实验室状态'
                    },

                }
            },

            person_total: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '最大预约人数不能为空'
                    }
                }
            },
            detail: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '描述不能为空'
                    }
                }
            },

        }
    });

    $("#select_Category").change( function(){
        if($(this).val()=="Classroom") {
            $(".select-floor").show();
        }else{
            $(".select-floor").hide();
        }
    })
}

function lab_exist_detail(){
    var url=pars.ajaxurl;
    $(function(){
        $('.btn-primary').click(function () {
            history.go(-1);
        });
        //时间选择
        laydate(start);
        laydate(end);
        $('.cancel').click(function (){
            //history.go(-1);
            window.location.href = url;
        });
    })
    var start = {
        elem: "#start",
        format: "YYYY-MM-DD",
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
    };
    var end = {
        elem: "#end",
        format: "YYYY-MM-DD",
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
    };
    $("#select_Category").change( function(){
        if($(this).val()=="Classroom") {
            $(".select-floor").show();
        }else{
            $(".select-floor").hide();
        }
    })
}

function lab_exist_list(){
    $(function(){
        for(var i=0;i<$(".table tr").length;i++){
            $("#false-del").parents("tr").remove();//假删除数据隐藏
        }
        var idName;
        $(".table a").click(function(){
            idName=$(this).parents("tr").children(".idName").text();
            $("#Form1,#Form2,#Form3,#Form4,#Form5").css("display","none");
            var className=$(this).attr("id");
            switch(className){
                case "look":
                    look();
                    $("#Form2").css("display","block");
                    break;
                case "edit":
                    edit();
                    $("#Form3").css("display","block");
                    break;
                case "forbidden":
                    $("#Form4 .modal-body").text("确认禁用"+$(this).parents("tr").children(".userName").text()+"用户？")
                    $("#Form4").css("display","block");
                    break;
                case "del":
                    $("#Form5 .modal-body").text("确认删除"+$(this).parents("tr").children(".userName").text()+"用户？");
                    $("#Form5").css("display","block");
                    break;
            }
        });
        $("#new-add").click(function(){//新增
            $("#Form1,#Form2,#Form3,#Form4,#Form5").css("display","none");
            $("#Form1").css("display","block");
        });
        $(".btn-del").click(function(){//确认删除
            $.ajax({
                type:"get",
                url:"/msc/admin/user/student-trashed/"+idName,
                async:true
            });
            history.go(0);
        });
        $(".btn-forbidden,#recover").click(function(){//禁用恢复
            $.ajax({
                type:"get",
                url:"/msc/admin/user/student-status/"+idName,
                async:true
            });
            history.go(0);
        })
        $(".btn-edit").click(function(){//确认修改验证
            var editName=$.trim($(".edit-name").val());
            var editCode=$.trim($(".edit-code").val());
            var editProfessional_name=$.trim($(".edit-professional_name").val());
            var editMobile=$.trim($(".edit-mobile").val());
            var editCard=$.trim($(".edit-idcard").val());
            var reg=/^1[3|5|8]{1}[0-9]{9}$/;
            if(editName==""){
                layer.tips('用户名不能为空', '.edit-name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editCode==""){
                layer.tips('学号不能为空', '.edit-code', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editProfessional_name==""){
                layer.tips('专业不能为空', '.edit-professional_name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editMobile==""){
                layer.tips('手机号不能为空', '.edit-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(!reg.test(editMobile)){
                layer.tips('请输入正确的手机号码', '.edit-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editCard==""){
                layer.tips('证件号不能为空', '.edit-idcard', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            $("#Form3").submit();
        });
        $(".btn-new-add").click(function(){//新增学生验证
            var addName=$.trim($(".add-name").val());
            var addCode=$.trim($(".add-code").val());
            var addProfession_name=$.trim($(".add-profession_name").val());
            var addMobile=$.trim($(".add-mobile").val());
            var addCard=$.trim($(".add-card").val());
            var reg=/^1[3|5|8]{1}[0-9]{9}$/;
            if(addName==""){
                layer.tips('用户名不能为空', '.add-name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addCode==""){
                layer.tips('学号不能为空', '.add-code', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addProfession_name==""){
                layer.tips('专业不能为空', '.add-profession_name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addMobile==""){
                layer.tips('手机号不能为空', '.add-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(!reg.test(addMobile)){
                layer.tips('请输入正确的手机号码', '.add-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addCard==""){
                layer.tips('证件号不能为空', '.add-card', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            $("#Form1").submit();
        })
        function look(){//查看
            $.ajax({
                type:"get",
                url: "/msc/admin/user/student-item/"+idName,
                async:false,
                success:function(res){
                    var data=JSON.parse(res);
                    console.log(data);
                    $(".look-name").val(data.name);//姓名
                    $(".look-code").val(data.code);//学号
                    if(data.gender=="男"){
                        $(".look-man").attr("checked","checked");
                    }else if(data.gender=="女"){
                        $(".look-woman").attr("checked","checked");
                    }
                    $(".look-grade").val(data.grade);//年级
                    $(".look-student_type").find("option[text='"+data.student_type+"']").attr(".look-student_type",true);//类别
                    $(".look-profession_name").val(data.profession_name);//专业
                    $(".look-mobile").val(data.mobile);//手机
                    $(".look-card").val(data.idcard);//证件号码

                }
            });
        }
        function edit(){//修改
            $.ajax({
                type:"get",
                url:"/msc/admin/user/student-edit/"+idName,
                async:true,
                success:function(res){
                    var data=JSON.parse(res);
                    $(".edit-name").val(data.name);//姓名
                    $(".edit-hidden-name").val(idName);
                    $(".edit-code").val(data.code);//学号
                    if(data.gender=="男"){
                        $(".edit-man").attr("checked","checked");
                    }else if(data.gender=="女"){
                        $(".edit-woman").attr("checked","checked");
                    }
                    $(".edit-grade").val(data.grade);//年级
                    $(".edit-student_type").find("option[text='"+data.student_type+"']").attr(".edit-student_type",true);//类别
                    $(".edit-professional_name").val(data.profession_name)//专业
                    $(".edit-mobile").val(data.mobile);//手机
                    $(".edit-card").val(data.idcard);//证件号码
                }
            });
        }

        $("#in").click(function(){
            $("#leading-in").click();
        })
        $("#leading-in").change(function(){
            var str=$("#leading-in").val().substring($("#leading-in").val().lastIndexOf(".")+1);
            if(str!="xlsx"){
                layer.alert(
                    "请上传正确的文件格式？",
                    {title:["温馨提示","font-size:16px;color:#408aff"]}
                );
            }else{
                $.ajaxFileUpload({
                    type:"post",
                    url:'/msc/admin/user/import-student-user',
                    fileElementId:'leading-in',//必须要是 input file标签 ID
                    success: function (data, status){

                    },
                    error: function (data, status, e){
                        console.log("失败");
                        layer.alert(
                            "上传失败！",
                            {title:["温馨提示","font-size:16px;color:#408aff"]}
                        );
                    }
                });
            }
        })
        $(".leading-out").click(function(){
            var keyword=$("#keyword").val();
            $.ajax({
                type:'get',
                url:'/msc/admin/user/export-student-user',
                data:{
                    keyword : keyword,
                },
                async:true,
                success:function(res){
                    if(res=="1") {
                        window.location.href = "/msc/admin/user/export-student-user";
                    }
                }
            });
        })

    })
}

function lab_history(){
    var start = {
        elem: "#start",
        format: "YYYY/MM/DD",
        min: '1970-01-01',
        max: "2099-12-31",
        istime: false,
        istoday: false,
        choose: function (a) {
            /*end.min = a;
             end.start = a*/
        }
    };

    $(function(){
        laydate(start);
       /* $(".date").text($(".date").text().substring(0,10));
        $(".time").text($(".time").text().substring(11,19));*/
    })
}