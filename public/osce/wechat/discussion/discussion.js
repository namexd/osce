/**
 * 讨论区
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22  
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        //讨论区
        case "discussion_list":discussion_list();break;
        case "discussion_response":discussion_response();break;
        case "discussion_edit":discussion_edit();break;
        case "discussion_quiz": discussion_quiz();break;
        case "discussion_detail":discussion_detail();break;
    }
});

/**
 * 讨论列表
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 * @return  {}   
 */
function discussion_list() {
	$(window).scroll(function(e){
        if(away_top >= (page_height - window_height)&&now_page<totalpages){
            now_page++;
            //qj.page=now_page;//设置页码
            getItem(now_page,url)
            /*加载显示*/
        }
    });
    //初始化
    var now_page = 1;
    var url = pars.getUrl;
    //内容初始化
    $('.history-list').empty();
    getItem(now_page,url);

    function getItem(current,url){
        $.ajax({
            type:'get',
            url:url,
            aysnc:true,
            data:{id:current,pagesize:current},
            success:function(res){
                totalpages = res.total;
                var html = '';
                var index = (current - 1)*10;
                data = res.data.rows;
                for(var i in data){
                    //准备dom
                    //计数
                    var key = (index+1+parseInt(i))
                    if(data[i].user==null)
                    {
                        var ThisName    ='-';
                    }
                    else
                    {
                        var ThisName    =   data[i].user.name;
                    }
                    //字数限制
                    var content = '';
                    if((data[i].content).length>45){
                        content = (data[i].content).substring(0,45) + '...';
                    }else{
                        content = data[i].content;
                    }

                    html += '<li>'+
					        	'<a class="nou" href="'+pars.URL+'?id='+data[i].id+'">'+
					        		'<p class="font14 fontb clo3 p_title">'+data[i].title+'</p>'+
					        		'<p class="font12 clo9 main_txt">'+content+'</p>'+
					        		'<p class="font12 p_bottom">'+
					        			'<span class="student_name">'+ThisName+'</span>'+
					        			'<span class="clo0">&nbsp;·&nbsp;</span>'+
					        			'<span class="clo9">'+data[i].time+'</span>'+
					        			'<span class="right comment"><img src="'+pars.img+'" height="16"/>&nbsp;'+data[i].count+'&nbsp;</span>'+
					        		'</p>'+
					        	'</a>'+
					        '</li>';
                }
                //插入
                  $('#discussion_ul').append(html);
            }
        });
    }
}

/**
 * 讨论回复
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 * @return  {}   
 */
function discussion_response() {
	//回复
    $("#context").keyup(function(){
        var content=$("#context").val();
        $(".sum").text(content.length);
    });

    $('.btn2').click(function(){
        var content = $('#context').val();
        if(content==''){
            $.alert({
                title: '提示：',
                content: '回复内容不能为空!',
                confirmButton: '确定',
                confirm: function(){
                }
            });
            return;
        }
        if(content.length>200){
            $.alert({
                title: '提示：',
                content: '回复内容不能超过200字!',
                confirmButton: '确定',
                confirm: function(){
                }
            });
            return;
        }

        $.ajax({
            type:'post',
            url:pars.post,
            data:{content:content,id:$('input[name=id]').val()},
            success:function(res){
                if(res.code!=1){
                    layer.alert(res.message);
                }else{
                	$.alert({
		                title: '提示：',
		                content: '回复成功!',
		                confirmButton: '确定',
		                confirm: function(){
		                	location.href = pars.URL +'?id='+$('input[name=id]').val();
		                }
		            });
                }
            }
        });
    });
}

/**
 * 讨论提问
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 * @return  {}   
 */
function discussion_quiz() {
	$('#list_form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            title: {/*键名username和input name值对应*/
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '标题不能为空！'
                    }
                }
            },
            content: {/*键名username和input name值对应*/
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '不能为空！'
                    },
                    stringLength: {
                        max:200,
                        message: '内容长度必须少于200字符'
                    }
                }
            }
        }
    });
}

function discussion_edit() {
	$('#list_form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            title: {/*键名username和input name值对应*/
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '标题不能为空！'
                    }
                }
            },
            content: {/*键名username和input name值对应*/
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '不能为空！'
                    },
                    stringLength: {
                        max:200,
                        message: '内容长度必须少于200字符'
                    }
                }
            }
        }
    });
}

/**
 * 讨论详情
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 * @return  {}   
 */
function discussion_detail() {
    $('.right').click(function(){
        $('.option').show();
    });

    $('.content-box').click(function(){
        $('.option').fadeOut();
    });

    $('.history-list').click(function(){
        $('.option').fadeOut();
    });

    /**
     * 删除操作
     * @author mao
     * @version 1.0
     * @date    2016-01-14
     */
    $('#del').click(function(){
        $this = $(this);
        $.confirm({
            title: '提示!',
            content: '是否删除？',
            confirmButton: '确定',
            cancelButton: '取消',
            confirm: function(){

                $.ajax({
                    url:pars.del,
                    type:'get',
                    data:{id:(location.href).split('=')[1]},
                    success:function(res){
                        if(res.code==2){
                            location.href = 'osce/admin/login/index';
                        }else if(res.code==3){
                            $.alert({
                                title: '提示：',
                                content: '无权限删除!',
                                cancelButton:false,
                                confirmButton: '确定',
                                confirm: function(){
                                    $('.option').fadeOut();
                                }
                            });
                        }
                        else{
                            location.href = pars.URL;
                        }
                    }
                })

            },
            cancel: function(){
                $('.option').fadeOut();
            }
        });
    });


    /**
     * 翻页
     * @author mao
     * @version 1.0
     * @date    2016-01-18
     */
    $(window).scroll(function(e){
        if(away_top >= (page_height - window_height)&&now_page<totalpages){
            now_page++;
            //qj.page=now_page;//设置页码
            getItem(now_page,url)
            /*加载显示*/
        }
    });

    //初始化
    var now_page = 1;
    var thisId = (location.href).split('=')[1];
    var url = pars.toPage;
    //内容初始化
    $('.history-list').empty();
    getItem(now_page,url);

    /**
     * 分页的ajax请求
     * @author mao
     * @version 1.0
     * @date    2016-01-18
     * @param   {string}   current 当前页
     * @param   {string}   url     请求地址
     */
    function getItem(current,url){
        $.ajax({
            type:'get',
            url:url,
            aysnc:true,
            data:{id:thisId,pagesize:current},
            success:function(res){
                totalpages = res.total;
                var html = '';
                var index = (current - 1)*10;
                data = res.data.rows;
                for(var i in data){
                    //准备dom
                    //计数
                    var key = (index+1+parseInt(i))
                    html += '<li>'+
                                '<div class="content-header">'+
                                    '<div class="content-l">'+
                                        '<span>'+key+'楼</span>.'+
                                        '<span class="student">'+data[i].name.name+'</span>.'+
                                        '<span class="time">'+data[i].time+'</span>'+
                                    '</div>'+
                                    '<div class="clearfix"></div>'+
                                '</div>'+
                                '<p>'+data[i].content+'</p>'+
                            '</li>';

                }
                //插入
                $('.history-list').append(html);
            }
        });

    }
}