/**
 * Created by Administrator on 2016/1/7 0007.
 */

var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "ban_maintain":ban_maintain();break; //楼栋管理页面
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
//            编辑新增弹出层表单切换
        $("#add_ban").click(function(){
            $("#edit_from").show();
            $("#add_from").hide();
        });
        $(".edit_ban").click(function(){
            $("#add_from").show();
            $("#edit_from").hide();
        });

//            编辑
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
                up: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '地上层数不能为空'
                        }
                    }
                },
                down: {/*键名username和input name值对应*/
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '地下层数不能为空'
                        }
                    }
                },
                type: {
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '请选择状态'
                        }

                    }
                }

            }
        });

        $('.edit').click(function () {
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
            //$('#add_from').attr('action','{{route("msc.admin.floor.postEditFloorInsert")}}');
            $('#myModalLabel').html('编辑楼栋');
            var id = $(this).attr("data");
            $('#add_from').append('<input type="hidden" name="id" value="'+id+'">');
        });

    })
}
