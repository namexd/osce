/**
 * Created by Administrator on 2016/1/7 0007.
 */

var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "resource_table":resource_table();break; //资源列表页面
        case "title_table":title_table();break; //职称列表页面
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

        //            编辑验证
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
        $(".sure_btn").click(function(){
            var name=$("#add_from .name").val();
            var cate=$("#add_from .cate").val();
            var detail=$("#add_from .detail").val();
            var state=$("#add_from .state").val();
            if(name.trim()==""){
                $("#add_from .name").focus();
                return false;
            }
            if(cate.trim()==""){
                $("#add_from .cate").focus();
                return false;
            }
            if(detail.trim()==""){
                $("#add_from .detail").focus();
                return false;
            }
            if(state.trim()==""){
                $("#add_from .state").focus();
                return false;
            }
        });
        $('.edit').click(function () {
            $("#add_from").hide();
            $("#edit_from").show();
            //            解决再次点击样式问题
            //$(".sure_btn").removeAttr("disabled");
           // $(".form-group").removeClass("has-success").removeClass("has-error").children(".col-sm-9").children("i").css("display","none").siblings("small").css("display","none");
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

        $('#addResources').click(function(){
            $("#add_from").show();
            $("#edit_from").hide();
            //            解决再次点击样式问题
            //$(".sure_btn").removeAttr("disabled");
           // $(".form-group").removeClass("has-success").removeClass("has-error").children(".col-sm-9").children("i").css("display","none").siblings("small").css("display","none");
            $("input,textarea,select").val("");
            
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
        $("#edit_from").show();
        edit_from();
//            去除为空样式
        $(".sure_btn").removeAttr("disabled");
        $(".form-group").removeClass("has-success").removeClass("has-error").children(".col-sm-9").children("i").css("display","none").siblings("small").css("display","none");
        if($(this).attr("data")){
            $('input[name=name]').val($(this).parent().parent().find('.name').html());
            $('input[name=description]').val($(this).parent().parent().find('.describe').html());
            var status = '';
            if($(this).parent().parent().find('.status').html() ==='正常'){
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
            '<option value="0">禁用</option> ' +
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
    //$(".modal,#close").click(function(event){
    //    event.stopPropagation();
    //})
}
