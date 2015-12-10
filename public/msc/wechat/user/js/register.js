/**
 * Created by DELL on 2015/11/25.
 */


function formcheck(qj,url,getdetail){

    /*mao 2015-11-25
     *表单验证
     */
    $('#sourceForm').bootstrapValidator({
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
                    },
                    stringLength: {/*长度提示*/
                        min: 6,
                        max: 30,
                        message: '用户名长度必须在6到30之间'
                    }/*最后一个没有逗号*/
                }
            },
            detail: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '描述不能为空'
                    }
                }
            },
            manager_name: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '姓名不能为空'
                    },
                    stringLength: {
                        min:2,
                        message: '姓名长度必须大于2'
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
            }
        }
    });

}
