/**
 * Created by Administrator on 2016/1/7 0007.
 */

var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "resource_table":resource_table();break; //资源列表页面
        case "title_table":title_table();break; //职称列表页面
        case "major_table":major_table();break; //专业列表页面
        case "departments_table":departments_table();break; //科室列表页面
    }

});
//资源列表页面
function resource_table(){
    $(function(){
//            删除
        $(".delete").click(function(){
            var this_id = $(this).attr('data');
//                alert(this_id);
            var url = "/msc/admin/resources/resources-remove?id="+this_id;
            //询问框
            layer.confirm('您确定要删除该资源？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=url;
            });
        });
//            停用
        $(".stop").click(function(){
            var this_id = $(this).attr('data');
            var type = $(this).attr('data-type');
//                alert(this_id);
            var url = "/msc/admin/resources/resources-status?id="+this_id+"&type="+type;
            var str = '';
            if(type == 1){
                str = '您确定要启用资源？';
            }else{

                str = '您确定要停用资源？';
            }
            //询问框
            layer.confirm( str, {
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
                    devices_cate_id: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            regexp: {
                                regexp: /^(?!-1).*$/,
                                message: '资源类型不能为空'
                            }
                        }
                    },
                    name: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '资源名称不能为空'
                            }
                        }
                    },
                    status: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '请选择状态'
                            }
                        }
                    },
                    detail: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '说明不能为空'
                            }
                        }
                    }
                }
            });
        }
        //            编辑验证
        function edit_form(){
            $('#edit_from').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    devices_cate_id: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '资源类型不能为空'
                            }
                        }
                    },
                    name: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '资源名称不能为空'
                            }
                        }
                    },
                    status: {
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '请选择状态'
                            }
                        }
                    },
                    detail: {/*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '说明不能为空'
                            }
                        }
                    }
                }
            });
        }
        var $edit_select=$(".edit_select").html();
        $('.edit').click(function () {
            $("#add_from").hide();
            var $editUrl=$("#editUrl").val();
            $("#edit_from").remove();
            $("#form_box").append('<form class="form-horizontal" id="edit_from" novalidate="novalidate" action='+$editUrl+' method="post"> ' +
                '<div class="modal-header"> ' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
                '<h4 class="modal-title" id="myModalLabel">编辑资源</h4> ' +
                '</div> ' +
                '<div class="modal-body"> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>资源名称</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control name add-name" name="name" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>资源类型</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b cate edit_select" name="devices_cate_id"> ' + $edit_select+
                '</select>' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label">说明</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control name add-name" name="detail" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b state" name="status"> ' +
                '<option value="1">正常</option> ' +
                '<option value="0">停用</option> ' +
                '</select> ' +
                '</div> ' +
                '</div> ' +
                '<div class="hr-line-dashed"></div> ' +
                '<div class="form-group"> ' +
                '<div class="col-sm-4 col-sm-offset-2 right"> ' +
                '<button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
                '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
                '</div> ' +
                '</div> ' +
                '</div> ' +
                '</form>');
            edit_form();
            if($(this).attr("data")){
                $('input[name=name]').val($(this).parent().parent().find('.name').html());
//                $('input[name=]').val($(this).parent().parent().find('.floor').attr('data'));
                $('input[name=detail]').val($(this).parent().parent().find('.detail').html());
                var devices = $(this).parent().parent().find('.catename').attr('data');
//                alert(devices);
                $('.cate option').each(function(){
                    if($(this).val() == devices){
                        $(this).attr('selected','selected');
                    }
                });
                var status = '';
                if($(this).parent().parent().find('.status').html() == '正常'){
                    status = 1;
                }else{
                    status = 0;
                }
                $('.state option').each(function(){
                    if($(this).val() == status){
                        $(this).attr('selected','selected');
                    }
                });
                var id = $(this).attr("data");
                $('#edit_from').append('<input type="hidden" name="id" value="'+id+'">');
            }
        });
        var $add_select=$(".add_select").html();
        $('#addResources').click(function(){
            $("#edit_from").hide();
            var $addUrl=$("#addUrl").val();
            $("#add_from").remove();
            $("#form_box").append('<form class="form-horizontal" id="add_from" novalidate="novalidate" action='+$addUrl+' method="post"> ' +
                '<div class="modal-header"> ' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> ' +
                '<h4 class="modal-title" id="myModalLabel">新增资源</h4> ' +
                '</div> ' +
                '<div class="modal-body"> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>资源名称</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control name add-name" name="name" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>资源类型</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b cate edit_select" name="devices_cate_id"> ' + $add_select+
                '</select>' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label">说明</label> ' +
                '<div class="col-sm-9"> ' +
                '<input type="text" class="form-control name add-name" name="detail" value="" /> ' +
                '</div> ' +
                '</div> ' +
                '<div class="form-group"> ' +
                '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
                '<div class="col-sm-9"> ' +
                '<select id="select_Category"   class="form-control m-b state" name="status"> ' +
                '<option value="1">正常</option> ' +
                '<option value="0">停用</option> ' +
                '</select> ' +
                '</div> ' +
                '</div> ' +
                '<div class="hr-line-dashed"></div> ' +
                '<div class="form-group"> ' +
                '<div class="col-sm-4 col-sm-offset-2 right"> ' +
                '<button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button> ' +
                '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button> ' +
                '</div> ' +
                '</div> ' +
                '</div> ' +
                '</form>');
            add_form();
        })
    })
}
//职称列表页面
function title_table(){
    //删除
    $(".delete").click(function(){
        var this_id = $(this).attr('data');
        var url = "/msc/admin/professionaltitle/holder-remove?id="+this_id;
        //询问框
        layer.confirm('您确定要删除该职称？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });
    //停用
    $(".stop").click(function(){
        var this_id = $(this).attr('data');
        var type = $(this).attr('data-type');
        var url = "/msc/admin/professionaltitle/holder-status?id="+this_id+"&type="+type;
        var str = '';
        if(type == 1){
            str = '您确定要启用职称？';
        }else{

            str = '您确定要停用职称？';
        }
        //询问框
        layer.confirm(str, {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });
    //        新增表单验证
    function add_from(){
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
                            message: '职称名不能为空'
                        }
                    }
                },
                status: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '状态不能为空'
                        }
                    }
                },
                description: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '描述不能为空'
                        }
                    }
                }
            }
        });
    }
    //        编辑表单验证
    function edit_from(){
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
                            message: '职称名不能为空'
                        }
                    }
                },
                status: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '状态不能为空'
                        }
                    }
                },
                description: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '描述不能为空'
                        }
                    }
                }
            }
        });
    }
    //编辑
    $('.edit').click(function () {
        $("#add_from").hide();
        $("#edit_from").remove();
        $('#formBox').append('<form class="form-horizontal" id="edit_from" novalidate="novalidate" action="'+$('#editUrl').val()+'" method="post">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
            '<h4 class="modal-title" id="myModalLabel">编辑职称</h4>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>职称名称</label>' +
            '<div class="col-sm-9">' +
            '<input type="text" class="form-control name add-name" name="name" value="" />' +
            '</div>' +
            '</div>' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">职称描述</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control describe add-describe" name="description" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category"   class="form-control m-b state" name="status"> ' +
            '<option value="1">正常</option> ' +
            '<option value="0">停用</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="hr-line-dashed"></div> ' +
            '<div class="form-group"> ' +
            '<div class="col-sm-4 col-sm-offset-2 right"> ' +
            '<button class="btn btn-primary sure_btn"  type="submit" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button> ' +
            '<button class="btn btn-white2 right" type="button" data-dismiss="modal" id="close">关&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;闭</button> ' +
            '</div> ' +
            '</div> ' +
            '</div> ' +
            '</form>');
        edit_from();
        if($(this).attr("data")){
            $('input[name=name]').val($(this).parent().parent().find('.name').html());
            $('input[name=description]').val($(this).parent().parent().find('.describe').html());
            var status = '';
            if($(this).parent().parent().find('.status').children("span").html() ==='正常'){
                status = 1;
            }else{
                status = 0;
            }
            $('.state option').each(function(){
                if($(this).val() == status){
                    $(this).attr('selected','selected');
                }
            });
            var id = $(this).attr("data");
            $('#edit_from').append('<input type="hidden" name="id" value="'+id+'">');
        }
    });
    $('#addtitletable').click(function(){
        $('#add_from').remove();
        $('#formBox').append('<form class="form-horizontal" id="add_from" novalidate="novalidate" action="'+$('#addUrl').val()+'" method="post">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
            '<h4 class="modal-title" id="myModalLabel">新增职称</h4>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>职称名称</label>' +
            '<div class="col-sm-9">' +
            '<input type="text" class="form-control name add-name" name="name" value="" />' +
            '</div>' +
            '</div>' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label">职称描述</label> ' +
            '<div class="col-sm-9"> ' +
            '<input type="text" class="form-control describe add-describe" name="description" /> ' +
            '</div> ' +
            '</div> ' +
            '<div class="form-group"> ' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label> ' +
            '<div class="col-sm-9"> ' +
            '<select id="select_Category"   class="form-control m-b state" name="status"> ' +
            '<option value="1">正常</option> ' +
            '<option value="0">停用</option> ' +
            '</select> ' +
            '</div> ' +
            '</div> ' +
            '<div class="hr-line-dashed"></div> ' +
            '<div class="form-group"> ' +
            '<div class="col-sm-4 col-sm-offset-2 right"> ' +
            '<button class="btn btn-primary sure_btn"  type="submit" >保&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;存</button> ' +
            '<button class="btn btn-white2 right" type="button" data-dismiss="modal" id="close">关&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;闭</button> ' +
            '</div> ' +
            '</div> ' +
            '</div> ' +
            '</form>');
        $("#edit_from").hide();
        add_from();
    });
}
//专业列表页面
function major_table(){
    //            删除
    $(".delete").click(function(){
        var this_id = $(this).attr('data');
        var url = pars.deleteUrl+"?id="+this_id;
        //询问框
        layer.confirm('您确定要删除该专业？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });
    //            停 用
    $(".stop").click(function(){
        var this_id = $(this).attr('data');
        var type = $(this).attr('data-type');
        var url = pars.stopUrl+"?id="+this_id+"&type="+type;
        var str = '';
        if(type == 1){
            str = '您确定要启用该专业？';
        }else{
            str = '您确定要停用该专业？';
        }
        //询问框
        layer.confirm(str, {
            btn: ['确定','取消'] //按钮
        }, function(){
            window.location.href=url;
        });
    });
    //            导入
    $("#in").change(function(){
        var str=$("#load_in").val().substring($("#load_in").val().lastIndexOf(".")+1);
        if(str!="xlsx"){
            $("#load_in").val('');
            layer.alert(
                "请上传正确的文件格式？",
                {title:["温馨提示","font-size:16px;color:#408aff"]}
            );
        }else{
            $.ajaxFileUpload({
                type:"post",
                url:pars.inUrl,
                fileElementId:'load_in',//必须要是 input file标签 ID
                dataType: 'json',
                success: function (data, status){
                    if(data.status = true){
                        layer.msg("导入成功，有"+data.dataHavenInfo.count+"条已有数据未被导入", {icon: 1,time: 4000});
                        setTimeout(function(){
                            window.location.href = window.location.href;
                        },3500)
                    }else{
                        layer.msg("导入失败", {icon: 1,time: 1000});
                    }
                },
                error: function (data, status, e){
                    layer.alert(
                        "上传失败！",
                        {title:["温馨提示","font-size:16px;color:#408aff"]}
                    );
                }
            });
        }
    });
    //            新增表单验证
    function add_form(){
        $('#add_from').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                code: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '专业代码不能为空'
                        }
                    }
                },
                name: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '专业名称不能为空'
                        }
                    }
                },
                status: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '状态不能为空'
                        }
                    }
                }
            }
        });
    }
    //            编辑表单验证
    function edit_form(){
        $('#edit_from').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {/*验证*/
                code: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '专业代码不能为空'
                        }
                    }
                },
                name: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '专业名称不能为空'
                        }
                    }
                },
                status: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '状态不能为空'
                        }
                    }
                }
            }
        });
    }
    //编辑
    $('.edit').click(function () {
        var $editUrl=$("#editUrl").val();
        var $my_edit=$("#my_edit");
        $("#my_add").hide();
        $("#edit_from").remove();
        $my_edit.show().empty().append(
            '<form class="form-horizontal" id="edit_from" novalidate="novalidate" action="" method="post">'+
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
            '<h4 class="modal-title" id="myModalLabel">编辑专业</h4>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>专业代码</label>' +
            '<div class="col-sm-9">' +
            '<input type="text" class="form-control name add-name" name="code" value="" />' +
            '</div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>专业名称</label>' +
            '<div class="col-sm-9">' +
            '<input type="text" class="form-control describe add-describe" name="name" />' +
            '</div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>' +
            '<div class="col-sm-9">' +
            '<select id="select_Category"   class="form-control m-b state" name="status">' +
            '<option value="1">正常</option>' +
            '<option value="0">停用</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="hr-line-dashed"></div>' +
            '<div class="form-group">' +
            '<div class="col-sm-4 col-sm-offset-2 right">' +
            '<button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>' +
            '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>' +
            '</div>' +
            '</div>' +
            '</div>'+
            '</form>'
        );
        $("#edit_from").attr("action",$editUrl);
        edit_form();
        if($(this).attr("data")){
            $('input[name=name]').val($(this).parent().parent().find('.name').html());
            $('input[name=code]').val($(this).parent().parent().find('.code').html());
            var status = '';
            if($(this).parent().parent().find('.status').html() == '正常'){
                status = 1;
            }else{
                status = 0;
            }
            $('.state option').each(function(){
                if($(this).val() == status){
                    $(this).attr('selected','selected');
                }
            });
            var id = $(this).attr("data");
            $('#edit_from').append('<input type="hidden" name="id" value="'+id+'">');
        }
    });
    $('#addprofession').click(function(){
        var $addUrl=$("#addUrl").val();
        var $my_add=$("#my_add");
        $("#my_edit").hide();
        $("#add_from").remove();
        $my_add.show().empty().append(
            '<form class="form-horizontal" id="add_from" novalidate="novalidate" action="" method="post">'+
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
            '<h4 class="modal-title" id="myModalLabel">新增专业</h4>' +
            '</div>' +
            '<div class="modal-body">' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>专业代码</label>' +
            '<div class="col-sm-9">' +
            '<input type="text" class="form-control name add-name" name="code" value="" />' +
            '</div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>专业名称</label>' +
            '<div class="col-sm-9">' +
            '<input type="text" class="form-control describe add-describe" name="name" />' +
            '</div>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="col-sm-3 control-label"><span class="dot">*</span>状态</label>' +
            '<div class="col-sm-9">' +
            '<select id="select_Category"   class="form-control m-b state" name="status">' +
            '<option value="1">正常</option>' +
            '<option value="0">停用</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '<div class="hr-line-dashed"></div>' +
            '<div class="form-group">' +
            '<div class="col-sm-4 col-sm-offset-2 right">' +
            '<button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>' +
            '<button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>' +
            '</div>' +
            '</div>' +
            '</div>'+
            '</form>'
        );
        $("#add_from").attr("action",$addUrl);
        add_form();
    })
}
function departments_table(){
    $(function(){
        gethistory();
        addfater();

        $(document).ajaxSuccess(function(event, request, settings) {
            listclick();//dom之后添加事件
            toggle();//
            editall();//更改当前栏目内容
            deleteall();//删除科室
            addChild();//添加子科室

        });
    });
    function gethistory(){
        $.ajax({
            url:pars.selectUrl, /*${ctx}/*/
            type:"get",
            dataType:"json",
            contentType : 'application/json',
            cache:false,
            success: function(result) {

                $(result.data.total).each(function(){

                    if(this.child!=""){
                        $(".treeview ul").append(
                            '<li class="list-group-item parent" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'">'
                            + '<input type="hidden" class="description" value=" '+this.description+'"/>'
                            +'<span class="icon"><i class="glyphicon  glyphicon-plus"><input type="hidden" class="toggel" value="0"/></i></span>'
                            +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                            +'<b>'+this.name+'</b>'
                            +'</li>'
                        );//第一层添加
                        $(this.child).each(function() {
                            if(this.child!=""){
                                $(".treeview ul").append(
                                    '<li class="list-group-item children1" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'"style="display: none;">'
                                    + '<span class="indent"></span>'
                                    + '<input type="hidden" class="description" value=" '+this.description+'"/>'
                                    + '<span class="icon"><i class="glyphicon glyphicon-plus"></i></span>'
                                    + '<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +'<b>'+this.name+'</b>'
                                    + '</li>'
                                );//第二层添加
                                $(this.child).each(function() {
                                    $(".treeview ul").append(
                                        '<li class="list-group-item children2" id="'+this.id+'"pid="'+this.pid+'" level="'+ this.level+'"style="display: none;">'
                                        + '<span class="indent"></span>'
                                        + '<span class="indent"></span>'
                                        + '<input type="hidden" class="description" value=" '+this.description+'"/>'

                                        + '<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                        +'<b>'+this.name+'</b>'
                                        + '</li>'
                                    );//第三层添加
                                })
                            }else{
                                $(".treeview ul").append(
                                    '<li class="list-group-item children1" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'"style="display: none;">'
                                    + '<span class="indent"></span>'
                                    + '<input type="hidden" class="description" value=" '+this.description+'"/>'

                                    +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                    +'<b>'+this.name+'</b>'
                                    +'</li>'
                                );//第二层添加
                            }

                        })
                    }else{
                        $(".treeview ul").append(
                            '<li class="list-group-item parent" id="'+this.id+'"pid="'+this.pid+'" level="'+this.level+'">'
                            + '<input type="hidden" class="description" value=" '+this.description+'"/>'

                            +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                            +'<b>'+this.name+'</b>'
                            +'</li>'
                        );//第一层添加
                    }
                });
            }
        });
    }
    function   listclick(){//dom之后添加事件
        $(".list-group-item").unbind().click(function(){
            $("#submit").hide();
            $("#edit_save").show();//按钮显示隐藏功能
            var level=$(this).attr("level");
            $("#hidden_this_id").val($(this).attr("id"));//获取点击时该栏目的ID
            $(".add-name").val($(this).text());
            $(".add-describe").val($(this).children(".description").val());
            if(level=="1"){
                $(".add-parent").val("");//上级科室
            }else if(level=="2"){
                var parent_name=$(this).prevAll(".parent").first().text()
                $(".add-parent").val(parent_name);//上级科室
            }else{
                var parent_name=$(this).prevAll(".children1").first().text()
                $(".add-parent").val(parent_name);//上级科室
            }
            $(this).addClass("checked").siblings().removeClass("checked");//表单切换
            addChild();//添加子科室功能
        });
    }
    function  toggle(){//dom之后添加事件
        $(".glyphicon").unbind().click(function(){
            var fatherid= $(this).parent().parent().attr("id");
            var fatherlevel= $(this).parent().parent().attr("level");

            if(fatherlevel=="1"){
                $(this).toggleClass("glyphicon-minus");
                $(this).toggleClass("glyphicon-plus");
                if($(this).children(".toggel").val()=="0"){
                    $(this).children(".toggel").val("1");
                    $(this).parent().parent().siblings(".children1").each(function(){
                        if($(this).attr("pid")==fatherid){
                            $(this).show();
                        }
                    })
                    return false;
                }else{

                    $(this).parent().parent().nextUntil(".parent").hide();
                    $(this).children(".toggel").val("0");
                    $(this).parent().parent().nextUntil(".parent").each(function(){
                        if($(this).attr("level")=="2"){
                            $(this).children(".icon").children(".glyphicon-minus").removeClass("glyphicon-minus").addClass("glyphicon-plus");
                        }
                    });
                    return false;
                }

            }else if(fatherlevel=="2"){
                $(this).toggleClass("glyphicon-minus");
                $(this).toggleClass("glyphicon-plus");
                $(this).parent().parent().siblings(".children2").each(function(){
                    if($(this).attr("pid")==fatherid){
                        $(this).toggle();
                    }
                })
            }
        })
    }
    function  addChild(){
        var listId;//
        var level;
        $("#new-add-child").unbind().click(function(){
            $(".list-group").find(".list-group-item").each(function(){//获取被选中的栏目
                if($(this).hasClass("checked")==true){
                    listId = $(this).attr("id");//获取点击时该栏目的ID
                    level=$(this).attr("level");
                    $(".add-parent").val($(this).text());
                }
            })
            if(!level){
                layer.msg("您尚未选择上一级科室", {icon: 2,time: 1000});
                return false;
            }
            else if(level>=3){
                layer.msg("无法再添加子科室", {icon: 2,time: 1000});
                return false;
            }
            $("#edit_save").hide();
            $("#submit").show();
            $(".add-name").val("");
            $(".add-describe").val("");
            level++;
            addChildgroup(level,listId);

        })

    }
    function addChildgroup(level,listId){
        $("#submit").unbind().click(function(){
            var name=$(".add-name").val();
            validate (name);//验证科室名称
            if(mark){
                return false;
            }
            var describe=$(".add-describe").val();
            var qj={name:name,pid:listId,level:level,description:describe}
            $.ajax({
                url:pars.addUrl, /*${ctx}/*/
                type: "post",
                dataType: "json",
                cache: false,
                data:qj,
                success: function (result) {

                    if(result.data.total.level=="2"){
                        $("#"+listId).after(
                            '<li class="list-group-item children1" id="'+result.data.total.id+'"pid="'+result.data.total.pid+'" level="'+result.data.total.level+'">'
                            + '<span class="indent"></span>'
                            + '<input type="hidden" class="description" value=" '+result.data.total.description+'"/>'

                            +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                            +'<b>'+result.data.total.name+'</b>'
                            +'</li>'
                        )
                    }else if(result.data.total.level=="3"){
                        $("#"+listId).after(
                            '<li class="list-group-item children2" id="'+result.data.total.id+'"pid="'+result.data.total.pid+'" level="'+result.data.total.level+'">'
                            + '<span class="indent"></span>'
                            + '<span class="indent"></span>'
                            + '<input type="hidden" class="description" value=" '+result.data.total.description+'"/>'

                            +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                            +'<b>'+result.data.total.name+'</b>'
                            +'</li>'
                        )
                    }
                    if($("#"+listId+" .glyphicon-plus").size()=="0"&&$("#"+listId+" .glyphicon-minus").size()=="0"){
                        $("#"+listId+" .description").before(
                            '<span class="icon"><i class="glyphicon glyphicon-minus"><input type="hidden" class="toggel" value="1"/></i></span>'
                        );
                        $("#"+listId+" .toggel").val("1");
                    }else{
                        $("#"+listId+" .glyphicon-plus").removeClass("glyphicon-plus").addClass("glyphicon-minus");
                        $("#"+listId).nextUntil(".parent").show();
                    }
                    toggle();
                    $("#"+result.data.total.id).addClass("checked").siblings().removeClass("checked");//表单切换
                    $("#hidden_this_id").val(result.data.total.id);//将右侧编辑框的隐藏域内容更新
                    $("#submit").hide();
                    $("#edit_save").show();//按钮显示隐藏功能
                }
            })
        })
    }
    function editall(){
        $("#edit_save").unbind().click(function(){
            var thisid=$("#hidden_this_id").val();
            var name=$(".add-name").val();
            var describe=$(".add-describe").val();
            var qj={name:name,id:thisid,description:describe}
            $.ajax({
                url:pars.updateUrl, /*${ctx}/*/
                type: "post",
                dataType: "json",
                cache: false,
                data:qj,
                success: function (result) {
                    if(result.code==1){
                        $("#"+thisid+" b").text(name);
                        $("#"+thisid).children(".description").val(describe);
                        layer.msg(result.message, {icon: 1,time: 1000});
                    } else{
                        layer.msg(result.message, {icon: 2,time: 1000});
                    }
                }
            })
        })
    }
    function deleteall(){
        $("#delete").unbind().click(function(){
            var thisid=$("#hidden_this_id").val();
            var attetioninfo='您确定要删除该科室？';
            if($("#"+thisid).next().attr("level")>1){
                attetioninfo='该科室下有子科室，您确定要删除该科室？';
            }
            var qj={id:thisid};

            layer.confirm(attetioninfo, {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    url:pars.deleteUrl, /*${ctx}/*/
                    type: "post",
                    dataType: "json",
                    cache: false,
                    data:qj,
                    success: function (result) {
                        if(result.code==1){
                            $(result.data.rows).each(function(){
                                $("#"+this).remove();
                                $("#add_department input").val("");
                                $("#hidden_this_id").val();
                            });
                            layer.msg(result.message, {icon: 1,time: 1000});
                        } else{
                            layer.msg(result.message, {icon: 1,time: 1000});
                        }
                    }
                })
            });

        })
    }
    function addfater(){
        $("#new-add-father").unbind().click(function(){
            $("#edit_save").hide();
            $("#submit").show();
            $(".add-parent").val("");
            $(".add-name").val("");
            $(".add-describe").val("");

            $("#submit").unbind().click(function(){
                var name=$(".add-name").val();
                validate (name);//添加验证
                if(mark){
                    return false;
                }
                var describe=$(".add-describe").val();
                var qj={name:name,pid:"0",level:1,description:describe}

                $.ajax({
                    url:pars.addUrl, /*${ctx}/*/
                    type: "post",
                    dataType: "json",
                    cache: false,
                    data:qj,
                    success: function (result) {
                        if(result.code==1){
                            $(".treeview ul").append(
                                '<li class="list-group-item parent" id="'+result.data.total.id+'"pid="'+result.data.total.pid+'" level="'+result.data.total.level+'">'
                                + '<input type="hidden" class="description" value=" '+result.data.total.description+'"/>'
                                +'<span class="icon"><i class="glyphicon glyphicon-stop"></i></span>'
                                +'<b>'+result.data.total.name+'</b>'
                                +'</li>'
                            );//第一层添加
                            layer.msg(result.message, {icon: 1,time: 1000});
                            $("#"+result.data.total.id).addClass("checked").siblings().removeClass("checked");//表单切换
                            $("#hidden_this_id").val(result.data.total.id);
                            $("#submit").hide();
                            $("#edit_save").show();//按钮显示隐藏功能
                            addChild();
                            addChildgroup();
                            deleteall();
                        } else{
                            layer.msg(result.message, {icon: 2,time: 1000});
                        }


                    }
                })
            });

        })
    }
    //        添加科室验证
    function validate (name){
        mark = false;
        $(".list-group").find(".list-group-item").each(function(){
            if($(this).text()==name){
                mark = true;
                layer.msg("该科室已存在", {icon: 2,time: 1000});
                return false;
            }
        })

    }
}
