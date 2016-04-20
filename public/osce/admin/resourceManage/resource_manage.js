/**
 * Created by Administrator on 2015/12/15 0015.
 */

var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        //考站管理
        case "exam_station": exam_station();break;
        case "exam_station_add": exam_station_add();break;
        case "exam_station_edit": exam_station_edit();break;
        //场所管理
        case "site_manage":site_manage();break;
        case "site_manage_add": site_manage_add(); break;
        case "site_manage_edit": site_manage_edit(); break;
        //病例管理
        case "clinical_case_manage":clinical_case_manage();break;
        case "clinical_case_manage_add":clinical_case_manage_add();break;
        case "clinical_case_manage_edit":clinical_case_manage_edit();break;
        //科目
        case "course_module":course_module();break;    //考点操作 新增&编辑
        case "course_manage":course_manage();break;
        //人员管理
        case "staff_manage_invigilator":staff_manage_invigilator();break;
        case "staff_manage_invigilator_add":staff_manage_invigilator_add();break;
        case "staff_manage_invigilator_edit":staff_manage_invigilator_edit();break;
        case "staff_manage_invigilator_sp":staff_manage_invigilator_sp();break;
        case "staff_manage_invigilator_sp_add":staff_manage_invigilator_sp_add();break;
        case "staff_manage_invigilator_sp_edit":staff_manage_invigilator_sp_edit();break;
        case "staff_manage_invigilator_patrol":staff_manage_invigilator_patrol();break;
        case "staff_manage_invigilator_patrol_add":staff_manage_invigilator_patrol_add();break;
        case "staff_manage_invigilator_patrol_edit":staff_manage_invigilator_patrol_edit();break;

        //设备管理
        case "equipment_manage": equipment_manage(); break;
        case "equipment_manage_video": equipment_manage_video(); break;
        case "equipment_manage_video_add": equipment_manage_video_add(); break;
        case "equipment_manage_video_edit": equipment_manage_video_edit(); break;
        case "equipment_manage_pad": equipment_manage_pad(); break;
        case "equipment_manage_pad_edit": equipment_manage_pad_edit(); break;
        case "equipment_manage_pad_add": equipment_manage_pad_add(); break;
        case "equipment_manage_watch": equipment_manage_watch(); break;
        case "equipment_manage_watch_edit": equipment_manage_watch_edit(); break;
        case "equipment_manage_watch_add": equipment_manage_watch_add(); break;
        //用物管理
        case "res_manage":res_manage();break;
        case "res_manage_add":res_manage_add();break;
        case "res_manage_edit":res_manage_edit();break;

    }
});



//解决ie10,ie11下select2 多选bug
var isIE10 = !!navigator.userAgent.match(/MSIE 10/i);
var isIE11 = !!navigator.userAgent.match(/Trident.*rv\:11\./);




/**
 * select2 模糊搜索样式
 * @author mao
 * @version 3.4
 * @date    2016-04-18
 */
function formatRepoSelection (repo) {
  return repo.full_name || repo.text;
}

/**
 * select2 下拉选择样式
 * @author mao
 * @version 1.0
 * @date    2016-04-18
 */
function formatRepo (repo) {
  if (repo.loading) return repo.text;

  var markup = "<div class='select2-result-repository clearfix'>" +
    "<div class='select2-result-repository__avatar'><img src='" + repo.owner.avatar_url + "' /></div>" +
    "<div class='select2-result-repository__meta'>" +
      "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";

  if (repo.description) {
    markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
  }

  markup += "<div class='select2-result-repository__statistics'>" +
    "<div class='select2-result-repository__forks'><i class='fa fa-flash'></i> " + repo.forks_count + " Forks</div>" +
    "<div class='select2-result-repository__stargazers'><i class='fa fa-star'></i> " + repo.stargazers_count + " Stars</div>" +
    "<div class='select2-result-repository__watchers'><i class='fa fa-eye'></i> " + repo.watchers_count + " Watchers</div>" +
  "</div>" +
  "</div></div>";

  return markup;
}





/**
 * 腕表管理
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_watch() {
    $(".fa-trash-o").click(function(){
        var thisElement=$(this);
        var eid=thisElement.attr("eid");
        layer.alert('确认删除？',{title:"删除",btn:['确认','取消']},function(){
            $.ajax({
                type:'post',
                async:true,
                url: pars.del,
                data:{id:eid, cate_id:3},
                success:function(data){
                    if(data.code == 1){
                        location.href= pars.url;
                    }else {
                        layer.msg(data.message,{skin:'msg-error',icon:1});
                    }
                }
            })
        });
    })
}

/**
 * 腕表管理编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_watch_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '腕表名称不能为空'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '腕表名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id:(location.href).split('=')[1],
                                cate: '3',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '设备ID不能为空'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: '请输入正确的设备ID'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '设备ID已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                cate: '3',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }

                        }
                    }
                }
            },
            factory: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '厂家不能为空'
                    }
                }
            },
            sp: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '型号不能为空'
                    }
                }
            },
            place: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '放置地点不能为空'
                    }
                }
            }
        }
    });
    /*时间选择*/
    var start = {
        elem: "#purchase_dt",
        format: "YYYY-MM-DD",
        min: "1970-00-00",
        max: "2099-06-16"
    };
    laydate(start);
}

/**
 * 腕表管理新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_watch_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '腕表名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                cate: '3',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '腕表名称不能为空'
                    },
                    stringLength: {
                        max:20,
                        message: '名称字数不超过20个'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '设备ID已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                cate: '3',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '设备ID不能为空'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: '请输入正确的设备ID'
                    }
                }
            },
            factory: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '厂家不能为空'
                    }
                }
            },
            sp: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '型号不能为空'
                    }
                }
            },
            place: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '放置地点不能为空'
                    }
                }
            }
        }
    });
    /*时间选择*/
    var start = {
        elem: "#purchase_dt",
        format: "YYYY-MM-DD",
        min: "1970-00-00",
        max: "2099-06-16"
    };
    laydate(start);
}

/**
 * pad管理
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_pad() {
    //删除用户
    $(".fa-trash-o").click(function(){
        var thisElement=$(this);
        var eid=thisElement.attr("eid");
        layer.alert('确认删除？',{title:"删除",title:"删除",btn:['确认','取消']},function(){
            $.ajax({
                type:'post',
                async:true,
                url:pars.del,
                data:{id:eid, cate_id:2},
                success:function(data){
                    if(data.code == 1){
                        location.href= pars.url;
                    }else {
                        layer.msg(data.message,{skin:'msg-error',icon:1});
                    }
                }
            })
        });
    })
}

/**
 * pad新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_pad_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '设备名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id:(location.href).split('=')[1],
                                cate: '2',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: 'PAD名称不能为空'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    regexp: {
                        regexp: /^[a-zA-Z0-9:]+$/,
                        message: '请输入正确的编号'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '设备ID已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                cate: '2',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '编号不能为空'
                    }
                }
            },
            factory: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '厂家不能为空'
                    }
                }
            },
            sp: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '型号不能为空'
                    }
                }
            },
            place: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '放置地点不能为空'
                    }
                }
            }
        }
    });
    /*时间选择*/
    var start = {
        elem: "#purchase_dt",
        format: "YYYY-MM-DD",
        min: "1970-00-00",
        max: "2099-06-16"
    };
    laydate(start);
}

/**
 * pad编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_pad_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '设备名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id:(location.href).split('=')[1],
                                cate: '2',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: 'PAD名称不能为空'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    regexp: {
                        regexp: /^[a-zA-Z0-9:]+$/,
                        message: '请输入正确的编号'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '设备ID已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                cate: '2',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '编号不能为空'
                    }
                }
            },
            factory: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '厂家不能为空'
                    }
                }
            },
            sp: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '型号不能为空'
                    }
                }
            },
            place: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '放置地点不能为空'
                    }
                }
            }
        }
    });
    /*时间选择*/
    var start = {
        elem: "#purchase_dt",
        format: "YYYY-MM-DD",
        min: "1970-00-00",
        max: "2099-06-16"
    };
    laydate(start);
}

/**
 * 摄像头管理
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_video() {
    //删除用户
    $(".fa-trash-o").click(function(){
        var thisElement=$(this);
        var eid=thisElement.attr("eid");
        layer.alert('确认删除？',{title:"删除",btn:['确认','取消']},function(){
            $.ajax({
                type:'post',
                async:true,
                url: pars.del,
                data:{id:eid, cate_id:1},
                success:function(data){
                    if(data.code == 1){
                        location.href= pars.url;
                    }else {
                        layer.msg(data.message,{skin:'msg-error',icon:1});
                    }
                }
            })
        });
    })
}

/**
 * 摄像头管理
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function equipment_manage_video_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '摄像机名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                cate: '1',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '编号不能为空'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: '请输入正确的设备ID'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '设备ID已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                cate: '1',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    }
                }
            },
            factory: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '厂家不能为空'
                    }
                }
            },
            sp: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '型号不能为空'
                    }
                }
            },
            ip: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: 'IP不能为空'
                    },
                    regexp: {
                        regexp: /\b(([01]?\d?\d|2[0-4]\d|25[0-5])\.){3}([01]?\d?\d|2[0-4]\d|25[0-5])\b/,
                        message: '请输入正确的IP地址'
                    }
                }
            },
            port: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '端口不能为空'
                    },
                    regexp: {
                        regexp: /^[0-9]*[1-9][0-9]*$/,
                        message: '请输入正确的端口'
                    }
                }
            },
            realport: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '实时端口不能为空'
                    },
                    regexp: {
                        regexp: /^[0-9]*[1-9][0-9]*$/,
                        message: '请输入正确的实时端口'
                    }
                }
            },
            channel: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '通道号不能为空'
                    }
                }
            },
            description: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '功能描述不能为空'
                    }
                }
            },
            username: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '用户名不能为空'
                    }
                }
            },
            password: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '密码不能为空'
                    }
                }
            },
            place: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '放置地点不能为空'
                    }
                }
            }
        }
    });
    /*时间选择*/
    var start = {
        elem: "#purchase_dt",
        format: "YYYY-MM-DD",
        min: "1970-00-00",
        max: "2099-06-16"
    };
    laydate(start);
}

/**
 * 摄像头管理编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 * @return  {[type]}   [description]
 */
function equipment_manage_video_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '设备名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id:(location.href).split('=')[1],
                                cate: '1',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '编号不能为空'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: '请输入正确的设备ID'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '设备ID已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                cate: '1',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    }
                }
            },
            factory: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '厂家不能为空'
                    }
                }
            },
            sp: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '型号不能为空'
                    }
                }
            },
            ip: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: 'IP不能为空'
                    },
                    regexp: {
                        regexp: /\b(([01]?\d?\d|2[0-4]\d|25[0-5])\.){3}([01]?\d?\d|2[0-4]\d|25[0-5])\b/,
                        message: '请输入正确的IP地址'
                    }
                }
            },
            port: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '端口不能为空'
                    },
                    regexp: {
                        regexp:  /^[0-9]*[1-9][0-9]*$/,
                        message: '请输入正确的端口'
                    }
                }
            },
            realport: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '实时端口不能为空'
                    },
                    regexp: {
                        regexp:  /^[0-9]*[1-9][0-9]*$/,
                        message: '请输入正确的实时端口'
                    }
                }
            },
            channel: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '通道号不能为空'
                    }
                }
            },
            description: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '功能描述不能为空'
                    }
                }
            },
            username: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '用户名不能为空'
                    }
                }
            },
            password: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '密码不能为空'
                    }
                }
            },
            place: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '放置地点不能为空'
                    }
                }
            }
        }
    });
    /*时间选择*/
    var start = {
        elem: "#purchase_dt",
        format: "YYYY-MM-DD",
        min: "1970-00-00",
        max: "2099-06-16"
    };
    laydate(start);
}

/**
 * 考站管理
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function exam_station(){

   $(".delete").click(function(){
       var thisElement = $(this);
       deleteItems('post',pars.deletes,thisElement.attr("value"),pars.firstpage);
   })



}

/**
 * 考站新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function exam_station_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '考站已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                title: 'station',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    }
                }
            },
            mins: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '时间限制不能为空'
                    },
                    regexp: {
                        regexp: /^\+?[1-9][0-9]*$/,
                        message: '请输入正确的时间'
                    }
                }
            }
        }
    });

    /**
     * 显示考卷
     * @author mao
     * @version 2.0.1
     * @date    2016-03-22
     */
    $('select[name="type"]').change(function() {
        var $this = $(this);
        if($this.val() == 3) {
            $('.paper-id').show();
            $('.sub-id').hide();
        } else {
            $('.paper-id').hide();
            $('.sub-id').show();
        }
    });



}

/**
 * 考站编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function exam_station_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '考站已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                title: 'station',
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    }
                }
            },
            mins: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '时间限制不能为空'
                    },
                    regexp: {
                        regexp: /^\+?[1-9][0-9]*$/,
                        message: '请输入正确的时间'
                    }
                }
            },
            subject_id: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请选择科目'
                    }
                }
            },
            case_id: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请选择病例'
                    }
                }
            },
            // room_id: {
            //     /*键名username和input name值对应*/
            //     message: 'The username is not valid',
            //     validators: {
            //         notEmpty: {/*非空提示*/
            //             message: '请选择所属考场'
            //         }
            //     }
            // },
            vcr_id: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '请选择关联摄像机'
                    }
                }
            }
        }
    });

    /**
     * 显示考卷
     * @author mao
     * @version 2.0.1
     * @date    2016-03-22
     */
    $('select[name="type"]').change(function() {
        var $this = $(this);
        if($this.val() == 3) {
            $('.paper-id').show();
            $('.sub-id').hide();
        } else {
            $('.paper-id').hide();
            $('.sub-id').show();
        }
    });

    //代码回显是否隐藏考卷
    if($('select[name="type"]').val() == 3) {
        $('.paper-id').show();
        $('.sub-id').hide();
    } else {
        $('.paper-id').hide();
        $('.sub-id').show();
    }


}

/**
 * 病例
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function clinical_case_manage(){
   $(".delete").click(function(){
       deleteItems("post",pars.deletes,$(this).attr("value"),pars.firstpage)
   })
}

/**
 * 病例管理新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function clinical_case_manage_add() {

    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '病例名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '病例名称不能为空'
                    }
                }
            }
        }
    });
}

/**
 * 病例管理编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function clinical_case_manage_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '病例名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '病例名称不能为空'
                    }
                }
            }
        }
    });
}

/**
 * 考场
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function site_manage(){
    $(".delete").click(function(){
        deleteArea("post",pars.deletes,$(this).attr("value"),$(this).data('type'),pars.firstpage)
    })
}

/**
 * 场所管理新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function site_manage_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 2,
                        max: 20,
                        message: '名称长度请在2到20之间'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {                            return {
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            };
                        }
                    }
                }
            },
            description: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '功能描述不能为空'
                    }
                }
            },
            address: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '地址不能为空'
                    }
                }
            },
            floor: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                   notEmpty: {/*非空提示*/
                       message: '楼层不能为空'
                   },
                    regexp: {
                        regexp: /^[0-9]*$/,
                        message: '楼层必须输入数字'
                    }
                }
            },
           room_number: {
               /*键名username和input name值对应*/
               message: 'The username is not valid',
               validators: {
                   notEmpty: {/*非空提示*/
                       message: '房号不能为空'
                   }
               }
           },
            proportion: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                   notEmpty: {/*非空提示*/
                       message: '可使用面积不能为空'
                   },
                    regexp: {
                        regexp: /^\d+(\.\d+)?$/,
                        message: '使用面积必须输入数字'
                    },
                    stringLength: {
                        max:20,
                        message: '使用面积输入长度不超过20个'
                    }
                }
            }
        }
    });

    //ie11下兼容问题
    $(window).resize(function(){
        $('.select2').css('width','100%');
    });

    //启动
    $('#cate').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        maximumInputLength: 12
    })
}

/**
 * 场所管理编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function site_manage_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '考场名称不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 2,
                        max: 20,
                        message: '名称长度请在2到20之间'
                    },
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '考场名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: pars.id,
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    }
                }
            },
            description: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '功能描述不能为空'
                    }
                }
            },
            address: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '地址不能为空'
                    }
                }
            },
            floor: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                   notEmpty: {/*非空提示*/
                       message: '楼层不能为空'
                   },
                    regexp: {
                        regexp: /^[0-9]*$/,
                        message: '楼层必须输入数字'
                    }
                }
            },
           room_number: {
               /*键名username和input name值对应*/
               message: 'The username is not valid',
               validators: {
                   notEmpty: {/*非空提示*/
                       message: '房号不能为空'
                   }
               }
           },
            proportion: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                   notEmpty: {/*非空提示*/
                       message: '可使用面积不能为空'
                   },
                    regexp: {
                        regexp: /^[0-9]*$/,
                        message: '使用面积必须输入数字'
                    },
                    stringLength: {
                        max:20,
                        message: '使用面积输入长度不超过20个'
                    }
                }
            }
        }
    });

    //ie11下兼容问题
    $(window).resize(function(){
        $('.select2').css('width','100%');
    });

    //启动
    $('[name=cate]').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        maximumInputLength: 12
    })

}

/**
 * 删除操作
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 * @param   {string}   url 请求地址
 */
function deleteItem(url){

	$('table').on('click','.fa-trash-o',function(){

        var thisElement = $(this);
        layer.alert('确认删除？',{title:"删除",btn:['确认','取消']},function(){
            $.ajax({
                type:'post',
                async:true,
                url:url,
                data:{id:thisElement.parent().parent().parent().attr('value'),type:thisElement.parent().parent().parent().data('type')},
                success:function(data){
                    console.log(data);
                    if(data.code==1){
                        location.reload();
                    }else{
                        layer.msg(data.message);
                    }

                },
                error:function(){
                    console.log("错误");
                }

            })
        });
    })
}

/**
 * 新增考核点
 * @author mao
 * @version 2.0.1
 * @date    2016-03-17
 */
function course_module(){
    $('#submit-btn').click(function(){
        var flag = null;
        $('#judgement tbody').find('.col-sm-10').each(function(key,elem){
            flag = true;

            if($(elem).find('input').val()==''){
                flag = false;
                return false;
            }
        });
        if(flag==false){
            layer.alert('考核点/考核项/评分标准不能为空！');
            return false;
        }
        if(flag==null){
            layer.alert('请新增考核点！');
            return false;
        }

        //验证与总分是否相等
        var total = 0;
        $('#judgement tbody').find('tr').each(function(key,elem) {
            if($(elem).attr('parent')!=undefined){
                total += parseInt($(elem).find('td').eq(2).find('span').text());
            }
        });

        if(total != parseInt($('#total').val())){
            var scores = parseInt($('#total').val()) - total,
                str = scores < 0 ? ('比总分多'+Math.abs(scores)+'分！'):('比总分少'+Math.abs(scores)+'分！')

            if(isNaN(scores)) {
                layer.alert('请添加考核项/评分标准或填写总分');
            } else {
                layer.alert(str);
            }

            return false;
        }

        //验证物品输入
        var goods = null,
            number = true;
        $('#things-use tbody').find('tr').each(function(key,elem){
            goods = true;
            var reg = new RegExp("^[0-9]*$");
            if(!reg.test($(elem).find('input').val())){
                number = false;
                return false;
            };
            if($(elem).find('input').val()==''){
                goods = false;
                return false;
            }
            if($(elem).find('input').val() < 1){
                number = false;
                return false;
            }
            if($(elem).find('select').val()==''||$(elem).find('select').val()==null){
                goods = false;
                return false;
            }
        });
        if(goods==false){
            layer.alert('用品/数量不能为空！');
            return false;
        }
        if(goods==null){
            layer.alert('请新增物品！');
            return false;
        }

        //检查物品数
        if(number == false) {
            layer.alert('物品数必须正整数');
            return false;
        }

    });


    /**
     * 新增一条父考核点
     * @author  mao
     * @version  1.0
     * @date        2015-12-31
     */
    $('#add-new').click(function(){
        //计数器标志
        var index = $('#judgement').find('tbody').attr('index');
        index = parseInt(index) + 1;
        var html = '<tr parent="'+index+'" current="0"  class="pid-'+index+'">'+
                '<td>'+parseInt(index)+'</td>'+
                '<td>'+
                '<div class="form-group">'+
                '<label class="col-sm-2 control-label">考核点:</label>'+
                '<div class="col-sm-10">'+
                '<input id="select_Category"  class="form-control" name="content['+index+'][title]"/>'+
                '</div>'+
                '</div>'+
                '</td>'+
                '<td>'+
                '<select style="display:none;" class="form-control" name="score['+index+'][total]">';
                for(var a=1; a<=15; a++){
                    html += '<option value="'+a+'">'+a+'</option>';
                }
        html +=  '</select>'+
                '</td>'+
                '<td>'+
                '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-up parent-up fa-2x"></i></span></a>'+
                '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-down parent-down fa-2x"></i></span></a>'+
                '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                '</td>'+
                '</tr>';
        //记录计数
        $('#judgement').find('tbody').attr('index',index);
        $('#judgement tbody').append(html);
    });

    /**
     * 新增个子类
     * @author  mao
     * @version  1.0
     * @date        2015-12-31
     */
    $('#judgement tbody').on('click','.fa-plus',function(){
        var thisElement = $(this).parent().parent().parent().parent();

        var parent = thisElement.attr('parent'),
                child = thisElement.attr('current');

        child = parseInt(child) + 1;

        var html = '<tr child="'+child+'" class="'+thisElement.attr('class')+'" >'+
                '<td>'+parent+'-'+child+'</td>'+
                '<td>'+
                '<div class="form-group">'+
                '<label class="col-sm-2 control-label">考核项:</label>'+
                '<div class="col-sm-10">'+
                '<input id="select_Category"  class="form-control" name="content['+parent+']['+child+']"/>'+
                '</div>'+
                '</div>'+
                '<div class="form-group">'+
                '<label class="col-sm-2 control-label">评分标准:</label>'+
                '<div class="col-sm-10">'+
                '<input id="select_Category"  class="form-control"  name="description['+parent+']['+child+']"/>'+
                '</div>'+
                '</div>'+
                '</td>'+
                '<td>'+
                '<select class="form-control" name="score['+parent+']['+child+']">';
                /*TODO: Zhoufuxiang 2016-2-26*/
                for(var a=1; a<=15; a++){
                    html += '<option value="'+a+'">'+a+'</option>';
                }
        html += '</select>'+
                '</td>'+
                '<td>'+
                '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up child-up fa-2x"></i></span></a>'+
                '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down child-down fa-2x"></i></span></a>'+
                '</td>'+
                '</tr>';
        //记录计数
        thisElement.attr('current',child);

        //分数自动加减
        //var thisElement = $(this).parent().parent();
        var childTotal  =   thisElement.parent().find('.pid-'+parent).length;
        thisElement.parent().find('.pid-'+parent).eq(childTotal-1).after(html);
        //父亲节点
        var className = thisElement.attr('class'),
            parent =  className.split('-')[1];

        //自动加减节点
        var change = $('.'+className+'[parent='+parent+']').find('td').eq(2).find('select');


        //改变value值,消除连续变换值的变化
        var total = 0;//= parseInt(change.val())+parseInt($(this).val());
        $('.'+className).each(function(key,elem){
            if($(elem).attr('parent')==parent){
                return;
            }else{
                total += parseInt($(elem).find('td').eq(2).find('select').val());
            }
        });

        //当没有子类的时候
        if(total==0){
            return;
        }

        var option = '';
        for(var k =1;k<=total;k++){
            option += '<option value="'+k+'">'+k+'</option>';
        }
        change.html(option);
        change.val(total);

        $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').remove();
        change.after('<span>'+parseInt(total)+'</span>');

        /*var option = '';
        for(var k =0;k<=child;k++){
            option += '<option value="'+k+'">'+k+'</option>';
        }
        thisElement.find('td').eq(2).find('select').html(option);
        thisElement.find('td').eq(2).find('select').val(child);
        //禁用下拉
        //thisElement.find('td').eq(2).find('select').hide();
        thisElement.find('td').eq(2).find('span').remove();
        thisElement.find('td').eq(2).find('select').after('<span>'+child+'</span>')
*/

        //更新计数
        increment(thisElement);
    });

    /**
     * 子类序号更新
     * @author marvine
     * @date    2015-12-31
     * @version [1.0]
     * @param   {object}   thisElement dom参数
     */
    function increment(thisElement){
        var update_P = 0,
                str = '.'+thisElement.attr('class');
        $('#judgement tbody').find(str).each(function(key,elem){

            if($(elem).attr('child')!=undefined){
                $(elem).attr('child',key);
                $(elem).attr('class','pid-'+update_P);
                $(elem).find('td').eq(0).text(update_P+'-'+key);
                $(elem).find('td').eq(2).find('select').attr('name','score['+update_P+']['+key+']');
            }else{
                update_P = $(elem).attr('parent');
                $(elem).attr('class','pid-'+update_P);
            }
        });
    }

    /**
     * 删除
     * @author marvine
     * @date    2015-12-31
     * @version [1.0]
     * @param   {[type]}   ){                     var thisElement [description]
     * @return  {[type]}       [description]
     */
    $('#judgement tbody').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        layer.confirm('确认删除？', {
                title:"删除",
                btn: ['确定','取消'] //按钮
            }, function(its){

        if(thisElement.attr('child')==undefined){
            //父类删除
            var classElement = '.'+thisElement.attr('class');
            var parent = 1;
            $(classElement).remove();
            //更新计数序号
            $('tbody tr').each(function(key,elem){
                if($(elem).attr('child')==undefined){
                    $(elem).attr('parent',parent);
                    $(elem).find('td').eq(0).text(parent);
                    $(elem).attr('class','pid-'+parent);
                    $(elem).find('td').eq(2).find('select').attr('name','score['+parent+'][total]');
                    parent += 1;
                }else{
                    var child = $(elem).attr('child'),
                            parent_p = parent - 1;
                    $(elem).find('td').eq(0).text(parent_p+'-'+child);
                    $(elem).attr('class','pid-'+parent_p);
                    $(elem).find('td').eq(2).find('select').attr('name','score['+parent_p+']['+child+']');
                    child += 1;
                }
            });

            //父类计数更新
            $('#judgement tbody').attr('index',parseInt($('tbody').attr('index'))-1)

        }else{
            //子类删除
            thisElement.remove();
            increment(thisElement);



            //父亲节点
            var className = thisElement.attr('class');
                parent =  className.split('-')[1];
            //自动加减节点
            var change = $('.'+className+'[parent='+parent+']').find('td').eq(2).find('select');

            //改变value值,消除连续变换值的变化
            var total = 0;//= parseInt(change.val())+parseInt($(this).val());
            $('.'+className).each(function(key,elem){
                if($(elem).attr('parent')==parent){
                    return;
                }else{
                    total += parseInt($(elem).find('td').eq(2).find('select').val());
                }
            });
            var cu = total;
            //当删除完的时候
            if(total==0){
                total = 1;
                cu = 0;
                $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').text('');
                //change.show();
                //dom
                var option = '';
                for(var k =1;k<=4;k++){
                    option += '<option value="'+k+'">'+k+'</option>';
                }
                change.html(option);
                change.val(total);
                $('.'+className+'[parent='+parent+']').attr('current',cu);
                layer.close(its);
                return;
            }
            var option = '';
            for(var k =1;k<=total;k++){
                option += '<option value="'+k+'">'+k+'</option>';
            }
            change.html(option);
            change.val(total);
            $('.'+className+'[parent='+parent+']').attr('current',cu);

            $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').remove();
            change.after('<span>'+parseInt(total)+'</span>');


        }

            layer.close(its);
        });
    });

    /**
     * 数据条目上移
     * @author marvine
     * @date    2015-12-31
     * @version [1.0]
     */
    $('#judgement tbody').on('click','.child-up',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        if(thisElement.prev().attr('child')!=undefined){
            var thisInput = thisElement.find('input:first').val(),
                thisInputLast = thisElement.find('input:last').val(),
                thisSelect = thisElement.find('select').val(),
                prevInput = thisElement.prev().find('input:first').val(),
                prevInputLast = thisElement.prev().find('input:last').val(),
                prevSelect = thisElement.prev().find('select').val();

            //交换数据
            thisElement.find('input:first').val(prevInput);
            thisElement.find('input:last').val(prevInputLast);
            thisElement.find('select').val(prevSelect);
            thisElement.prev().find('input:first').val(thisInput);
            thisElement.prev().find('input:last').val(thisInputLast);
            thisElement.prev().find('select').val(thisSelect);
        }else{
            return;
        }
    });

    /**
     * 数据条目下移
     * @author marvine
     * @date    2015-12-31
     * @version [1.0]
     */
    $('#judgement tbody').on('click','.child-down',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        if(thisElement.next().attr('child')!=undefined){
            var thisInput = thisElement.find('input:first').val(),
                thisInputLast = thisElement.find('input:last').val(),
                thisSelect = thisElement.find('select').val(),
                nextInput = thisElement.next().find('input:first').val(),
                nextInputLast = thisElement.next().find('input:last').val(),
                nextSelect = thisElement.next().find('select').val();

            //交换数据
            thisElement.find('input:first').val(nextInput);
            thisElement.find('input:last').val(nextInputLast);
            thisElement.find('select').val(nextSelect);
            thisElement.next().find('input:first').val(thisInput);
            thisElement.next().find('input:last').val(thisInputLast);
            thisElement.next().find('select').val(thisSelect);
        }else{
            return;
        }
    });

    /**
     * 父亲节点上移
     * @author mao
     * @version 1.0
     * @date    2016-01-19
     */
    $('#judgement tbody').on('click','.parent-up',function(){

        var thisElement = $(this).parent().parent().parent().parent();
        var className = thisElement.attr('class');
        var parent =  1;
        var value = [];
        var valueTotal = null;

        //存储select的值
        $('.'+className).each(function(key,elem){
            if($(elem).attr('parent')==undefined){
                value.push($(elem).find('td').eq(2).find('select').val());
            }else{
               valueTotal = $(elem).find('td').eq(2).find('select').val();
            }
        });
        //存储dom结构
        var thisDOM = $('.'+className).clone();
        var preIndex = parseInt(className.split('-')[1])-1;

        //最头一个
        if($('.pid-'+preIndex+'[parent="'+preIndex+'"]').length==0){
            return;
        }

        //上移
        $('.'+className).remove();
        $('.pid-'+preIndex+'[parent="'+preIndex+'"]').before(thisDOM);

        //更新序号
        $('#judgement tbody tr').each(function(key,elem){
            if($(elem).attr('child')==undefined){
                $(elem).attr('parent',parent);
                $(elem).find('td').eq(0).text(parent);
                $(elem).attr('class','pid-'+parent);

                //更新name表单序号
                $(elem).find('td').eq(1).find('input').attr('name','content['+parent+'][title]');
                $(elem).find('td').eq(2).find('select').attr('name','score['+parent+'][total]');

                parent += 1;
            }else{
                var child = $(elem).attr('child'),
                        parent_p = parent - 1;
                $(elem).find('td').eq(0).text(parent_p+'-'+child);
                $(elem).attr('class','pid-'+parent_p);

                //更新name表单序号
                $(elem).find('td').eq(1).find('input').eq(0).attr('name','content['+parent_p+']['+child+']');
                $(elem).find('td').eq(1).find('input').eq(1).attr('name','description['+parent_p+']['+child+']');
                $(elem).find('td').eq(2).find('select').attr('name','score['+parent_p+']['+child+']');

                child += 1;
            }
        });
        //更新数据
        $('.pid-'+preIndex).each(function(key,elem){
            if($(elem).attr('parent')==undefined){

                $(elem).find('td').eq(2).find('select').find("option").eq(value[key-1]-1).attr('selected','selected');
                $(elem).find('td').eq(2).find('select').find("option:selected").val(value[key-1]);
            }else{
                $(elem).find('td').eq(2).find('select').find("option:selected").text(valueTotal);
                $(elem).find('td').eq(2).find('select').find("option:selected").val(valueTotal);
            }
        });


    });

    /**
     * 父亲节点下移
     * @author mao
     * @version 1.0
     * @date    2016-01-19
     */
    $('#judgement tbody').on('click','.parent-down',function(){

        var thisElement = $(this).parent().parent().parent().parent();
        var className = thisElement.attr('class');
        var parent =  1;
        var value = [];
        var valueTotal = null;


        //存储select的值
        $('.'+className).each(function(key,elem){
            if($(elem).attr('parent')==undefined){
                value.push($(elem).find('td').eq(2).find('select').val());
            }else{
               valueTotal = $(elem).find('td').eq(2).find('select').val();
            }
        });
        //存储dom结构
        var thisDOM = $('.'+className).clone();
        var preIndex = parseInt(className.split('-')[1])+1;

        //最尾一个
        if($('.pid-'+preIndex+'[parent="'+preIndex+'"]').length==0){
            return;
        }

        //上移
        $('.'+className).remove();
        $('.pid-'+preIndex+':last').after(thisDOM);

        //更新序号
        $('#judgement tbody tr').each(function(key,elem){
            if($(elem).attr('child')==undefined){
                $(elem).attr('parent',parent);
                $(elem).find('td').eq(0).text(parent);
                $(elem).attr('class','pid-'+parent);

                //更新name表单序号
                $(elem).find('td').eq(1).find('input').attr('name','content['+parent+'][title]');
                $(elem).find('td').eq(2).find('select').attr('name','score['+parent+'][total]');

                parent += 1;
            }else{
                var child = $(elem).attr('child'),
                        parent_p = parent - 1;
                $(elem).find('td').eq(0).text(parent_p+'-'+child);
                $(elem).attr('class','pid-'+parent_p);

                //更新name表单序号
                $(elem).find('td').eq(1).find('input').eq(0).attr('name','content['+parent_p+']['+child+']');
                $(elem).find('td').eq(1).find('input').eq(1).attr('name','description['+parent_p+']['+child+']');
                $(elem).find('td').eq(2).find('select').attr('name','score['+parent_p+']['+child+']');

                child += 1;
            }
        });
        
        //更新数据
        $('.pid-'+preIndex).each(function(key,elem){
            if($(elem).attr('parent')==undefined){
                //$(elem).find('td').eq(2).find('select').find("option:selected").text(value[key-1]);
                $(elem).find('td').eq(2).find('select').find("option").eq(value[key-1]-1).attr('selected','selected');
                $(elem).find('td').eq(2).find('select').val(value[key-1]);
            }else{
                $(elem).find('td').eq(2).find('select').find("option:selected").text(valueTotal);
                $(elem).find('td').eq(2).find('select').find("option:selected").val(valueTotal);
            }
        });

    });

    /**
     * 文件导入
     * @author mao
     * @version 1.0
     * @date    2016-01-08
     */
    $("#file1").change(function(){
            $.ajaxFileUpload
            ({

                url:pars.excel,
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'text',//
                success: function (data, status)
                {
                    data    =   data.replace('<pre>','').replace('</pre>','');
                    data    =   eval('('+data+')');

                    if(data.code == 1){
                        layer.msg('导入成功！',{skin:'msg-success',icon:1});
                        
                        /**
                         * 数据导入
                         * @author mao
                         * @version 1.0
                         * @date    2016-01-08
                         */
                        var html = '';
                        var res = data.data;
                        //var index = parseInt($('tbody').attr('index'));

                        /*序号置0，内容清空 TODO: Zhoufuxiang 2016-2-26*/
                        var index = 0;
                        $('#judgement tbody').html('');

                        for(var i in res){
                            /*TODO: Zhoufuxiang 2016-2-26*/

                           if((res[i].sort).split('-')[1] == undefined){
                                index++;
                               //添加父级dom
                               html += '<tr parent="'+index+'" current="0"  class="pid-'+index+'">'+
                                       '<td>'+index+'</td>'+
                                       '<td>'+
                                       '<div class="form-group">'+
                                       '<label class="col-sm-2 control-label">考核点:</label>'+
                                       '<div class="col-sm-10">'+
                                       '<input id="select_Category"  class="form-control" value="'+res[i].check_point+'" name="content['+index+'][title]"/>'+
                                       '</div>'+
                                       '</div>'+
                                       '</td>'+
                                       '<td>'+
                                       '<select class="form-control" style="display:none;" name="score['+index+'][total]">'+
                                       '<option value="'+res[i].score+'">'+res[i].score+'</option>';
                                       /*TODO: Zhoufuxiang 2016-2-26*/
                                       for(var a=1; a<=15; a++){
                                           html += '<option value="'+a+'">'+a+'</option>';
                                       }
                               html += '</select>'+
                                       '<span>'+res[i].score+'</span>'+
                                       '</td>'+
                                       '<td>'+
                                       '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                       '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-up parent-up fa-2x"></i></span></a>'+
                                       '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-arrow-down parent-down fa-2x"></i></span></a>'+
                                       '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                                       '</td>'+
                                       '</tr>';
                        
                               for(var j in res){
                                   /*TODO: Zhoufuxiang 2016-2-26*/
                                   if(((res[j].sort).split('-')[1] != undefined) && ((res[j].sort).split('-')[0] == res[i].sort)){
                        
                                       //处理子级dom
                                       html += '<tr child="'+res[j].sort.substr(res[j].sort.indexOf('-')+1, 3)+'" class="pid-'+index+'" >'+
                                               '<td>'+res[j].sort+'</td>'+
                                               '<td>'+
                                               '<div class="form-group">'+
                                               '<label class="col-sm-2 control-label">考核项:</label>'+
                                               '<div class="col-sm-10">'+
                                               '<input id="select_Category"  class="form-control" value="'+res[j].check_item+'" name="content['+index+']['+res[j].sort.substr(res[j].sort.indexOf('-')+1, 3)+']"/>'+
                                               '</div>'+
                                               '</div>'+
                                               '<div class="form-group">'+
                                               '<label class="col-sm-2 control-label">评分标准:</label>'+
                                               '<div class="col-sm-10">'+
                                               '<input id="select_Category"  class="form-control" value="'+res[j].answer+'" name="description['+index+']['+res[j].sort.substr(res[j].sort.indexOf('-')+1, 3)+']"/>'+
                                               '</div>'+
                                               '</div>'+
                                               '</td>'+
                                               '<td>'+
                                               '<select class="form-control" name="score['+index+']['+res[j].sort.substr(res[j].sort.indexOf('-')+1, 3)+']">';
                                                /*TODO: Zhoufuxiang 2016-2-26*/
                                               //'<option value="'+res[j].score+'">'+res[j].score+'</option>';
                                               for(var a=1; a<=15; a++){
                                                   html += '<option value="'+a+'"'+((res[j].score==a)?" selected ":"")+'>'+a+'</option>';
                                               }
                                       html += '</select>'+
                                               '</td>'+
                                               '<td>'+
                                               '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                               '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up child-up fa-2x"></i></span></a>'+
                                               '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down child-down fa-2x"></i></span></a>'+
                                               '</td>'+
                                               '</tr>';
                                   }
                               }
                           }
                        }
                        console.log(data)
                        $('#judgement tbody').attr('index',index);
                        $('#judgement tbody').append(html);
                    }else {
                        layer.alert(data.message+'，请参考下载模板！');
                        //layer.alert('文件导入错误，请参考下载模板！');
                    }
                },
                error: function (data, status, e)
                {
                    layer.msg('导入失败！',{skin:'msg-error',icon:1});
                }
            });
        }) ;


        /**
         * 考核分数自动加减
         * @author mao
         * @version 1.0
         * @date    2016-01-20
         */
        $('#judgement tbody').on('change','select',function(){
            var thisElement = $(this).parent().parent();
            //父亲节点
            var className = thisElement.attr('class'),
                parent =  className.split('-')[1];

            //自动加减节点
            var change = $('.'+className+'[parent='+parent+']').find('td').eq(2).find('select');


            //改变value值,消除连续变换值的变化
            var total = 0;//= parseInt(change.val())+parseInt($(this).val());
            $('.'+className).each(function(key,elem){
                if($(elem).attr('parent')==parent){
                    return;
                }else{
                    total += parseInt($(elem).find('td').eq(2).find('select').val());
                }
            });

            //当没有子类的时候
            if(total==0){
                return;
            }

            var option = '';
            for(var k =1;k<=total;k++){
                option += '<option value="'+k+'">'+k+'</option>';
            }
            change.html(option);
            change.val(total);

            $('.'+className+'[parent='+parent+']').find('td').eq(2).find('span').remove();
            change.after('<span>'+parseInt(total)+'</span>')


        });

        /**
         * 病例列表
         * @author mao
         * @version 3.3
         * @date    2016-03-31
         */
        $('#select-clinical').select2({
            placeholder: isIE11 || isIE10 ? '' : '请选择',
            ajax: {
            url: pars.clinicalList,
            dataType: 'json',
            delay: 250,
            processResults: function (res) {
                if(res.code == 1){
                    var data = res.data,
                        str = [];

                    for(var i in data) {
                        str.push({id:data[i].id,text:data[i].name});
                    }
                    str.push({id:-999,text:'==新增病例=='});
                    return{
                        results:str
                    }
                }
            }
        }
        });

        /**
         * 新增病例
         * @author mao
         * @version 3.3
         * @date    2016-03-31
         */
        $('#select-clinical').on("select2:select",function(e) {
            var arr = $(this).val();

            //去掉新增病例项
            for(var i in arr) {
                if(arr[i] == -999) delete arr[i]
            }
            //重置
            $(this).val(arr).trigger("change");

            if(e.params.data.id == -999) {
                layer.open({
                  type: 2,
                  title: '病例新增',
                  shadeClose: true,
                  shade: 0.8,
                  area: ['90%', '90%'],
                  content: pars.clinical_add+"?value=-999"
                });
            }
        });

        /**
         * 新增物品
         * @author mao
         * @version 3.3
         * @date    2016-03-31
         */
        $('#add-things').click(function() {
            var html = '',
                index = $('#things-use').find('tbody').attr('index');

            index = parseInt(index) + 1;
            html = '<tr>'+
                        '<td>'+
                            '<select class="form-control js-example-basic-single" name="goods['+index+'][name]" style="width: 481px;"></select>'+
                        '</td>'+
                        '<td>'+
                            '<input class="form-control" type="number" value="1" name="goods['+index+'][number]" placeholder="请输入数量" />'+
                        '</td>'+
                        '<td>'+
                            '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        '</td>'+
                    '</tr>';

            $('#things-use').find('tbody').append(html);
            $('#things-use').find('tbody').attr('index',index);
            //启动select2
            $('#things-use .js-example-basic-single').select2({
                tags:true,
                ajax: {
                    url: pars.goodList,
                    dataType: 'json',
                    delay: 250,
                    processResults: function (res) {
                        if(res.code == 1){
                            var data = res.data,
                                str = [];

                            for(var i in data) {
                                str.push({id:data[i].name,text:data[i].name});
                            }

                            return{
                                results:str
                            }
                        }
                    },
                    templateResult: formatRepo,
                    templateSelection: formatRepoSelection
                }
            })
            

        });

    /**
     * 编辑初始化
     */
    $('#things-use .js-example-basic-single').select2({
            tags:true,
            ajax: {
                url: pars.goodList,
                dataType: 'json',
                delay: 250,
                processResults: function (res) {
                    if(res.code == 1){
                        var data = res.data,
                            str = [];

                        for(var i in data) {
                            str.push({id:data[i].name,text:data[i].name});
                        }

                        return{
                            results:str
                        }
                    }
                },
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            }
        })

        /**
         * 删除物品
         * @author mao
         * @version 3.3
         * @date    2016-03-31
         */
        $('#things-use').on('click', '.fa-trash-o', function() {
            var $that = $(this).parent().parent().parent().parent();
            layer.confirm('确认删除？', {
                title:"删除",
                btn: ['确定','取消'] //按钮
            }, function(its){
                $that.remove();
                layer.close(its);
            });
        });



}

function staff_manage_invigilator(){
    //删除老师
    $(".delete").click(function(){
        deleteItems("post",pars.deletes,$(this).attr("tid"),pars.firstpage);
    })

    /**
     * 老师导入
     * @author mao
     * @version 2.0.1
     * @date    2016-03-21
     */
    $('#file1').change(function() {
        //加载中
        var index = layer.load(0, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.ajaxFileUpload({
            url:pars.excel,
            type:'post',
            secureuri:false,//
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',//
            success: function (data, status)
            {
                if(data.code == 1){
                    layer.close(index);
                    layer.msg(data.message,{skin:'msg-success',icon:1},function(){
                        location.reload();
                    });
                }else{
                    layer.close(index);
                    layer.msg(data.message,{skin:'msg-error',icon:1},function(){
                        location.reload();
                    });
                }
            },
            error: function (data, status, e)
            {
                layer.close(index);
                layer.alert(data.message,{skin:'msg-error',icon:1});
            }
        });
    });
    
}

/**
 * 人员管理
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function staff_manage_invigilator_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    regexp: {/* 只需加此键值对，包含正则表达式，和提示 */
                        regexp:  /^[a-zA-Z\u4e00-\u9fa5]+$/,
                        message: '只能输入中文和字母'
                    },
                    stringLength: {
                        max:20,
                        message: '名称字数不超过20个'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '该教师编号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '教师编号不能为空'
                    },
                    regexp: {
                        regexp: /^\w+$/,
                        message: '教师编号应该由数字，英文或下划线组成'
                    },
                    stringLength: {
                        max:20,
                        message: '编号数字不超过20个'
                    }
                }
            },
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '号码已经存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|7|5|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            idcard: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '身份证号已存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                idcard: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '身份证号不能为空'
                    },
                    regexp: {
                        regexp: /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
                        message: '请输入正确的身份证号'
                    }
                }
            },
            email: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.email,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '邮箱已存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                email: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ,
                        message: '请输入正确的邮箱'
                    }
                }
            },/*TODO: chenxia 2016-3-30*/
            'subject[]': {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '支持考试项目不能为空'
                    }
                }
            },
            description:{
                message: 'The username is not valid',
                validators:{
                    stringLength: {
                        max: 100,
                        message: '最多只能输入100个字符！'
                    }
                }
            }
        }
    });
    $("#images_upload").change(function(e){

        var files=document.getElementById("file0").files;

        //兼容ie9
        if(files) {
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }
        }

        $.ajaxFileUpload
        ({
            url:pars.url,
            secureuri:false,//
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',//
            success: function (data, status)
            {
                if(data.code){
                    var href=data.data.path;
                    $('.img_box').find('li').remove();
                    $('#images_upload').before('<li><img src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
                    $('#images_upload').attr("class","images_upload1");
                }
            },
            error: function (data, status, e)
            {
                var res = (JSON.parse(data.responseText));
                layer.msg((res.message).split(':')[1],{skin:'msg-error',icon:1});
            }
        });
    }) ;

    //建立一個可存取到該file的url
    var url='';
    function getObjectURL(file) {
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url;
    }
    $(".img_box").delegate(".del_img","click",function(){
        $(this).parent("li").remove();
        $('#images_upload').attr("class","images_upload");
    });

    //$(".image-box").find(".help-block").css({"color":"#a94442","text-align":"center","width":"280px"});//图片未选择提示语言颜色
    //图片检测
    $('#save').click(function(){
        if($('.img_box').find('img').attr('src')==undefined){
            layer.msg('请上传图片！',{skin:'msg-error',icon:1});
            return false;
        }
    });


    /**
     * 多选下拉框
     * @author mao
     * @version 3.3
     * @date    2016-03-30
     */
    $(".data-example-ajax").select2({
      ajax: {
        type:'get',
        url: pars.get_subject,
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                title: params.term
            };
        },
        processResults: function (res) {
          
            //数据格式化
            var str = [];
            var data = res.data;
            for(var i in data){
                str.push({id:data[i].id,text:data[i].title});
            }

            //加载入数据
            return {
                results: str
            };
        },
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
      }
    });


}

function staff_manage_invigilator_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    regexp: {/* 只需加此键值对，包含正则表达式，和提示 */
                        regexp:  /^[a-zA-Z\u4e00-\u9fa5]+$/,
                        message: '只能输入中文和字母'
                    },
                    stringLength: {
                        max:20,
                        message: '名称字数不超过20个'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '该教师编号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id:  (location.href).split('=')[1],
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '教师编号不能为空'
                    },
                    regexp: {
                        regexp: /^\w+$/,
                        message: '教师编号应该由数字，英文或下划线组成'
                    },
                    stringLength: {
                        max:20,
                        message: '编号数字不超过20个'
                    }
                }
            },
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '号码已经存在',//提示消息
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                mobile: $('#mobile').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|7|5|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            idcard: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '身份证号已存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id: (location.href).split('=')[1],
                                idcard: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '身份证号不能为空'
                    },
                    regexp: {
                        regexp: /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
                        message: '请输入正确的身份证号'
                    }
                }
            },
            email: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.email,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '邮箱已经存在',//提示消息
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                mobile: $('#mobile').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ,
                        message: '请输入正确的邮箱'
                    }
                }
            },/*TODO: chenxia 2016-3-30*/
            'subject[]': {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '支持考试项目不能为空'
                    }
                }
            },
            description:{
                message: 'The username is not valid',
                validators:{
                    stringLength: {
                        max: 100,
                        message: '最多只能输入100个字符！'
                    }
                }
            }
        }
    });
    $("#images_upload").change(function(){

        var files=document.getElementById("file0").files;

        //兼容ie9
        if(files) {
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }
        }

        $.ajaxFileUpload
        ({

            url:pars.url,
            secureuri:false,//
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',//
            success: function (data, status)
            {
                if(data.code){
                    var href=data.data.path;
                    $('.img_box').find('li').remove();
                    $('#images_upload').before('<li><img style="width:197px;height:250px;" src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
                }
            },
            error: function (data, status, e)
            {
                var res = (JSON.parse(data.responseText));
                layer.msg((res.message).split(':')[1],{skin:'msg-error',icon:1});
            }
        });
    }) ;

    //建立一個可存取到該file的url
    var url='';
    function getObjectURL(file) {
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url;
    }

    /**
     * 删除
     * @author mao
     * @version 1.0
     * @date    2016-02-19
     */
    $(".img_box").delegate(".del_img","click",function(){
        $(this).parent("li").remove();
        $('#images_upload').attr("class","images_upload");
    });
    /**
     * 多选下拉框
     * @author chenxia
     * @version 3.3
     * @date    2016-03-30
     */
    $(".data-example-ajax").select2({
        ajax: {
            type:'get',
            url: pars.get_subject,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    title: params.term
                };
            },
            processResults: function (res) {

                //数据格式化
                var str = [];
                var data = res.data;
                for(var i in data){
                    str.push({id:data[i].id,text:data[i].title});
                }

                //加载入数据
                return {
                    results: str
                };
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        }
    });

}

/**
 * 评分标准列表
 * @author mao
 * @version 1.0
 * @date    2016-01-15
 * @return  {[type]}   [description]
 */

function course_manage(){

    $(".fa-trash-o").click(function(){
        var thisElement=$(this);

        layer.confirm('确认删除？', {
            title:"删除",
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type:'get',
                async:true,
                url: pars.del + "?id="+thisElement.parent().parent().parent().attr('value'),
                success:function(res){

                    if(res.code==1){
                        location.href = (location.href).split('?')[0];
                    }else{
                        layer.alert(res.message)
                    }
                }
            })
        });
    })


   /*$(".fa-trash-o").click(function(){
        var $that = $(this);
        layer.alert('确认删除？',function(){
            $.ajax({
                type:'get',
                async:false,
                url:pars.del,
                data:{id: $that.parent().parent().parent().attr('value')},
                success:function(data){
                    location.reload();
                }
            })
        });
    })*/
}

function staff_manage_invigilator_sp(){
    //删除老师
    $(".delete").click(function(){

        deleteItems("post",pars.deletes,$(this).attr("tid"),pars.firstpage);
    })
}

/**
 * sp老师新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function staff_manage_invigilator_sp_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    regexp: {/* 只需加此键值对，包含正则表达式，和提示 */
                        regexp:  /^[a-zA-Z\u4e00-\u9fa5]+$/,
                        message: '只能输入中文和字母'
                    },
                    stringLength: {
                        max:20,
                        message: '名称字数不超过20个'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '该教师编号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '教师编号不能为空'
                    },
                    regexp: {
                        regexp: /^\w+$/,
                        message: '教师编号应该由数字，英文或下划线组成'
                    },
                    stringLength: {
                        max:20,
                        message: '编号数字不超过20个'
                    }
                }
            },
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '号码已经存在'//提示消息
                    },
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            idcard: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '身份证号已存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                idcard: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '身份证号不能为空'
                    },
                    regexp: {
                        regexp: /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
                        message: '请输入正确的身份证号'
                    }
                }
            },
            email: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.email,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '邮箱已经存在',//提示消息
                        data: function(validator) {
                            return {
                                mobile: $('#mobile').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ,
                        message: '请输入正确的邮箱'
                    }
                }
            },
            /*TODO: chenxia 2016-3-30*/
            'subject[]': {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '支持考试项目不能为空'
                    }
                }
            },
            description:{
                message: 'The username is not valid',
                validators:{
                    stringLength: {
                        max: 100,
                        message: '最多只能输入100个字符！'
                    }
                }
            }
        }
    });
    $("#images_upload").change(function(){

        var files=document.getElementById("file0").files;

        //兼容ie9
        if(files) {
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }
        }

        $.ajaxFileUpload
        ({

            url:pars.url,
            secureuri:false,//
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',//
            success: function (data, status)
            {
                if(data.code){
                    var href=data.data.path;
                    $('.img_box').find('li').remove();
                    $('#images_upload').before('<li><img src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
                    $('#images_upload').attr("class","images_upload1");
                }
            },
            error: function (data, status, e)
            {
                var res = (JSON.parse(data.responseText));
                layer.msg((res.message).split(':')[1],{skin:'msg-error',icon:1});
            }
        });
    }) ;

    //建立一個可存取到該file的url
    var url='';
    function getObjectURL(file) {
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url;
    }
    $(".img_box").delegate(".del_img","click",function(){
        $(this).parent("li").remove();
        $('#images_upload').attr("class","images_upload");
    });
    
    //图片检测
    $('#save').click(function(){
        if($('.img_box').find('img').attr('src')==undefined){
            layer.msg('请上传图片！',{skin:'msg-error',icon:1});
            return false;
        }
    });
    /**
     * 多选下拉框
     * @author chenxia
     * @version 3.3
     * @date    2016-03-30
     */
    $(".data-example-ajax").select2({
        ajax: {
            type:'get',
            url: pars.get_subject,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    title: params.term
                };
            },
            processResults: function (res) {

                //数据格式化
                var str = [];
                var data = res.data;
                for(var i in data){
                    str.push({id:data[i].id,text:data[i].title});
                }

                //加载入数据
                return {
                    results: str
                };
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        }
    });
}

/**
 * sp老师编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function staff_manage_invigilator_sp_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    regexp: {/* 只需加此键值对，包含正则表达式，和提示 */
                        regexp:  /^[a-zA-Z\u4e00-\u9fa5]+$/,
                        message: '只能输入中文和字母'
                    },
                    stringLength: {
                        max:20,
                        message: '名称字数不超过20个'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '该教师编号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                                                                                                                                
                            return {
                                id:  (location.href).split('=')[1],
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '教师编号不能为空'
                    },
                    regexp: {
                        regexp: /^\w+$/,
                        message: '教师编号应该由数字，英文或下划线组成'
                    },
                    stringLength: {
                        max:20,
                        message: '编号数字不超过20个'
                    }
                }
            },
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '号码已经存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                mobile: $('#mobile').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|7|5|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            idcard: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '身份证号已存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id: (location.href).split('=')[1],
                                idcard: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '身份证号不能为空'
                    },
                    regexp: {
                        regexp: /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
                        message: '请输入正确的身份证号'
                    }
                }
            },
            email: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.email,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '邮箱已经存在',//提示消息
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                mobile: $('#mobile').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ,
                        message: '请输入正确的邮箱'
                    }
                }
            },
            /*TODO: chenxia 2016-3-30*/
            'subject[]': {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '支持考试项目不能为空'
                    }
                }
            },
            description:{
                message: 'The username is not valid',
                validators:{
                    stringLength: {
                        max: 100,
                        message: '最多只能输入100个字符！'
                    }
                }
            }
        }
    });

    $("#images_upload").change(function(){

        var files=document.getElementById("file0").files;

        //兼容ie9
        if(files) {
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }
        }

        $.ajaxFileUpload
        ({

            url:pars.url,
            secureuri:false,//
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',//
            success: function (data, status)
            {
                if(data.code){
                    var href=data.data.path;
                    $('.img_box').find('li').remove();
                    $('#images_upload').before('<li><img src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
                }
            },
            error: function (data, status, e)
            {
                var res = (JSON.parse(data.responseText));
                layer.msg((res.message).split(':')[1],{skin:'msg-error',icon:1});
            }
        });
    }) ;

    //建立一個可存取到該file的url
    var url='';
    function getObjectURL(file) {
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url;
    }

    /**
     * 删除
     * @author mao
     * @version 1.0
     * @date    2016-02-19
     */
    $(".img_box").delegate(".del_img","click",function(){
        $(this).parent("li").remove();
        $('#images_upload').attr("class","images_upload");
    });
    /**
     * 多选下拉框
     * @author chenxia
     * @version 3.3
     * @date    2016-03-30
     */
    $(".data-example-ajax").select2({
        ajax: {
            type:'get',
            url: pars.get_subject,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    title: params.term
                };
            },
            processResults: function (res) {

                //数据格式化
                var str = [];
                var data = res.data;
                for(var i in data){
                    str.push({id:data[i].id,text:data[i].title});
                }

                //加载入数据
                return {
                    results: str
                };
            },
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        }
    });
    /**
     * 保存数据
     * @author chenxia
     * @version 3.3
     * @date    2016-04-12
     *//*
    $('#save').click(function() {
        $.ajax({
            type:'post',
            url: "",
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('保存数据失败！',{skin:'msg-error',icon:1});
                } else {
                    layer.msg('数据保存成功！',{skin:'msg-success',icon:1});
                }
            }
        })
    });
*/
}

//删除方法封装,其中id为当前dom的value值
function deleteItems(type,url,id,firstpage){
    layer.alert('确认删除?',{title:"删除",btn:['确认','取消']},function(){
        $.ajax({
            type:type,
            async:false,
            url:url,
            data:{id:id},
            success:function(data){
                if(data.code == 1){
                    location.href=firstpage;
                }else {
                    layer.msg(data.message,{skin:'msg-error',icon:1});
                }
            }
        })
    });
}
//删除场所
function deleteArea(type,url,id,areaType,firstpage){
    layer.alert('确认删除?',{title:"删除",btn:['确认','取消']},function(){
        $.ajax({
            type:type,
            async:false,
            url:url,
            data:{
                id:id,
                type:areaType
            },
            success:function(data){
                if(data.code == 1){
                    if(data.data.type == 0){
                        location.href = firstpage;
                    }else {
                        location.href= $('.nav-tabs').find('.active').find('a').attr('href');
                    }
                }else {
                    layer.msg(data.message,{skin:'msg-error',icon:1});
                }
            }
        })
    });
}
/**
 * 人员管理
 * @author chenxia
 * @version 2.0.1
 * @date    2016-03-30
 */
function staff_manage_invigilator_patrol_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    regexp: {/* 只需加此键值对，包含正则表达式，和提示 */
                        regexp:  /^[a-zA-Z\u4e00-\u9fa5]+$/,
                        message: '只能输入中文和字母'
                    },
                    stringLength: {
                        max:20,
                        message: '名称字数不超过20个'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '该教师编号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '教师编号不能为空'
                    },
                    regexp: {
                        regexp: /^\w+$/,
                        message: '教师编号应该由数字，英文或下划线组成'
                    },
                    stringLength: {
                        max:20,
                        message: '编号数字不超过20个'
                    }
                }
            },
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '号码已经存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|7|5|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            idcard: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '身份证号已存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                idcard: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '身份证号不能为空'
                    },
                    regexp: {
                        regexp: /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
                        message: '请输入正确的身份证号'
                    }
                }
            },
            email: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.email,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '邮箱已经存在',//提示消息
                        data: function(validator) {
                            return {
                                mobile: $('#mobile').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ,
                        message: '请输入正确的邮箱'
                    }
                }
            },
            description:{
                message: 'The username is not valid',
                validators:{
                    stringLength: {
                        max: 100,
                        message: '最多只能输入100个字符！'
                    }
                }
            }
        }
    });
    $("#images_upload").change(function(){

        var files=document.getElementById("file0").files;

        //兼容ie9
        if(files) {
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }
        }

        $.ajaxFileUpload
            ({
                url:pars.url,
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'json',//
                success: function (data, status)
                {
                    if(data.code){
                        var href=data.data.path;
                        $('.img_box').find('li').remove();
                        $('#images_upload').before('<li><img src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
                        $('#images_upload').attr("class","images_upload1");
                    }
                },
                error: function (data, status, e)
                {
                    var res = (JSON.parse(data.responseText));
                    layer.msg((res.message).split(':')[1],{skin:'msg-error',icon:1});
                }
            });
    }) ;

    //建立一個可存取到該file的url
    var url='';
    function getObjectURL(file) {
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url;
    }
    $(".img_box").delegate(".del_img","click",function(){
        $(this).parent("li").remove();
        $('#images_upload').attr("class","images_upload");
    });

    //$(".image-box").find(".help-block").css({"color":"#a94442","text-align":"center","width":"280px"});//图片未选择提示语言颜色
    //图片检测
    $('#save').click(function(){
        if($('.img_box').find('img').attr('src')==undefined){
            layer.msg('请上传图片！',{skin:'msg-error',icon:1});
            return false;
        }
    });
}

/**
 * 人员管理
 * @author chenxia
 * @version 2.0.1
 * @date    2016-03-30
 */
function staff_manage_invigilator_patrol_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '名称不能为空'
                    },
                    regexp: {/* 只需加此键值对，包含正则表达式，和提示 */
                        regexp:  /^[a-zA-Z\u4e00-\u9fa5]+$/,
                        message: '只能输入中文和字母'
                    },
                    stringLength: {
                        max:20,
                        message: '名称字数不超过20个'
                    }
                }
            },
            code: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '该教师编号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '教师编号不能为空'
                    },
                    regexp: {
                        regexp: /^\w+$/,
                        message: '教师编号应该由数字，英文或下划线组成'
                    },
                    stringLength: {
                        max:20,
                        message: '编号数字不超过20个'
                    }
                }
            },
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '号码已经存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id: (location.href).split('=')[1],
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '手机号码不能为空'
                    },
                    stringLength: {
                        min: 11,
                        max: 11,
                        message: '请输入11位手机号码'
                    },
                    regexp: {
                        regexp: /^1[3|7|5|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            idcard: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '身份证号已存在',//提示消息
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {

                            return {
                                id: (location.href).split('=')[1],
                                idcard: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '身份证号不能为空'
                    },
                    regexp: {
                        regexp: /^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/,
                        message: '请输入正确的身份证号'
                    }
                }
            },
            email: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.email,//验证地址
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        message: '邮箱已经存在',//提示消息
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                mobile: $('#mobile').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/ ,
                        message: '请输入正确的邮箱'
                    }
                }
            },
            description:{
                message: 'The username is not valid',
                validators:{
                    stringLength: {
                        max: 100,
                        message: '最多只能输入100个字符！'
                    }
                }
            }
        }
    });
    $("#images_upload").change(function(){

        var files=document.getElementById("file0").files;

        //兼容ie9
        if(files) {
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }
        }
        
        $.ajaxFileUpload
            ({
                url:pars.url,
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'json',//
                success: function (data, status)
                {
                    if(data.code){
                        var href=data.data.path;
                        $('.img_box').find('li').remove();
                        $('#images_upload').before('<li><img src="'+href+'"/><input type="hidden" name="images_path[]" value="'+href+'"/><i class="fa fa-remove font16 del_img"></i></li>');
                        $('#images_upload').attr("class","images_upload1");
                    }
                },
                error: function (data, status, e)
                {
                    var res = (JSON.parse(data.responseText));
                    layer.msg((res.message).split(':')[1],{skin:'msg-error',icon:1});
                }
            });
    }) ;

    //建立一個可存取到該file的url
    var url='';
    function getObjectURL(file) {
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(file) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file) ;
        }
        return url;
    }
    $(".img_box").delegate(".del_img","click",function(){
        $(this).parent("li").remove();
        $('#images_upload').attr("class","images_upload");
    });

    //$(".image-box").find(".help-block").css({"color":"#a94442","text-align":"center","width":"280px"});//图片未选择提示语言颜色
    //图片检测
    $('#save').click(function(){
        if($('.img_box').find('img').attr('src')==undefined){
            layer.msg('请上传图片！',{skin:'msg-error',icon:1});
            return false;
        }
    });
}
/**
 * 用物管理
 * @author chenxia
 * @version 3.3
 * @date    2016-03-31
 */
function res_manage_add() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '用物名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '用物名称不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 1,
                        max: 32,
                        message: '用物名称长度不超过32个'
                    }/*最后一个没有逗号*/
                }
            }
        }
    });
    /**
     * 保存数据
     * @author chenxia
     * @version 3.3
     * @date    2016-04-13
     */
     /*$('#save').click(function() {
         $.ajax({
             type:'post',
             url: "",
             dataType:"json",
             success: function(res) {
                 if(res.code != 1) {
                    layer.msg('保存数据失败！',{skin:'msg-error',icon:1});
                 } else {
                    layer.msg('数据保存成功！',{skin:'msg-success',icon:1});
                 }
             }
         })
     });*/
}
/**
 * 用物管理
 * @author chenxia
 * @version 3.3
 * @date    2016-03-31
 */
function res_manage_edit() {
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                /*键名username和input name值对应*/
                message: 'The username is not valid',
                validators: {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.name,//验证地址
                        message: '用物名称已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: (location.href).split('=')[1],
                                name: $('[name="whateverNameAttributeInYourForm"]').val()
                            }
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '用物名称不能为空'
                    },
                    stringLength: {/*长度提示*/
                        min: 1,
                        max: 32,
                        message: '用物名称长度不超过32个'
                    }/*最后一个没有逗号*/
                }
            }
        }
    });
}
/**
 * 用物管理
 * @author chenxia
 * @version 3.3
 * @date    2016-03-31
 */
function res_manage(){

    $(".fa-trash-o").click(function(){
        var thisElement=$(this);

        layer.confirm('确认删除？', {
            title:"删除",
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type:'get',
                async:true,
                url: pars.del + "?id="+thisElement.parent().parent().parent().attr('value'),
                success:function(res){

                    if(res.code==1){
                        location.href = (location.href).split('?')[0];
                    }else{
                        layer.alert(res.message)
                    }
                }
            })
        });
    })
}