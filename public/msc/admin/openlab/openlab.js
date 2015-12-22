/**
 * Created by Administrator on 2015/12/21 0021.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "lab-add":lab_add();break; //lab-addҳ��
        case "lab-exist-detail":lab_exist_detail();break; //lab-exist-detailҳ��
        case "lab-exist-list":lab_exist_list();break;//lab-exist-listҳ��
        case "lab-history":lab_history();break;//lab-historyҳ��
    }
});

function lab_add(){
    $(function(){
        var url = pars.ajaxurl;
        //ʱ��ѡ��
        laydate(start);
        laydate(end);
        $('.cancel').click(function (){
            //history.go(-1);
            window.location.href = url;
        });
        /*{}{
         * �����ǽ��в����ʼ��
         * ��ֻ�贫����Ӧ�ļ�ֵ��
         * */
        $('#labForm').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*�����ͬ״̬����ʾͼƬ����ʽ*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*��֤*/
                name: {/*����username��input nameֵ��Ӧ*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*�ǿ���ʾ*/
                            message: '�û�������Ϊ��'
                        }
                    }
                },
                manager_name: {
                    validators: {
                        notEmpty: {/*�ǿ���ʾ*/
                            message: '�����˲���Ϊ��'
                        },
                        stringLength: {
                            min:2,
                            message: '�û������ȱ������2'
                        }
                    }
                },
                manager_mobile: {
                    validators: {
                        notEmpty: {/*�ǿ���ʾ*/
                            message: '�ֻ����벻��Ϊ��'
                        },
                        stringLength: {
                            min: 11,
                            max: 11,
                            message: '������11λ�ֻ�����'
                        },
                        regexp: {
                            regexp: /^1[3|5|8]{1}[0-9]{9}$/,
                            message: '��������ȷ���ֻ�����'
                        }
                    }
                },
                address: {
                    validators: {
                        notEmpty: {/*�ǿ���ʾ*/
                            message: '��ַ����Ϊ��'
                        }
                    }
                },
                type: {
                    validators: {
                        notEmpty: {/*�ǿ���ʾ*/
                            message: '���Ͳ���Ϊ��'
                        }
                    }
                },
                maxorder: {
                    validators: {
                        notEmpty: {/*�ǿ���ʾ*/
                            message: '���ԤԼ��������Ϊ��'
                        }
                    }
                },
                detail: {
                    validators: {
                        notEmpty: {/*�ǿ���ʾ*/
                            message: '��������Ϊ��'
                        }
                    }
                },  begindate: {
                    validators: {
                        notEmpty: {
                            message: '��ʼʱ�䲻��Ϊ��'
                        },
                    }
                },
                enddate: {
                    validators: {
                        notEmpty: {
                            /*�ǿ���ʾ*/
                            message: '����ʱ�䲻��Ϊ��'
                        },
                        callback: {
                            message: '�������ڲ���С�ڿ�ʼ����',
                            callback: function (value, validator, $field) {
                                var begin = $('#star').val();
                                $('#star').keypress();
                                var b_date = begin.replace(/-/g,"");
                                var e_date = value.replace(/-/g,"");
                                return parseInt(e_date) >= parseInt(b_date);
                            }
                        }
                    }
                },

            }
        });
    })
    var start = {
        elem: "#start",
        format: "YYYY-MM-DD",
        max: "2099-06-16",
        istoday: true,
        istime: true,
        istoday: false,
        choose: function(dates){ //ѡ������ڵĻص�
            $("#start").val(dates);
            $('#labForm').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*�����ͬ״̬����ʾͼƬ����ʽ*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*��֤*/
                    begindate: {
                        validators: {
                            notEmpty: {
                                message: '��ʼʱ�䲻��Ϊ��'
                            },
                        }
                    }

                }
            });
        }
    };
    var end = {
        elem: "#end",
        format: "YYYY-MM-DD",
        max: "2099-06-16",
        istoday: true,
        istime: true,
        istoday: false,
        choose: function(dates){ //ѡ������ڵĻص�
            $("#end").val(dates);
            $('#labForm').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*�����ͬ״̬����ʾͼƬ����ʽ*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*��֤*/
                    enddate: {
                        validators: {
                            notEmpty: {
                                /*�ǿ���ʾ*/
                                message: '����ʱ�䲻��Ϊ��'
                            },
                            callback: {
                                message: '�������ڲ���С�ڿ�ʼ����',
                                callback: function (value, validator, $field) {
                                    var begin = $('#star').val();
                                    $('#star').keypress();
                                    var b_date = begin.replace(/-/g,"");
                                    var e_date = value.replace(/-/g,"");
                                    return parseInt(e_date) >= parseInt(b_date);
                                }
                            }
                        }
                    },

                }
            });
        }
    };
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
        //ʱ��ѡ��
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
            $("#false-del").parents("tr").remove();//��ɾ����������
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
                    $("#Form4 .modal-body").text("ȷ�Ͻ���"+$(this).parents("tr").children(".userName").text()+"�û���")
                    $("#Form4").css("display","block");
                    break;
                case "del":
                    $("#Form5 .modal-body").text("ȷ��ɾ��"+$(this).parents("tr").children(".userName").text()+"�û���");
                    $("#Form5").css("display","block");
                    break;
            }
        });
        $("#new-add").click(function(){//����
            $("#Form1,#Form2,#Form3,#Form4,#Form5").css("display","none");
            $("#Form1").css("display","block");
        });
        $(".btn-del").click(function(){//ȷ��ɾ��
            $.ajax({
                type:"get",
                url:"/msc/admin/user/student-trashed/"+idName,
                async:true
            });
            history.go(0);
        });
        $(".btn-forbidden,#recover").click(function(){//���ûָ�
            $.ajax({
                type:"get",
                url:"/msc/admin/user/student-status/"+idName,
                async:true
            });
            history.go(0);
        })
        $(".btn-edit").click(function(){//ȷ���޸���֤
            var editName=$.trim($(".edit-name").val());
            var editCode=$.trim($(".edit-code").val());
            var editProfessional_name=$.trim($(".edit-professional_name").val());
            var editMobile=$.trim($(".edit-mobile").val());
            var editCard=$.trim($(".edit-idcard").val());
            var reg=/^1[3|5|8]{1}[0-9]{9}$/;
            if(editName==""){
                layer.tips('�û�������Ϊ��', '.edit-name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editCode==""){
                layer.tips('ѧ�Ų���Ϊ��', '.edit-code', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editProfessional_name==""){
                layer.tips('רҵ����Ϊ��', '.edit-professional_name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editMobile==""){
                layer.tips('�ֻ��Ų���Ϊ��', '.edit-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(!reg.test(editMobile)){
                layer.tips('��������ȷ���ֻ�����', '.edit-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(editCard==""){
                layer.tips('֤���Ų���Ϊ��', '.edit-idcard', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            $("#Form3").submit();
        });
        $(".btn-new-add").click(function(){//����ѧ����֤
            var addName=$.trim($(".add-name").val());
            var addCode=$.trim($(".add-code").val());
            var addProfession_name=$.trim($(".add-profession_name").val());
            var addMobile=$.trim($(".add-mobile").val());
            var addCard=$.trim($(".add-card").val());
            var reg=/^1[3|5|8]{1}[0-9]{9}$/;
            if(addName==""){
                layer.tips('�û�������Ϊ��', '.add-name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addCode==""){
                layer.tips('ѧ�Ų���Ϊ��', '.add-code', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addProfession_name==""){
                layer.tips('רҵ����Ϊ��', '.add-profession_name', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addMobile==""){
                layer.tips('�ֻ��Ų���Ϊ��', '.add-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(!reg.test(addMobile)){
                layer.tips('��������ȷ���ֻ�����', '.add-mobile', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            if(addCard==""){
                layer.tips('֤���Ų���Ϊ��', '.add-card', {
                    tips: [1, '#408AFF'],
                    time: 4000
                });
                return false;
            }
            $("#Form1").submit();
        })
        function look(){//�鿴
            $.ajax({
                type:"get",
                url: "/msc/admin/user/student-item/"+idName,
                async:false,
                success:function(res){
                    var data=JSON.parse(res);
                    console.log(data);
                    $(".look-name").val(data.name);//����
                    $(".look-code").val(data.code);//ѧ��
                    if(data.gender=="��"){
                        $(".look-man").attr("checked","checked");
                    }else if(data.gender=="Ů"){
                        $(".look-woman").attr("checked","checked");
                    }
                    $(".look-grade").val(data.grade);//�꼶
                    $(".look-student_type").find("option[text='"+data.student_type+"']").attr(".look-student_type",true);//���
                    $(".look-profession_name").val(data.profession_name);//רҵ
                    $(".look-mobile").val(data.mobile);//�ֻ�
                    $(".look-card").val(data.idcard);//֤������

                }
            });
        }
        function edit(){//�޸�
            $.ajax({
                type:"get",
                url:"/msc/admin/user/student-edit/"+idName,
                async:true,
                success:function(res){
                    var data=JSON.parse(res);
                    $(".edit-name").val(data.name);//����
                    $(".edit-hidden-name").val(idName);
                    $(".edit-code").val(data.code);//ѧ��
                    if(data.gender=="��"){
                        $(".edit-man").attr("checked","checked");
                    }else if(data.gender=="Ů"){
                        $(".edit-woman").attr("checked","checked");
                    }
                    $(".edit-grade").val(data.grade);//�꼶
                    $(".edit-student_type").find("option[text='"+data.student_type+"']").attr(".edit-student_type",true);//���
                    $(".edit-professional_name").val(data.profession_name)//רҵ
                    $(".edit-mobile").val(data.mobile);//�ֻ�
                    $(".edit-card").val(data.idcard);//֤������
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
                    "���ϴ���ȷ���ļ���ʽ��",
                    {title:["��ܰ��ʾ","font-size:16px;color:#408aff"]}
                );
            }else{
                $.ajaxFileUpload({
                    type:"post",
                    url:'/msc/admin/user/import-student-user',
                    fileElementId:'leading-in',//����Ҫ�� input file��ǩ ID
                    success: function (data, status){

                    },
                    error: function (data, status, e){
                        console.log("ʧ��");
                        layer.alert(
                            "�ϴ�ʧ�ܣ�",
                            {title:["��ܰ��ʾ","font-size:16px;color:#408aff"]}
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
        format: "YYYY/MM/DD hh:mm:ss",
        min: laydate.now(),
        max: "2099-06-16 23:59:59",
        istime: true,
        istoday: false,
        choose: function (a) {
            /*end.min = a;
             end.start = a*/
        }
    };

    $(function(){
        laydate(start);
        $(".date").text($(".date").text().substring(0,10));
        $(".time").text($(".time").text().substring(11,19));
    })
}