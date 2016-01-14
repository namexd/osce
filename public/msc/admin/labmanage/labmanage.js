/**
 * Created by Administrator on 2016/1/7 0007.
 */

var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "ban_maintain":ban_maintain();break; //楼栋管理页面
        case "lab_maintain":lab_maintain();break; //实验室管理页面
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
                    floor_buttom: {/*键名username和input name值对应*/
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
                    floor_buttom: {/*键名username和input name值对应*/
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

}