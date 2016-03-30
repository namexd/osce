/**
 * 成绩查询
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        //用户管理
        case "user_manage":user_manage();break;
        case "user_manage_add":user_manage_add();break;
        case "user_manage_edit":user_manage_edit();break;
        case "user_manage_change_role":user_manage_change_role();break;
        //系统设置
        case "system_settings_media": system_settings_media();break;
        
    }
});

function system_settings_media() {
	$(".checkbox_input").click(function(){
		if($(this).find("input").is(':checked')){
			$(this).find(".check_icon ").addClass("check");
		}else{
			$(this).find(".check_icon").removeClass("check");
		}
	});
		/**
     * 下面是进行插件初始化
     * 你只需传入相应的键值对
     * */
    $('#list_form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
       		'message_type[]': {/*键名username和input name值对应*/
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请至少选择一个'
                    }
                }
           	},
            sms_cnname: {
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请选择短信方式'
                    }
                }
                
            },
            sms_url: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    },
                    regexp: {
                        regexp: /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/,
                        message: '请输入正确网址'
                    }
                }
            },
            sms_username: {
                validators: {
                    notEmpty: {
                        message: '用户名不能为空'
                    }
                }
            },
            sms_password: {
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    }
                }
            },
            wechat_use_alias: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            wechat_app_id: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            wechat_secret: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            wechat_token: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            wechat_encoding_key: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            email_server: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            email_port: {
                validators: {
                    notEmpty: {
                        message: '端口不能为空'
                    },
                    regexp: {
                        regexp: /^[0-9]+$/,
                        message: '只能输入数字'
                    }
                }
            },
            email_protocol: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            email_ssl: {
                validators: {
                    notEmpty: {
                        message: '不能为空'
                    }
                }
            },
            email_username: {
                validators: {
                    notEmpty: {
                        message: '用户名不能为空'
                    }
                }
            },
            email_password: {
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    }
                }
            }
        }
    });
}

/**
 * 用户管理
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function user_manage() {
	//删除用户
    $(".fa-trash-o").click(function(){
        var thisElement=$(this);
        var uid=thisElement.attr("uid");

        layer.confirm('确认删除？', {
        	title:"删除",
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type:'post',
                async:true,
                url:pars.URL,
                data:{id:uid},
                success:function(data){
                    if(data.code == 1){
                        location.reload();
                    }else {
                        layer.msg(data.message,{skin:'msg-error',type:1});
                    }
                }
            })
        });
    })
}

/**
 * 用户管理新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function user_manage_add() {
	/**
     * 下面是进行插件初始化
     * 你只需传入相应的键值对
     * */
    $('#Form3').bootstrapValidator({
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
                            message: '姓名不能为空'
                        },
                        stringLength: {/*长度提示*/
                            min: 2,
                            max: 20,
                            message: '姓名长度请在2到20之间'
                        }/*最后一个没有逗号*/
                    }
                },
                gender: {
                    validators: {
                        notEmpty: {
                            message: '请选择性别'
                        }
                    }
                },
                mobile: {
	                 validators: {
	                    notEmpty: {
	                        message: '手机号码不能为空'
	                    },
	                    regexp: {
	                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
	                        message: '请输入11位正确的手机号码'
	                    }
	                }
	            }
            }
        });
}

/**
 * 用户管理编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function user_manage_edit() {
	/**
     * 下面是进行插件初始化
     * 你只需传入相应的键值对
     * */
    $('#Form3').bootstrapValidator({
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
                            message: '姓名不能为空'
                        },
                        stringLength: {/*长度提示*/
                            min: 2,
                            max: 20,
                            message: '姓名长度请在2到20之间'
                        }/*最后一个没有逗号*/
                    }
                },
                gender: {
                    validators: {
                        notEmpty: {
                            message: '请选择性别'
                        }
                    }
                },
                mobile: {
                     validators: {
                        notEmpty: {
                            message: '手机号码不能为空'
                        },
                        regexp: {
                            regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
                            message: '请输入11位正确的手机号码'
                        }
                    }
                }
            }
        });
}
