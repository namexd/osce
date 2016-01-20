/**
 * Created by Administrator on 2016/1/7 0007.
 */

var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "ban_maintain":ban_maintain();break; //楼栋管理页面
        case "lab_maintain":lab_maintain();break; //实验室管理页面
        case "resource_maintain":resource_maintain();break; //实验室资源管理页面
    }

});
//楼栋管理页面
function ban_maintain(){
    $(function(){
//            删除
        $(".delete").click(function(){
            var this_id = $(this).attr('data');
            var url = "/msc/admin/floor/delete-floor?id="+this_id;
            //询问框
            layer.confirm('您确定要删除该楼栋？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=url;
            });
        });
//            停用
        $(".stop").click(function(){
            var this_id = $(this).attr('data');
            var type = $(this).attr('data-type');
            var url = "/msc/admin/floor/stop-floor?id="+this_id+"&type="+type;
            var str = '';
            if(type == 1){
                str = '您确定要启用该楼栋？';
            }else{

                str = '您确定要停用该楼栋？';
            }
            //询问框
            layer.confirm(str, {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=url;
            });
        });
        //地上、地下层数不能为0
        $("#form_box").delegate(".floor_top","change",function(){
            if($(this).val()<0){
                $(this).val("0");
            }
        });
        $("#form_box").delegate(".floor_buttom","change",function(){
            if($(this).val()<0){
                $(this).val("0");
            }
        });
//            新增
        var $add_select=$(".add_select").html();
        $("#add_ban").click(function(){
            var $addUrl=$("#addUrl").val();
            $("#edit_from").remove();
            $("#add_from").hide();
            $("#form_box").append('<form class="form-horizontal" id="edit_from" novalidate="novalidate" action='+$addUrl+' method="post"> ' +
                '<div class="modal-header"> ' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
                '<h4 class="modal-title" id="myModalLabel">新增楼栋</h4> ' +
                '</div> ' +
                '<div class="modal-body"> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>楼栋名称</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control name add-name" name="name" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地上)</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="number" class="form-control name add-name floor_top" name="floor_top" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地下)</label>' +
                '<div class="col-sm-9"> ' +
                '<input type="number" class="form-control name add-name floor_buttom" name="floor_buttom" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label">地址</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control describe add-describe" name="address" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label">所属分院</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b add_select" name="school_id"> ' +$add_select+
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b " name="status"> ' +
                '<option value="-1">请选择状态</option> ' +
                '<option value="1">正常</option> ' +
                '<option value="0">停用</option> ' +
                '</select> ' +
                '</div> ' +
                '</div> ' +
                '<div class="hr-line-dashed"></div> ' +
                '<div class="form-group"> ' +
                '<div class="col-sm-4 col-sm-offset-2 right"> ' +
                '<button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
                '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
                '</div> ' +
                '</div> ' +
                '</div> ' +
                '</form>');
            add_form();
        });
//            编辑表单验证
        function edit_form(){
            $('#add_from').bootstrapValidator({
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
                                message: '楼栋名称不能为空'
                            }
                        }
                    },
                    floor_top: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地上层数不能为空'
                            }
                        }
                    },
                    floor_bottom: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地下层数不能为空'
                            }
                        }
                    },
                    address: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地址不能为空'
                            }
                        }
                    },
                    school_id: {
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '所属分院不能为空'
                            }
                        }
                    },
                    status: {
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '请选择状态'
                            }

                        }
                    }
                }
            });
        }
        //            新增表单验证
        function add_form(){
            $('#edit_from').bootstrapValidator({
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
                                message: '楼栋名称不能为空'
                            }
                        }
                    },
                    floor_top: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地上层数不能为空'
                            }
                        }
                    },
                    floor_bottom: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地下层数不能为空'
                            }
                        }
                    },
                    address: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地址不能为空'
                            }
                        }
                    },
                    school_id: {
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '所属分院不能为空'
                            }
                        }
                    },
                    status: {
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '请选择状态'
                            }

                        }
                    }
                }
            });
        }
        //编辑
       // var $edit_select=$(".edit_select").html();
        $('.edit').click(function () {
            var str1 = '';
            var str2 = '';
            var $editUrl=$("#editUrl").val();
            $("#edit_from").hide();
            var school_id = $(this).parent().parent().find('.sname').attr('data');
            $('.edit_select option').each(function(i){
                if($(this).val() == school_id){
                    $(this).attr('selected','selected');
                }
            });
            var status = $(this).parent().parent().find('.status').attr('data');
            if(status == 1){
                str2 = '';
                str1 = 'selected="selected"';
            }else{
                str1 = '';
                str2 = 'selected="selected"';
            }
            var $edit_select=$(".edit_select").html();
            $("#add_from").remove();
            $("#form_box").append('<form class="form-horizontal" id="add_from" novalidate="novalidate" action='+$editUrl+' method="post"> ' +
                '<div class="modal-header"> ' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
                '<h4 class="modal-title" id="myModalLabel">编辑楼栋</h4> ' +
                '</div> ' +
                '<div class="modal-body"> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>楼栋名称</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control name add-name" name="name" value="'+$(this).parent().parent().find('.name').html()+'" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地上)</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="number" class="form-control name add-name floor_top" name="floor_top" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层数(地下)</label>' +
                '<div class="col-sm-9"> ' +
                '<input type="number" class="form-control name add-name floor_buttom" name="floor_buttom" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label">地址</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control describe add-describe" name="address" value="'+$(this).parent().parent().find('.address').html()+'"/> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label">所属分院</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b edit_select" name="school_id"> ' +$edit_select+
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b " name="status"> ' +
                '<option value="1" '+str1+'>正常</option> ' +
                '<option value="0"'+str2+'>停用</option> ' +
                '</select> ' +
                '</div> ' +
                '</div> ' +
                '<div class="hr-line-dashed"></div> ' +
                '<div class="form-group"> ' +
                '<div class="col-sm-4 col-sm-offset-2 right"> ' +
                '<button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
                '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
                '</div> ' +
                '</div> ' +
                '</div> ' +
                '</form>');
            edit_form();
            $('.lname').val($(this).parent().parent().find('.name').html());
            $('.floor_top').val($(this).parent().parent().find('.floor').attr('data'));
            $('.floor_buttom').val($(this).parent().parent().find('.floor').attr('data-b'));
            $('.address').val($(this).parent().parent().find('.address').html());
            var sname = $(this).parent().parent().find('.sname').html();
            var status = '';
            if($(this).parent().parent().find('.status').html() == '正常'){
                status = 1;
            }else{
                status = 0;
            }
            $('.school option').each(function(){
                if($(this).html() == sname){
                    $(this).attr('selected','selected');
                }
            });

            $('.state option').each(function(){
                if($(this).val() == status){
                    $(this).attr('selected','selected');
                }
            });
            $('#myModalLabel').html('编辑楼栋');
            var id = $(this).attr("data");
            $('#add_from').append('<input type="hidden" name="id" value="'+id+'">');
        });

    })
}
//实验室管理页面
function lab_maintain(){
    //            删除
    $(".delete").click(function(){
        var this_id = $(this).attr('data');
        var url = "/msc/admin/laboratory/delete-lab?id="+this_id;
        //询问框
        layer.confirm('您确定要删除该实验室？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });
//            停用
    $(".stop").click(function(){
        var this_id = $(this).attr('data');
        var type = $(this).attr('data-type');
        var url = "/msc/admin/laboratory/stop-lab?id="+this_id+"&type="+type;
        var str = '';
        if(type == 0){
            str = '您确定要停用实验室？';
        }else{
            str = '您确定要启用实验室？';
        }
        //询问框
        layer.confirm(str, {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });
//            新增验证
    function add_form(){
        $('#add_from').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                hospital: {
                    message: 'The hospital is not valid',
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '所属分院不能为空'
                        }
                    }
                },
                building: {
                    message: 'The building is not valid',
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '教学楼不能为空'
                        }
                    }
                },
                floor: {
                    message: 'The floor is not valid',
                    validators: {
                        regexp: {
                            regexp: /^(?!-9999).*$/,
                            message: '楼层不能为空'
                        }
                    }
                },
                name: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '实验室名称不能为空'
                        }
                    }
                },
                code: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '房号不能为空'
                        }
                    }
                },
                total: {/*键名username和input name值对应*/
                    validators: {
                        notEmpty: {
                            /*非空提示*/
                            message: '实验室容量不能为空'
                        },
                        regexp: {
                            regexp: /^(?:[1-9]\d?|[1234]\d{2}|500)$/,
                            message: '容量最大为500人'
                        }
                    }
                },
                manager_user_id:{
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '管理员不能为空'
                        }
                    }
                },
                open_type:{
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '实验室性质不能为空'
                        }
                    }
                },
                status: {
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '状态不能为空'
                        }
                    }
                }
            }
        });
    }
    //            新增
    var add_hospital=$(".add_hospital").html();
    var add_master=$(".add_master").html();
    $("#add_lab").click(function(){
        var $addUrl=$("#addUrl").val();
        $("#edit_from").hide();
        $("#add_from").remove();
        $("#form_box").append('<form class="form-horizontal" id="add_from" novalidate="novalidate" action="'+$addUrl+'" method="post"> ' +
            '<div class="modal-header"> ' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
            '<h4 class="modal-title" id="myModalLabel">新增实验室</h4> ' +
            '</div> ' +
            '<div class="modal-body"> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>所属分院</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b oldschool edit_hospital school" name="hospital"> ' +
            '<option value="-1">请选择所属分院</option> ' +add_hospital+
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>教学楼</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b oldlocal local" name="building"> ' +
            '<option value="-1">请选择教学楼</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b oldfloor floor" name="floor"> ' +
            '<option value="-9999">请选择楼层</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>实验室名称</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="name" name="name" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">简称</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="short_name" name="short_name" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">英文全称</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="enname" name="enname" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">英文缩写</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="short_enname" name="short_enname" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>房号</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="code" name="code" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>容量</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control describe add-describe" id="total" name="total" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>管理员</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b teacher edit_master" name="manager_user_id"> ' +
            '<option value="-1">点击选择</option> ' +add_master+
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>实验室性质</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b opentype" name="open_type"> ' +
            '<option value="-1">点击选择</option> ' +
            '<option value="1">实验室</option> ' +
            '<option value="2">准备间</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category"   class="form-control m-b sta" name="status"> ' +
            '<option value="-1">请选择状态</option> ' +
            '<option value="1">正常</option> ' +
            '<option value="0">停用</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="hr-line-dashed"></div> ' +
            '<div class="form-group"> ' +
            '<div class="col-sm-4 col-sm-offset-2 right"> ' +
            '<button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
            '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
            '</div> ' +
            '</div> ' +
            '</div> ' +
            '</form>');
        $('#add_from').delegate('.school','change',function(){
            var id = $(this).val();
            var opstr = '<option value="-1">请选择教学楼</option>';
            $.ajax({
                type: "POST",
                url: pars.getLocalUrl,
                data: {id:id},
                success: function(msg){
                    if(msg){
                        $(msg).each(function(i,k){
                            opstr += '<option value="'+k.id+'">'+k.name+'</option>';
                        });
                        $('.local').html(opstr);
                    }
                }
            });
        });
        $('#add_from').delegate('.local','change',function(){
            var id = $(this).val();
            var opstr = '<option value="-9999">请选择楼层</option>';
            $.ajax({
                type: "POST",
                url: pars.getFloorUrl,
                data: {id:id},
                success: function(msg){
                    if(msg){

                        $.each($(msg),function(i,n){
                            opstr += '<option value="'+n+'">'+n+'楼</option>';
                        });
                        $('.floor').html(opstr);
                    }
                }
            });
        });
        $('.oldlocal').change(function(){
            var id = $(this).val();
            var opstr = '<option value="-9999">请选择楼层</option>';
            $.ajax({
                type: "POST",
                url: pars.getFloorUrl,
                data: {id:id},
                success: function(msg){
                    if(msg){

                        $.each($(msg),function(i,n){
                            opstr += '<option value="'+n+'">'+n+'楼</option>';
                        });
                        $('.floor').html(opstr);
                    }
                }
            });
        });
        add_form();
    });
    //编辑验证
    function edit_form(){
        $("#edit_from").bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                hospital: {
                    message: 'The hospital is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '所属分院不能为空'
                        }
                    }
                },
                building: {
                    message: 'The building is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '教学楼不能为空'
                        }
                    }
                },
                floor: {
                    message: 'The floor is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '楼层不能为空'
                        }
                    }
                },
                name: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '实验室名称不能为空'
                        }
                    }
                },
                code: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '房号不能为空'
                        }
                    }
                },
                total: {/*键名username和input name值对应*/
                    validators: {
                        notEmpty: {
                            /*非空提示*/
                            message: '实验室容量不能为空'
                        },
                        regexp: {
                            regexp: /^(?:[1-9]\d?|[1234]\d{2}|500)$/,
                            message: '容量最大为500人'
                        }
                    }
                },
                manager_user_id:{
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '管理员不能为空'
                        }
                    }
                },
                open_type:{
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '实验室性质不能为空'
                        }
                    }
                },
                status: {
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '状态不能为空'
                        }
                    }
                }
            }
        });
    }
//           编辑
    var edit_hospital=$(".edit_hospital").html();
    var edit_master=$(".edit_master").html();
    $('.update').click(function () {
        var $editUrl=$("#editUrl").val();
        $("#add_from").hide();
        $("#edit_from").remove();
        $("#form_box").append('<form class="form-horizontal" id="edit_from" novalidate="novalidate" action="'+$editUrl+'" method="post"> " ' +
            '<div class="modal-header"> ' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
            '<h4 class="modal-title" id="myModalLabel">编辑实验室</h4> ' +
            '</div> ' +
            '<div class="modal-body"> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>所属分院</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b oldschool edit_hospital" name="hospital"> ' +edit_hospital+
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>教学楼</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b oldlocal local" name="building"> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>楼层</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b oldfloor floor" name="floor"> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>实验室名称</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="name" name="name" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">简称</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="short_name" name="short_name" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">英文全称</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="enname" name="enname" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">英文缩写</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="short_enname" name="short_enname" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>房号</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control name add-name" id="code" name="code" value="" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>容量</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control describe add-describe" id="total" name="total" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">管理员</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b teacher edit_master" name="manager_user_id"> ' +edit_master+
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">实验室性质</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category" class="form-control m-b opentype" name="open_type"> ' +
            '<option value="1">实验室</option> ' +
            '<option value="2">准备间</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category"   class="form-control m-b sta" name="status"> ' +
            '<option value="1">正常</option> ' +
            '<option value="0">停用</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="hr-line-dashed"></div> ' +
            '<div class="form-group"> ' +
            '<div class="col-sm-4 col-sm-offset-2 right"> ' +
            '<button class="btn btn-primary"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
            '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
            '</div> ' +
            '</div> ' +
            '</div> ' +
            '</form>');
        edit_form();
        var updateobj = $(this);
        $('#code').val($(this).parent().parent().find('.code').html());
        $('input[name=floor]').val($(this).parent().parent().find('.floors').attr('data-b'));
        $('#name').val($(this).parent().parent().find('.name').html());
        $('#short_name').val($(this).parent().parent().find('.short_name').val());
        $('#enname').val($(this).parent().parent().find('.enname').val());
        $('#short_enname').val($(this).parent().parent().find('.short_enname').val());
        $('#total').val($(this).parent().parent().find('.total').val());
        $('.oldschool option').each(function(){
            if($(this).val() == $(updateobj).parent().parent().find('.lname').attr('data')){
                $(this).attr('selected','selected');
            }
        });
        var id = $(this).attr("data");
        $.ajax({
            type: "POST",
            url: pars.getFloorUrl,
            data: {id:id,type:1},
            success: function(msg){
                var opstr = '';
                if(msg){
                    $.each($(msg),function(i,n){
                        if(n == $(updateobj).parent().parent().find('.floors').html()){
                            opstr += '<option value="'+n+'" selected="selected">'+n+'楼</option>';
                        }else{
                            opstr += '<option value="'+n+'">'+n+'楼</option>';
                        }
                    });
                    $('.oldfloor').html(opstr);
                }
            }
        });
        $('.oldschool').change(function(){
            var id = $(this).val();
            var opstr = '<option value="-1">请选择教学楼</option>';
            $.ajax({
                type: "POST",
                url: pars.getLocalUrl,
                data: {id:id},
                success: function(msg){
                    if(msg){
                        $(msg).each(function(i,k){

                            opstr += '<option value="'+k.id+'">'+k.name+'</option>';
                        });
                        $('.local').html(opstr);
                    }
                }
            });
        });
        $.ajax({
            type: "POST",
            url: pars.getLocalUrl,
            data: {id:$(updateobj).parent().parent().find('.lname').attr('data'),type:1},
            success: function(msg){
                console.log(msg);
                var opstr = '';
                if(msg){
                    $.each($(msg),function(i,n){
                        if(n.id == $(updateobj).parent().parent().find('.lname').attr('data-local')){
                            opstr += '<option value="'+ n.id+'" selected="selected">'+ n.name+'</option>';
                        }else{
                            opstr += '<option value="'+ n.id+'">'+ n.name+'</option>';
                        }

                    });
                    $('.oldlocal').html(opstr);
                }
            }
        });
        var status = $(this).parent().parent().find('.status').attr('data');
        $('.sta option').each(function(){
            if($(this).val() == status){
                $(this).attr('selected','selected');
            }
        });
        var teach = $(this).parent().parent().find('.tname').attr('data');
        $('.teacher option').each(function(){
            if($(this).val() == teach){
                $(this).attr('selected','selected');
            }
        });
        var open_type = $(this).parent().parent().find('.open_type').attr('data');
        if(open_type){
            $('.opentype option').each(function(){
                if($(this).val() == open_type){
                    $(this).attr('selected','selected');
                }
            });
        }
        $('#edit_from').append('<input type="hidden" name="id" value="'+id+'">');
    });
}
function resource_maintain(){
    $(document).ajaxSuccess(function(event, request, settings) {
        //楼栋选项卡切换
        ban();
        //实验室数据加载
        labdata();
    });
//            楼栋选项卡切换
    function ban(){
        $(".list-group-parent").unbind().click(function(){
            $(this).addClass("checked").parent(".list-group").siblings().children(".list-group-parent").removeClass("checked");
            if($(this).next(".lab_num").children(".list-group-child").size()!="0"){
                $(this).next(".lab_num").slideToggle("200");
                $(this).children(".fa").toggleClass("deg");
            }
            if($(this).parent().next(".list-group").length=="1"){
                $(this).next(".lab_num").children().last().addClass("border-bottom");
            }
        });
        $(".list-group-child").unbind().click(function(){
            $(".list-group-parent").removeClass("checked");
            $(".list-group-child").removeClass("checked");
            $(this).addClass("checked");
        });
    }

//            新增、编辑切换
    $("#edit").click(function(){
        $("#add_device_form").hide();
        $("#edit_form").show();
    });
//            楼栋数据绑定
    $("#ban_select").change(function(){
        var $treeview=$(".treeview");
        $treeview.empty();
        var $thisId=$(this).val();
        var url="/msc/admin/ladMaintain/floor-lab?lid="+$thisId;
        $.ajax({
            type:"get",
            url:url,
            cache:false,
            success:function(result){
                $(result).each(function(){
                    if(this.lab.length>0){
                        $treeview.append( "<div class='list-group' style='margin-bottom: 0' id='"+this.floor+"'>" +
                            "<div class='list-group-item list-group-parent'>"
                            +this.floor+"楼"
                            +"</div>"
                            +"<div class='lab_num'></div>"
                            +"</div>"
                        );
                    }

                    if(this.lab!=""){
                        $(this.lab).each(function(){
                            $(".treeview #"+ this.floor +" .lab_num").append("<div class='list-group-item list-group-child  labdetail'  data='"+this.total+"' lab_id='"+this.id+"'>"+this.name+"</div>")
                        });
                        $(".treeview #"+ this.floor +" .list-group-parent").append("<i class='fa fa-angle-right right'></i>");
                    }

                })

            }
        })
    });
//              人数数量
    $('.ibox-content').delegate('.labdetail','click',function(){
        var total = $(this).attr('data');
        if(total == 'null'){
            total = 0;
        }
        var labname = $(this).html();
        $('.labname').html(labname);
        $('.labtotal').html(total+'人');
        $('#add_device').removeAttr('disabled');
        $('#lab_id').val($(this).attr('lab_id'));
    });
//            新增弹出层选项框
    $(".check_all").click(function(){
        if($(this).children(".check_icon").hasClass("check")){
            $(this).children(".check_icon").removeClass("check");
            $(".check_one").children(".check_icon").removeClass("check");
        }else{
            $(this).children(".check_icon").addClass("check");
            $(".check_one").children(".check_icon").addClass("check");
        }
    });
    $("#add_device_form").delegate(".check_one","click",function(){
        if($(this).children(".check_icon").hasClass("check")){
            $(this).children(".check_icon").removeClass("check");
            $(".check_all").children(".check_icon").removeClass("check");
        }else{
            $(this).children(".check_icon").addClass("check");
            if($(".check_one").size() == $(".check_one").children(".check").size()){
                $(".check_all").children(".check_icon").addClass("check");
            }
        }
    });

    //实验室数据显示
    function labdata(){
        $('.treeview .list-group-child').click(
            function(){
                var lab_id = $(this).attr('lab_id');
                updateLabDeviceList(lab_id);
            }
        )
    }
    //更新和当前实验室相关的列表数据
    function updateLabDeviceList(lab_id,page){
        var url = pars.ajaxUrl;
        url += '?lab_id='+lab_id;
        if(!isNaN(page)){
            url += '&page='+page;
        }
        $.ajax({
            type:"get",
            url:url,
            async:true,
            success:function(res){
                var str = '';
                if(res.code == 1){
                    var data = res.data.rows.LadDeviceList.data;
                    $('#paginationOne').html(createPageDom(res.data.total,res.data.pagesize,res.data.page));
                    for(var i=0;i<data.length;i++){
                        str += '<tr>' +
                            '<td>'+(res.data.page*res.data.pagesize-res.data.pagesize+1+i)+'</td>' +
                            '<td class="device_name">'+data[i].device_info.name+'</td>' +
                            '<td class="device_type">'+data[i].device_info.devices_cate_info.name+'</td>' +
                            '<td class="total" id="DeviceNum_'+data[i].id+'">'+data[i].total+'</td>' +
                            '<td class="opera">' +
                            '<a class="state1 edit edit_res"  data-toggle="modal" data-target="#myModal"  style="text-decoration: none" id="edit">' +
                            '<span class="edit_num" labDeviceId="'+data[i].id+'">编辑数量</span>' +
                            '</a>' +
                            '<a class="state2 delete" labDeviceId="'+data[i].id+'"><span>删除</span></a>' +
                            '</td>' +
                            '</tr>';
                    }
                }
                $('#table-striped tbody').html(str);
            }
        });
    }

    //和实验室有关的 资源的翻页
    $('#paginationOne').delegate('a','click',function(){
        var page = $(this).parents('li').attr('page');
        updateLabDeviceList($('#lab_id').val(),page);
    })

//            设备添加回显数据
    $('#add_device').click(function(){
//                点击新增按钮显示当前实验室设备弹出层
        $("#add_device_form").show();
        $(".check_all").children(".check_icon").removeClass("check");
        $("#edit_form").hide();
        add();
    });
//            添加中  关键字搜索
    $('#search').click(function(){
        add();
        return false;
    });
    //编辑数量
    $('#table-striped').delegate('.edit_res','click',function(){
        $("#add_device_form").hide();
        $("#edit_form").show();
    });

    //编辑的时候把数据提取到表单
    $('#table-striped').delegate('.edit_num','click',function(){
//                alert($(this).attr('labDeviceId'));
        if($(this).attr("labDeviceId")){
            $('input[name=name]').val($(this).parent().parent().parent().find('.device_name').html());

            $('input[name=type]').val($(this).parent().parent().parent().find('.device_type').html());
            $('input[name=total]').val($(this).parent().parent().parent().find('.total').html());
        }

        var id = $(this).attr("labDeviceId");
        $('input[name="id"]').remove();
        $('#edit_form').append('<input type="hidden" name="id" value="'+id+'">');

    })




    //删除和当前实验室相关的 设备信息
    $('#table-striped').delegate('.delete','click',function(){
        var $this = $(this);
        var this_id = $(this).attr('labDeviceId');
        var url = pars.deleteUrl+"?id="+this_id;
        layer.confirm('您确定要删除该设备？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type:"get",
                url:url,
                async:true,
                success:function(res){
                    if(res.code == 1){
                        $this.parents('tr').remove();
                        layer.msg("删除成功", {icon: 1,time: 1000});
                    }else{
                        layer.msg(res.message, {icon: 2,time: 1000});
                    }
                }
            });
        });
    });


    //设备添加方法

    function add(cate_id,page){
        var url  = pars.listUrl;
        url += '?keyword='+$('#keyword').val()+'&lab_id='+$('#lab_id').val()
        if(cate_id){
            url += '&devices_cate_id='+cate_id;
        }
        if(!isNaN(page)){
            url += '&page='+page;
        }
        $.ajax({
            type:"get",
            url:url,
            async:true,
            success:function(result){
                var html = '<li>' +
                    '<a href="javascript:void(0)" cate_id = "0">全部</a>'+
                    ' </li>';
                var list ='';
                $(result.data.rows.deviceType).each(function(){
                    html+='<li>' +
                        '<a href="javascript:void(0)" cate_id = "'+this.id+'">'+this.name+'</a>'+
                        ' </li>'
                })
                $('#device-type').html(html);
                $('#paginationTwo').html(createPageDom(result.data.total,result.data.pagesize,result.data.page));
                var num = result.data.page*result.data.pagesize-result.data.pagesize+1;
                $(result.data.rows.list).each(function($item){
                    list+='<tr>' +
                        '<td>' +
                        '<label class="check_label checkbox_input check_one"> ' +
                        '<div class="check_real check_icon display_inline">' +
                        '</div> <input type="hidden" name="" value="'+this.id+'">' +
                        '</label>' +
                        '</td>' +
                        ' <td>'+(num+$item)+'</td>' +
                        ' <td> <input type="number" class="deviceNum" value="1"></td>' +
                        ' <td>'+this.name+'</td> ' +
                        '<td>'+this.catename+'</td> ' +
                        '</tr> '
                })
                $('#addition tbody').html(list);
                $('#addition').find('.check_real').removeClass('check');
            }
        })

    }

    //资源的翻页
    $('#paginationTwo').delegate('a','click',function(){
        var page = $(this).parents('li').attr('page');
        add('',page);
    })

    //根据类别筛选资源列表
    $('#device-type').delegate('a','click',function(){
        add($(this).attr('cate_id'))
    })
    //保存编辑数量
    $('#saveEdit').click(function(){
        var url = pars.editUrl;
        var labDeviceId = $('#edit_form').find('input[name="id"]').val();
        var total = $('#edit_form').find('input[name="total"]').val();
        $.ajax({
            type:"get",
            url:url,
            data:{lab_device_id:labDeviceId,total:total},
            async:true,
            success:function(result){
                if(result.code == 1){
                    $('#DeviceNum_'+labDeviceId).html(total);
                    layer.msg("编辑成功", {icon: 1,time: 1000});
                }else{
                    layer.msg("编辑失败", {icon: 2,time: 1000});
                }
            }
        })
    });
    //编辑数量验证不能为负
    $("#edit_form").delegate(".plus","change",function(){
        if($(this).val()<=0){
            $(this).val("1");
        }
    });

    //添加实验室相关设备
    $('#addDevice').click(function(){

        var DeviceIdNumArr = [];

        $(".check_one").children(".check").next("input").each(function(){
            DeviceIdNumArr.push( $(this).val()+','+$(this).parents('tr').find('.deviceNum').val());
        })
        var url = pars.addUrl;

        $.ajax({
            type:"post",
            url:url,
            data:{lab_id:$('#lab_id').val(),device_id_num:DeviceIdNumArr},
            async:true,
            success:function(result){
                if(result.code == 1){
                    updateLabDeviceList($('#lab_id').val());
                    layer.msg("添加成功", {icon: 1,time: 1000});
                }else{
                    layer.msg("添加失败", {icon: 2,time: 1000});
                }

            }
        })
    })

    //触发 check 选中
    $('#addition').delegate('.deviceNum','change',function(){
        var num = $(this).val();
        if(num>0){
            $(this).parents("tr").find('.check_real').addClass('check');
        }else{
            $(this).val(1);
            return false;
        }
    })

    function createPageDom(total,pagesize,page){
        var string = '';
        if(total>0){
            var sum = Math.ceil(total/pagesize);
            //TODO 拼凑上一页的按钮
            if(page == 1){
                string += '<li class="disabled"><span>«</span></li>';
            }else{
                string += '<li rel="prev" page="'+(page-1)+'" ><a href="javascript:void(0)">«</a></li>';
            }

            for(var i = 0;i<sum;i++){
                if(page == (i+1)){
                    string += '<li class="active"><span>'+(i+1)+'</span></li>';
                }else{
                    string += '<li page="'+(i+1)+'"><a href="javascript:void(0)">'+(i+1)+'</a></li>';
                }
            }

            //TODO 拼凑下一页的按钮
            if(page == sum){
                string += '<li class="disabled"><span>»</span></li>';
            }else{
                string += '<li rel="next" page="'+(page+1)+'" ><a href="javascript:void(0)">»</a></li>';
            }
        }
        return  string;
    }
}