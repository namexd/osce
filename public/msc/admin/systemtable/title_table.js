/**
 * Created by Administrator on 2016/1/8 0008.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "title_table":title_table();break; //ְ���б�ҳ��
    }
});
function title_table(){
    $(".delete").click(function(){
        var this_id = $(this).attr('data');
        var url = "/msc/admin/professionaltitle/holder-remove?id="+this_id;
        //ѯ�ʿ�
        layer.confirm('��ȷ��Ҫɾ����ְ�ƣ�', {
            btn: ['ȷ��','ȡ��'] //��ť
        }, function(){
            window.location.href=url;
        });
    });
    $(".stop").click(function(){
        var this_id = $(this).attr('data');
        var type = $(this).attr('data-type');
        var url = "/msc/admin/professionaltitle/holder-status?id="+this_id+"&type="+type;
        var str = '';
        if(type == 1){
            str = '��ȷ��Ҫ����ְ�ƣ�';
        }else{

            str = '��ȷ��Ҫͣ��ְ�ƣ�';
        }
        //ѯ�ʿ�
        layer.confirm(str, {
            btn: ['ȷ��','ȡ��'] //��ť
        }, function(){
            window.location.href=url;
        });
    });
//        ��������֤
    $('#add_from').bootstrapValidator({
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
                        message: 'ְ��������Ϊ��'
                    }
                }
            },
            status: {
                validators: {
                    notEmpty: {/*�ǿ���ʾ*/
                        message: '״̬����Ϊ��'
                    }
                }
            },
            description: {/*����username��input nameֵ��Ӧ*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*�ǿ���ʾ*/
                        message: '��������Ϊ��'
                    }
                }
            }
        }
    });
    //        �༭����֤
    $('#edit_from').bootstrapValidator({
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
                        message: 'ְ��������Ϊ��'
                    }
                }
            },
            status: {
                validators: {
                    notEmpty: {/*�ǿ���ʾ*/
                        message: '״̬����Ϊ��'
                    }
                }
            },
            description: {/*����username��input nameֵ��Ӧ*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*�ǿ���ʾ*/
                        message: '��������Ϊ��'
                    }
                }
            }
        }
    });
    $('.edit').click(function () {
        $("#add_from").hide();
        $("#edit_from").show();
//            ȥ��Ϊ����ʽ
        $(".form-group").removeClass("has-success").removeClass("has-error").children(".col-sm-9").children("i").css("display","none").siblings("small").css("display","none");
        if($(this).attr("data")){
            $('input[name=name]').val($(this).parent().parent().find('.name').html());
            $('input[name=description]').val($(this).parent().parent().find('.describe').html());
            var status = '';
            if($(this).parent().parent().find('.status').html() ==='����'){
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
        $(".form-group").removeClass("has-success").removeClass("has-error").children(".col-sm-9").children("i").css("display","none").siblings("small").css("display","none");
        $("input,textarea,select").val("");
        $("#add_from").show();
        $("#edit_from").hide();
    })
}