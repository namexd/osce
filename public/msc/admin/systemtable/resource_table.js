/**
 * Created by Administrator on 2016/1/7 0007.
 */

var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "resource_table":resource_table();break; //资源列表页面
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
                str = '您确定要恢复资源？';
            }else{

                str = '您确定要禁用资源？';
            }

            //询问框
            layer.confirm( str, {
                btn: ['确定','取消'] //按钮
            }, function(){
                window.location.href=url;
            });
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
                kinds: {/*键名username和input name值对应*/
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
                type: {
                    validators: {
                        regexp: {
                            regexp: /^(?!-1).*$/,
                            message: '请选择状态'
                        }

                    }
                },

            }
        });

        $('.edit').click(function () {
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
                $('#add_from').attr('action','{{route("msc.admin.resources.ResourcesSave")}}');
                var id = $(this).attr("data");
                $('#add_from').append('<input type="hidden" name="id" value="'+id+'">');
            }
        });

        $('#addResources').click(function(){
            $("input,textarea,select").val("");
            $('#add_from').attr('action',"{{route('msc.admin.resources.ResourcesAdd')}}");
        })
    })
}
