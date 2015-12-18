/**
 * Created by Administrator on 2015/12/17 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "resourceborrow":resourceborrow();break; //resourceborrow页面
    }
});

function resourceborrow(){
    /**
     *外借设备申请表单验证
     *吴冷眉
     *QQ：2632840780
     *2015-12-17
     *update：wulengmei（2015-12-17 18:00） （最近更新/更改 作者及时间）
     **/

    var url = pars.ajaxurl;
    $("#teacher_dept").select2({
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data.rows,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });
    function formatRepo (repo) {
        if (repo.loading) return repo.text;
        return  "<div class='select2-result-repository clearfix'>" +repo.name +"</div>";
    }
    function formatRepoSelection (repo) {
        $('#code').val( repo.code);
        $('#resources_id').val( repo.id);
        return repo.name;
    }

    $('#frmTeacher').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            resources_tool_id: {/*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '所选设备不能为空'
                    },
                }
            },

            begindate: {
                validators: {
                    notEmpty: {
                        message: '开始时间不能为空'
                    },
                    callback: {
                        message: '开始日期不能小于当前日期',
                        callback:function(value, validator,$field,options){
                            var b_date = value.replace(/-/g,"");
                            var t_date = today.replace(/-/g,"");
                            return parseInt(b_date)>=parseInt(t_date);
                        }
                    }
                }
            },
            enddate: {
                validators: {
                    notEmpty: {
                        /*非空提示*/
                        message: '结束时间不能为空'
                    },
                    callback: {
                        message: '结束日期不能小于开始日期',
                        callback: function (value, validator, $field) {
                            var begin = $('#star_time').val();
                            $('#star_time').keypress();
                            var b_date = begin.replace(/-/g,"");
                            var e_date = value.replace(/-/g,"");
                            return parseInt(e_date) >= parseInt(b_date);
                        }
                    }
                }
            },

            detail: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '申请理由不能为空'
                    }                }
            },

        }
    });

}