/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        //考试安排
        case "exam_assignment":exam_assignment();break;   
        case "exam_assignment_add":exam_assignment_add();break;                //考试新增
        case "exam_basic_info":exam_basic_info();break;             //基础信息
        case "examroom_assignment":examroom_assignment();break;  //考场安排
        case "station_assignment":station_assignment();break;    //考站安排
        case "examinee_manage":examinee_manage();break;     //考生管理
        case "examinee_manage_add":examinee_manage_add();break;       //考生新增
        case "examinee_manage_edit":examinee_manage_edit();break;   //考生编辑
        case "smart_assignment":smart_assignment();break;     //智能排考
        //咨询&通知
        case "exam_notice": exam_notice();break;
        case "exam_notice_add":exam_notice_add();break;
        case "exam_notice_edit":exam_notice_edit();break;
        //考前培训
        case "train_list": train_list();break;
        case "train_add": train_add();break;
        case "train_edit": train_edit();break;
        //成绩查询
        case "score_query": score_query();break;
        case "score_query_detail": score_query_detail();break;
        //考官安排
        case "examiner_manage":examiner_manage();break;
    }
});

/**
 * 成绩查询
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function score_query() {
    $('#select_Category').change(function(){

        var examId = $(this).val();
        $.ajax({
            type:'get',
            url:pars.URL,
            data:{exam_id:examId},
            success:function(res){
                if(res.code!=1){
                    layer.alert(res.message);
                }else{
                    var data = res.data;
                    var result = [];
                    //数据结构
                    for(var i in data){
                        result.push({id:data[i][0].id,name:data[i][0].name});
                    }

                    //数据去重
                    var _r = {},current = [];
                    for(var i in result){
                        if(!_r[result[i].id]){
                            _r[result[i].id] = true;
                            current.push(result[i]);
                        }
                    }

                    //写入dom
                    var html = '<option value="">全部考站</option>';
                    for(var i in current){
                        html += '<option value="'+current[i].id+'">'+current[i].name+'</option>';
                    }

                    $('#station_Category').html(html);
                }
            },
            error:function(res){
                layer.alert('通讯失败！')
            }
        });
    });
}


function score_query_detail() {
    /**
     * 图表统计
     * @author mao
     * @version 1.0
     * @date    2016-01-29
     * @param   {array}   standard     考核点分数
     * @param   {string}   student_name 学生姓名
     * @param   {array}   avg          考核点平均分
     * @param   {array}   xAxis          考核点
     */
    function charts(standard,student_name,avg,xAxis){

        //考核点数据较少处理
        if(xAxis.length<8){
            var len = 7 - xAxis.length;
            for(var i = 0;i<=len;i++){
                xAxis.push('');
            }
        }

        var option = {
            title : {
                text: '图表分析',
                textStyle:{
                    fontFamily:'Microsoft YaHei',
                    fontSize:16,
                    color:'#676a6c'
                }
            },
            tooltip : {
                trigger: 'axis'
            },
            legend: {
                data:[student_name,'平均分'],
                x:'right'
            },
            toolbox: {
                show : false
            },
            calculable : false,
            xAxis : [
                {
                    type : 'category',
                    boundaryGap : false,
                    axisLine : {
                        lineStyle : {
                            color: '#ddd',
                            width: 1,
                            type: 'solid'
                        }
                    },
                    data : xAxis//['考核点1','考核点2','','','','','']
                }
            ],
            yAxis : [
                {
                    type : 'value',
                    axisLine : {
                        lineStyle : {
                            color: '#ddd',
                            width: 1,
                            type: 'solid'
                        }
                    },
                    axisLabel : {
                        formatter: '{value} 分'
                    }
                }
            ],
            series : [
                {
                    name:student_name,
                    type:'line',
                    smooth:true,
                    symbol:'emptyCircle',
                    symbolSize:4,
                    itemStyle: {
                        normal: {
                            color:'#1ab394',
                            lineStyle:{
                                color:'#1ab394'
                            },
                            areaStyle: {
                                color:'rgba(26,179,148,.3)',
                                type: 'default'
                            }
                        }
                    },
                    data:standard//[30, 82, 34, 91, 90, 30, 10]
                },
                {
                    name:'平均分',
                    type:'line',
                    symbol:'emptyCircle',
                    symbolSize:4,
                    smooth:true,
                    itemStyle: {
                        normal: {
                            color:'#ccc',
                            lineStyle:{
                                color:'#ccc'
                            },
                            areaStyle: {
                                type: 'default'
                            }
                        }
                    },
                    data:avg//[55, 67, 76, 68, 60, 68, 77]
                }
            ]
        };

        var myChart = echarts.init(document.getElementById('score')); 
        myChart.setOption(option);
    }

    //考核点分数
    var standard = [],avg = [],xAxis = [];
    $('#standard li').each(function(key,elem){
        standard.push($(elem).attr('value'));
    });

    $('#avg li').each(function(key,elem){
        avg.push($(elem).attr('value'));
    });

    if(standard.length>avg.length){
        for(var i in standard){
            xAxis.push('考核点'+(parseInt(i)+1));
        }
    }else{
        for(var i in standard){
            xAxis.push('考核点'+(parseInt(i)+1));
        }
    }

    console.log(standard,avg,xAxis)
    //触发图表格
    charts(standard,$('#student').text(),avg,xAxis);


    /**
     * 图片下载页面弹出
     * @author mao
     * @version 1.0
     * @date    2016-01-28
     */
    $('.fa-picture-o').click(function(){

        //获取img数据
        var img = [];
        $(this).parent().siblings('.img').find('li').each(function(key,elem){

            img.push({src:$(elem).attr('value'),download:$(elem).attr('download')});
        });

        //下载的图片dom结构
        var str = '';
        for(var i in img){
            if(i==0){
                str += '<div class="item active">'+
                      '<img style="height:200px; width:100%;" src="/'+img[i].src+'" alt="...">'+
                      '<div class="carousel-caption">'+
                        '<a href="'+img[i].download+'" target="_blank">下载</a>'+
                      '</div>'+
                    '</div>';
            }else{
                str += '<div class="item">'+
                      '<img style="height:200px; width:100%;" src="/'+img[i].src+'" alt="...">'+
                      '<div class="carousel-caption">'+
                        '<a href="'+img[i].download+'" target="_blank">下载</a>'+
                      '</div>'+
                    '</div>';
            }
            
        }

        //轮播dom准备
        var html = '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel" style="height:220px;">'+
                      '<div class="carousel-inner" role="listbox">'+str+'</div>'+
                      '<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">'+
                        '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>'+
                        '<span class="sr-only">Previous</span>'+
                      '</a>'+
                      '<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">'+
                        '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>'+
                        '<span class="sr-only">Next</span>'+
                      '</a>'+
                    '</div>';



        //弹出容器
        layer.open({
            type: 1,
            closeBtn: 0, //不显示关闭按钮
            title:'',
            area: ['420px', '240px'],
            shift: 2,
            shadeClose: true, //开启遮罩关闭
            content: html
        });

    });
}

/**
 * 考前培训新增
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */ 
function train_add() {
    var start={
            elem: '#start',
            event: 'click',
            format: 'YYYY/MM/DD hh:mm',
            min: laydate.now(),
            max: '2099-06-16 23:59',
            istime: true,
            istoday:false,
            choose: function(datas){
                end.min = datas;
            }
        }
        var end={
            elem: '#end',
            event: 'click',
            format: 'YYYY/MM/DD hh:mm',
            min: laydate.now(),
            max: '2099-06-16 23:59',
            istime: true,
            istoday:false,
            choose: function(datas){
                start.max = datas;
            }
        }

        //时间最小值处理
        $("#end").click(function(){
            end.min = ($('#start').val()).split(' ')[0];
            laydate(end);
        });

        //时间最小值处理
        $("#start").click(function(){

            start.max = ($('#end').val()).split(' ')[0];
            laydate(start);
        });

        laydate.skin('molv');
        laydate(start);
        laydate(end);
        
        $('#form1').bootstrapValidator({
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
                        stringLength: {
                            max:64,
                            message: '用户名必须少于64字符'
                        }
                    }
                },
                address: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '地址不能为空'
                        },
                        stringLength: {
                            max:64,
                            message: '地址长度必须少于64字符'
                        }
                    }
                },
                teacher: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '讲师不能为空'
                        }
                   }
                },
                content: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '内容不能为空'
                        }
                   }
                }
            }
        });
        $(".upload").change(function(){
            var files=document.getElementById("file0").files;
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }

            $.ajaxFileUpload
            ({
                url:pars.URL,
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'json',//
                success: function (data, status)
                {
                    if(data.code!=1){
                        layer.msg('只能上传后缀为".xlsx"或".docx"的文件！',{skin:'msg-error',icon:1});
                    }else{
                        var val=data.url;
                        var point = val.lastIndexOf(".");
                        var type = val.substr(point);
                        var str='<p><input type="hidden" name="file[]" id="" value="'+data.url+'" /><i class="fa fa-2x fa-delicious"></i>&nbsp;'+data.title+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                        $(".upload_list_doc").append(str);
                    }
                    /*if(data.state=='SUCCESS'){
                        var val=data.url;
                        var point = val.lastIndexOf(".");
                        var type = val.substr(point);
                        console.log(type);
                        if(type===".xlsx"|type===".doc"|type===".docx"){
                            var str='<p><input type="hidden" name="file[]" id="" value="'+data.url+'" /><i class="fa fa-2x fa-delicious"></i>&nbsp;'+data.title+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                            $(".upload_list_doc").append(str);
                        }else{
                            layer.msg('只能上传后缀为".xlsx"或".docx"的文件！',{skin:'msg-error',icon:1});
                        }
                    }*/
                },
                error: function (data, status, e)
                {
                    layer.msg('上传失败！',{skin:'msg-error',icon:1});
                }
            });
        }) ;
        /*$(".upload_list").on("click",".fa-remove",function(){
            $(this).parent("p").remove();
        });*/

        $(".fabu_btn").click(function(){
            var start=$("#start").val();
            var end=$("#end").val();
            if(start==""){
                layer.alert('你还没有选择开始时间!',function(its){layer.close(its)});
                return false;
            }
            if(end==""){
                layer.alert('你还没有选择结束时间!',function(its){layer.close(its)});
                return false;
            }
            if(Date.parse(start)>Date.parse(end)){
                layer.alert('请正确设置开始时间和结束时间!',function(its){layer.close(its)});
                return false;
            }
        })


    var ue = UE.getEditor('editor',{
        serverUrl:'/osce/api/communal-api/editor-upload'
    });
    /**
     * 获取文本编辑内容
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     * @return  {[type]}   [为本内容]
     */
    function getContent(){

        var arr = [];
        arr.push(UE.getEditor('editor').getContent());
        return arr.join("\n");
    }

    function delAttch(){
        if(confirm('确认删除附件？'))
        {
            $(this).remove();
        }
    }

    //验证content
    $('.btn-primary').click(function(){
        if(getContent()==''){
            layer.alert('内容不能为空！');
            return false;
        }else{
            return true;
        }
    })

    /**
     * checkbox
     * @author mao
     * @version 1.0
     * @date    2016-01-20
     */
    $(".checkbox_input").click(function(){
        if($(this).find("input").is(':checked')){
            $(this).find(".check_icon ").addClass("check");
        }else{
            $(this).find(".check_icon").removeClass("check");
        }
    });

    /**
     * 附件上传
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    $(".images_uploads").change(function(){
        var files=document.getElementById("file0").files;
        var kb=Math.floor(files[0].size/1024);
        //console.log(kb);
        if(kb>2048){
            layer.alert('文件大小不得超过2M!');
            $("#file0").val('');
            return false;
        }
        $.ajaxFileUpload({
            url:pars.url,
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',
            success: function (data, status){
               if(data.code==1){
                   str='<p><input type="hidden" name="attach[]" id="" value="'+data.data.path+'" />'+data.data.name+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                    //var ln=$(".upload_list").children("p").length;
                    //添加
                    $(".upload_list").append(str);
                }
            },
            error:function(res){
                var data = JSON.parse(res.responseText);
                var str = '';

                for(var i in data){
                    str += data[i][0];
                }

                layer.alert(str);
            }
        });
    }) ;

    /**
     * 删除
     * @author mao
     * @version 1.0
     * @date    2016-01-19
     */
    $(".upload_list").on("click",".fa-remove",function(){

        var thisElement = $(this);
        layer.confirm('确认删除？',{
            title:'删除',
            btn: ['确定','取消'] 
        }, function(index){

            thisElement.parent("p").remove();
            layer.close(index);
        }); 

    });
}

/**
 * 考前培训编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-22
 */
function train_edit() {
    var start={
            elem: '#start',
            event: 'click',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday:false,
            choose: function(datas){
                end.min = datas;
            }
        }
        var end={
            elem: '#end',
            event: 'click',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday:false,
            choose: function(datas){
                start.max = datas;
            }
        }

        $("#end").click(function(){
            end.min = ($('#start').val()).split(' ')[0];
            laydate(end);
        });

        //时间最小值处理
        $("#start").click(function(){

            start.max = ($('#end').val()).split(' ')[0];
            laydate(start);
        });

        laydate.skin('molv');
        laydate(start);
        laydate(end);


        $('#form1').bootstrapValidator({
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
                        stringLength: {
                            max:64,
                            message: '用户名必须少于64字符'
                        }
                    }
                },
                address: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '地址不能为空'
                        },
                        stringLength: {
                            max:64,
                            message: '地址长度必须少于64字符'
                        }
                    }
                },
                teacher: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '讲师不能为空'
                        }
                   }
                },
                content: {
                    validators: {
                        notEmpty: {/*非空提示*/
                            message: '内容不能为空'
                        }
                   }
                }
            }
        });
        $(".upload").change(function(){
            var files=document.getElementById("file0").files;
            var kb=Math.floor(files[0].size/1024);
            //console.log(kb);
            if(kb>2048){
                layer.alert('文件大小不得超过2M!');
                $("#file0").val('');
                return false;
            }

            $.ajaxFileUpload
            ({
                url:pars.URL,
                secureuri:false,//
                fileElementId:'file0',//必须要是 input file标签 ID
                dataType: 'json',//
                success: function (data, status)
                {
                    if(data.code!=1){
                        layer.msg('只能上传后缀为".xlsx"或".docx"的文件！',{skin:'msg-error',icon:1});
                    }else{
                        str='<p><input type="hidden" name="file[]" id="" value="'+data.url+'" />'+data.title+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                        var ln=$(".upload_list").children("p").length;
                        if(ln<=1){
                            $(".upload_list").append(str);
                        }else{
                            layer.msg('最多上传2个文件！',{skin:'msg-error',icon:1});
                        }
                    }
                },
                error: function (data, status, e)
                {
                    layer.alert('上传失败！',{skin:'msg-error',icon:1});
                }
            });
        }) ;
        /*$(".upload_list").on("click",".fa-remove",function(){
            $(this).parent("p").remove();
        });*/
        
        $(".fabu_btn").click(function(){
            var start=$("#start").val();
            var end=$("#end").val();
            if(start==""){
                layer.alert('你还没有选择开始时间!',function(its){layer.close(its)});
                return false;
            }
            if(end==""){
                layer.alert('你还没有选择结束时间!',function(its){layer.close(its)});
                return false;
            }
            if(Date.parse(start)>Date.parse(end)){
                layer.alert('请正确设置开始时间和结束时间!',function(its){layer.close(its)});
                return false;
            }
        });



        var ue = UE.getEditor('editor',{
        serverUrl:'/osce/api/communal-api/editor-upload'
    });
    /**
     * 获取文本编辑内容
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     * @return  {[type]}   [为本内容]
     */
    function getContent(){

        var arr = [];
        arr.push(UE.getEditor('editor').getContent());
        return arr.join("\n");
    }

    function delAttch(){
        if(confirm('确认删除附件？'))
        {
            $(this).remove();
        }
    }

    //验证content
    $('.btn-primary').click(function(){
        if(getContent()==''){
            layer.alert('内容不能为空！');
            return false;
        }else{
            return true;
        }
    })

    /**
     * checkbox
     * @author mao
     * @version 1.0
     * @date    2016-01-20
     */
    $(".checkbox_input").click(function(){
        if($(this).find("input").is(':checked')){
            $(this).find(".check_icon ").addClass("check");
        }else{
            $(this).find(".check_icon").removeClass("check");
        }
    });

    /**
     * 附件上传
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    $(".images_uploads").change(function(){
        var files=document.getElementById("file0").files;
        var kb=Math.floor(files[0].size/1024);
        //console.log(kb);
        if(kb>2048){
            layer.alert('文件大小不得超过2M!');
            $("#file0").val('');
            return false;
        }
        $.ajaxFileUpload({
            url:pars.url,
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',
            success: function (data, status){
               if(data.code==1){
                   str='<p><input type="hidden" name="attach[]" id="" value="'+data.data.path+'" />'+data.data.name+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                    //var ln=$(".upload_list").children("p").length;
                    //添加
                    $(".upload_list").append(str);
                }
            },
            error:function(res){
                var data = JSON.parse(res.responseText);
                var str = '';

                for(var i in data){
                    str += data[i][0];
                }

                layer.alert(str);
            }
        });
    }) ;

    /**
     * 删除
     * @author mao
     * @version 1.0
     * @date    2016-01-19
     */
    $(".upload_list").on("click",".fa-remove",function(){

        var thisElement = $(this);
        layer.confirm('确认删除？',{
            title:'删除',
            btn: ['确定','取消'] 
        }, function(index){

            thisElement.parent("p").remove();
            layer.close(index);
        }); 

    });
}


/**
 * 考前培训
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function train_list() {
    $(".fa-trash-o").click(function(){
        var thisElement=$(this);

        layer.confirm('确认删除？', {
            title:"删除",
            btn: ['确定','取消'] //按钮
        }, function(its){
            $.ajax({
                type:'get',
                async:true,
                url:pars.URL + "?id="+thisElement.parent().parent().parent().attr('value'),
                success:function(data){
                    if(data.code == 1){
                        layer.msg('删除成功',{skin:'msg-success',icon:1});
                        setTimeout(function () {
                            location.href= pars.reloads+ '?page=1';
                        },1000)

                    }else {
                        layer.close(its);
                        layer.msg(data.message,{skin:'msg-error',icon:1});
                    }
                },
                error:function(data){
                    layer.close(its);
                    layer.msg('没有权限！',{skin:'msg-error',icon:1});
                }
            })
        });
    })
}

/**
 * 考试安排
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function exam_assignment(){

    $('table').on('click','.fa-trash-o',function(){

        var thisElement = $(this);
        layer.confirm('确认删除？', {
            title:'删除',
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type:'post',
                async:true,
                url:pars.deletes,
                data:{id:thisElement.parent().parent().parent().attr('value')},
                success:function(res){
                    if(res.code==1){
                        location.href = (location.href).split('?')[0];
                    }else{
                        layer.msg(res.message,{'skin':'msg-error',icon:1})
                    }
                }
            })
        });
    })
}

/**
 * 新增考试
 * @author mao
 * @version 1.0
 * @date    2016-01-04
 * @return  {[type]}   [description]
 */
function exam_assignment_add(){
    //时间选择
    timePicker(pars.background_img);


    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                validators: {
                    notEmpty: {
                        message: '考试名称不能为空'
                    }
                }
            },
            address: {
                validators: {
                    notEmpty: {
                        message: '考试地点不能为空'
                    }
                }
            },
            sequence_cate:{
                validators:{
                    notEmpty:{
                        message:'考试顺序不能为空'
                    }
                }
            }
        }
    });

    $('tbody').on('keyup','.end',function(e){
        
        var re = RegExp('/^\d{4}-(?:0\d|1[0-2])-(?:[0-2]\d|3[01])( (?:[01]\d|2[0-3])\:[0-5]\d)?$/');
        var thisElement = $(this);
        if(e.keyCode){
            if(!re.test(thisElement.val())){
                layer.alert('时间不能为空！');
                thisElement.focus();
                return;
            }else{
                return;
            }
        }
    });

    $('#save').click(function(){
        var flag = null;
        $('tbody').find('.laydate').each(function(key,elem){
            flag = true;
            if($(elem).find('input').val()==''){
                flag = false;
            }
        });
        if(flag==false){
            layer.alert('时间不能为空！');
            return false;
        }
        if(flag==null){
            layer.alert('未设置考试时间！');
            return false;
        }
    });
    /**
     * 新增考试 考生同时进出/考生分阶段考试
     * @author chenxia
     * @version 3.3
     * @date    2016-04-05
     */
    //要求考生同时进出考站（考站的时间采用最长考站时间）
    $(".checkbox_one").click(function(){
        if ($(this).find("input").is(':checked')) {
            $(this).find(".check_icon ").addClass("check");
            $(this).find("input").attr("checked","checked");
            $(this).find("input").val("1")
        } else {
            $(this).find(".check_icon").removeClass("check");
            $(this).find("input").removeAttr("checked","checked")
            $(this).find("input").val("0")
        }
    })
    /**
     * 阶段选择框
     * @author chenxia
     * @version 3.4
     * @date    2016-04-06
     */
    //考生分阶段考试
    $(".checkbox_two").click(function(){
        if ($(this).find("input").is(':checked')) {
            $(this).find(".check_icon ").addClass("check");
            $(this).find("input").attr("checked","checked")
            $(this).parent().find(".check_div input").removeAttr('readonly');
            $(this).find(".check_icon ").attr("checkbox","1");
            $(this).find("input").val("1");
            $(".checkbox_num").val("1")
            //当选中后在取消后在选中，替换掉以前的
            $('table tr td select').each(function(){
                //移除所有的option
                $('table tr td select option').remove();
                //获取考生分阶段考试的值
                var checkbox_num=$(".checkbox_num").val(),
                    number = ['','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'];

                var checkbox_number='';
                for(var i=1;i<=checkbox_num;i++) {
                    checkbox_number += '<option value="'+i+'">'+'阶段'+number[i]+'</option>';
                }
                var html =  checkbox_number;
                //记录计数
                $('#exam_add').find('select').append(html);
            });
        } else {
            $(this).find(".check_icon").removeClass("check");
            $(this).parent().find(".check_div input").attr('readonly','readonly');
            $(this).find(".check_icon ").attr("checkbox","0");
            $(this).find("input").removeAttr("checked","checked");
            $(this).find("input").val("0")
            //当选中后在取消后，替换掉以前的
            $('table tr td select').each(function(){
                //移除所有的option
                $('table tr td select option').remove();

                var html = '<option value="1">'+'阶段一'+'</option>';
                //记录计数
                $('#exam_add').find('select').append(html);
            });
        }
    })

    /**
     * 新增一条
     * @author chenxia
     * @version 3.3
     * @date        2016-04-06
     */
    $('#add-new').click(function(){
        //计数器标志
        var index = $('#exam_add').find('tbody').attr('index');
        index = parseInt(index) + 1;

        //获取考生分阶段考试的值
        var checkbox_num=$(".checkbox_num").val(),
            number = ['','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'];
        /**
         * 这里是生成<阶段>
         */
        var checkbox_number = '';
        for(var i=1;i<=checkbox_num;i++) {
            checkbox_number += '<option value="'+i+'">'+'阶段'+number[i]+'</option>';
        }
        //时长默认值
        var timeLength = (Time.getTime('YYYY-MM-DD hh:mm')).split(' ')[1];
        var hours = timeLength.split(':')[0];
        var minutes = timeLength.split(':')[1];

         var html = '<tr index= '+parseInt(index)+'>'+
             '<td>'+parseInt(index)+'</td>'+
             '<td class="laydate">'+
             '<input type="text" class="laydate-icon end" readonly="readonly" name="time['+parseInt(index)+'][begin_dt]" value="'+Time.getTime('YYYY-MM-DD')+' 00:00"/>'+
             '</td>'+
             '<td class="laydate">'+
             '<input type="text" class="laydate-icon end" readonly="readonly" name="time['+parseInt(index)+'][end_dt]" value="" placeholder="YYYY-MM-DD hh:mm"/>'+
             '</td>'+
             '<td>0天0小时0分</td>'+
             '<td class="check_select">' +
             '<select class="form-control" name="time['+parseInt(index)+'][gradation_order]" >'+checkbox_number+'</select>'+
             '</td>'+
             '<td>'+
             '<a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
             '</td>'+
             '</tr>';
         //记录计数
         $('#exam_add').find('tbody').attr('index',index);
         $('#exam_add').find('tbody').append(html);

    });
    /**
     * 这是阶段输入框失去光标的时候的判断
     * @author chenxia
     * @version 3.4
     * @date    2016-04-06
     */
    $(".checkbox_num").blur(function(){
        //获取阶段输入框的值
        var checkbox_num=$(".checkbox_num").val();
        //这里是判断《阶段》后面的输入框是不是输入的1-20的整数
        var pattern = /^(\d|1\d|20)$/gm;

        if(!pattern.test(checkbox_num)){
            layer.alert('请输入1-20的整数！');
            $(".layui-layer-btn0").click(function(){
                $(".checkbox_num").val("1").focus();
            })
            return false;
        }
        //这里是判断输入的值是否大于20
        if(checkbox_num>20||checkbox_num==0){
            layer.alert('请输入1-20的整数！');
            $(".layui-layer-btn0").click(function(){
                $(".checkbox_num").val("1").focus();
            })
            return false;
        }else{
            $('table tr td select').each(function(){
                //移除所有的option
                $('table tr td select option').remove();
                //获取考生分阶段考试的值;
                var number = ['','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'];

                    var checkbox_number='';
                    for(var i=1;i<=checkbox_num;i++) {
                        checkbox_number += '<option value="'+i+'">'+'阶段'+number[i]+'</option>';
                    }

                var html = checkbox_number;
                    //记录计数
                    $('#exam_add').find('select').append(html);
                });
        }
    })

    /**
     * 删除一条记录
     * @author mao
     * @version 1.0
     * @date    2016-01-05
     */
    $('#exam_add').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        layer.confirm('确认删除？',{
            title:'删除',
            btn: ['确定','取消'] 
        },function(thisID){
            thisElement.remove();

            //计数器标志
            var index = $('#exam_add').find('tbody').attr('index');
            if(index<1){
                index = 0;
            }else{
                index = parseInt(index) - 1;
            }
            $('#exam_add').find('tbody').attr('index',index);
            //更新序号
            $('#exam_add tbody').find('tr').each(function(key,elem){
                $(elem).find('td').eq(0).text(parseInt(key)+1);
            });

            layer.close(thisID);
        });
        //var thisElement = $(this).parent().parent().parent().parent();
        //thisElement.remove();
        

    });



    $('#quick-add').click(function() {
        layer.open({
          type: 2,
          title: '快速新增时间',
          shadeClose: true,
          shade: 0.8,
          area: ['60%', '90%'],
          content: '/osce/admin/station/test' //iframe的url
        }); 
    })

}

/**
 * 新增考试 基础信息
 * @author mao
 * @version 1.0
 * @date    2016-01-04
 */
function exam_basic_info(){
    //时间选择
    timePicker(pars.background_img);

    /**
     * 表单验证信息
     * @type {String}
     */
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            name: {
                validators: {
                    notEmpty: {
                        message: '考试名称不能为空'
                    }
                }
            },
            address: {
                validators: {
                    notEmpty: {
                        message: '考试地点不能为空'
                    }
                }
            },
            sequence_cate: {
                validators: {
                    notEmpty: {
                        message: '考试顺序不能为空'
                    }
                }
            }
        }
    });


    $('tbody').on('keyup','.end',function(e){
        
        var re = RegExp('/^\d{4}-(?:0\d|1[0-2])-(?:[0-2]\d|3[01])( (?:[01]\d|2[0-3])\:[0-5]\d)?$/');
        var thisElement = $(this);
        if(e.keyCode){
            if(!re.test(thisElement.val())){
                layer.alert('时间不能为空！');
                thisElement.focus();
                return;
            }else{
                return;
            }
        }
    });

    /**
     * 验证不能为空
     * @author mao
     * @version 1.0
     * @date    2016-02-19
     */
    $('#save').click(function(){
        var flag = null;
        $('tbody').find('.laydate').each(function(key,elem){
            flag = true;
            if($(elem).find('input').val()==''){
                flag = false;
            }
        });
        if(flag==false){
            layer.alert('时间不能为空！');
            return false;
        }
        if(flag==null){
            layer.alert('未设置考试时间！');
            return false;
        }
    });
    /**
     * 新增考试 考生同时进出/考生分阶段考试
     * @author chenxia
     * @version 3.3
     * @date    2016-04-05
     */
        //要求考生同时进出考站（考站的时间采用最长考站时间）
    $(".checkbox_one").click(function(){
        if ($(this).find("input").is(':checked')) {
            $(this).find(".check_icon ").addClass("check");
            $(this).find("input").attr("checked","checked");
            $(this).find("input").val("1")
        } else {
            $(this).find(".check_icon").removeClass("check");
            $(this).find("input").removeAttr("checked","checked");
            $(this).find("input").val("0")
        }
    })
    /**
     * 阶段选择框
     * @author chenxia
     * @version 3.4
     * @date    2016-04-06
     */
    //考生分阶段考试
    $(".checkbox_two").click(function(){
        if ($(this).find("input").is(':checked')) {
            $(this).find(".check_icon ").addClass("check");
            $(this).find(".check_icon ").attr("checkbox","1")
            $(this).parent().find(".check_div input").removeAttr('readonly');
            $(this).find("input").attr("checked","checked");
            $(this).find("input").val("1");
            $(".checkbox_num").val("1")
            //当选中后在取消后在选中，替换掉以前的
            $('#add-basic tr td select').each(function(){
                //移除所有的option
                $('table tr td select option').remove();
                //获取考生分阶段考试的值
                var checkbox_num=$(".checkbox_num").val(),
                    number = ['','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'];

                    var checkbox_number='';
                    for(var i=1;i<=checkbox_num;i++) {
                        checkbox_number += '<option value="'+i+'">'+'阶段'+number[i]+'</option>';
                    }

                    var html =  checkbox_number;
                    //记录计数
                    $('#add-basic').find('select').append(html);
            });
        } else {
            $(this).find(".check_icon").removeClass("check");
            $(this).find(".check_icon ").attr("checkbox","0");
            $(this).parent().find(".check_div input").attr('readonly','readonly');
            $(this).find("input").removeAttr("checked","checked");
            $(this).find("input").val("0")
            //移除所有的option
            $('table tr td select option').remove();
            //当选中后在取消后，替换掉以前的
            $('#add-basic tr td select').each(function(){
                //移除所有的option
                $('table tr td select option').remove();

                var html = '<option value="1">'+'阶段一'+'</option>';
                //记录计数
                $('#add-basic').find('select').append(html);

            });
        }
    })

    /**
     * 新增一条
     * @author chenxia
     * @version 3.3
     * @date        2016-04-06
     */
    $('#add-new').click(function(){
        //计数器标志
        var index = $('#add-basic').find('tbody').attr('index');
        index = parseInt(index) + 1;


        //获取考生分阶段考试的值
        var checkbox_num=$(".checkbox_num").val(),
            number = ['','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'];


        //时长默认值
        var timeLength = (Time.getTime('YYYY-MM-DD hh:mm')).split(' ')[1];
        var hours = timeLength.split(':')[0];
        var minutes = timeLength.split(':')[1];

            /**
             * 这里是生成<阶段>
             */
            var checkbox_number = '';
            for(var i=1;i<=checkbox_num;i++) {
                checkbox_number += '<option value="'+i+'">'+'阶段'+number[i]+'</option>';
            }
            var html = '<tr>'+
                '<td>'+parseInt(index)+'</td>'+
                '<td class="laydate">'+
                '<input type="text" class="laydate-icon end" readonly="readonly" name="time['+parseInt(index)+'][begin_dt]" value="'+Time.getTime('YYYY-MM-DD')+' 00:00"/>'+
                '</td>'+
                '<td class="laydate">'+
                '<input type="text" class="laydate-icon end" readonly="readonly" name="time['+parseInt(index)+'][end_dt]" value="" placeholder="YYYY-MM-DD hh:mm"/>'+
                '</td>'+
                '<td>0天0小时0分</td>'+
                '<td class="check_select">' +
                '<select class="form-control" name="time['+parseInt(index)+'][gradation_order]" >'+checkbox_number+'</select>'+
                '</td>'+
                '<td>'+
                '<a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                '</td>'+
                '</tr>';
            //记录计数
            $('#add-basic').find('tbody').attr('index',index);
            $('#add-basic').find('tbody').append(html);
    });
    /**
     * 阶段输入框
     * @author chenxia
     * @version 3.4
     * @date    2016-04-06
     */
    $(".checkbox_num").blur(function(){
        $('#add-basic tr td select').each(function(){
            //移除所有的option
            $('table tr td select option').remove();

            //获取考生分阶段考试的值
            var checkbox_num=$(".checkbox_num").val(),
                number = ['','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'];
            //这里是判断《阶段》后面的输入框是不是输入的1-20的整数
            var pattern = /^(\d|1\d|20)$/gm;

            if(!pattern.test(checkbox_num)){
                layer.alert('请输入1-20的整数！');
                $(".layui-layer-btn0").click(function(){
                    $(".checkbox_num").val("1").focus();
                })
                return false;
            }
            //这里是判断输入的值是否大于20
            if(checkbox_num>20||checkbox_num==0){
                layer.alert('请输入1-20的整数！');
                $(".layui-layer-btn0").click(function(){
                    $(".checkbox_num").val("1").focus();
                })
                return false;
            }else{

                /**
                 * 这里是生成<阶段>
                 */
                var checkbox_number = '';
                for(var i=1;i<=checkbox_num;i++) {
                    checkbox_number += '<option value="'+i+'">'+'阶段'+number[i]+'</option>';
                }
                var html = checkbox_number;
                //记录计数
                $('#add-basic').find('select').append(html);
            }
        });
    })

    /**
     * 删除一条记录
     * @author mao
     * @version 1.0
     * @date    2016-01-05
     */
    $('#add-basic').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();

        layer.confirm('确认删除？',{
            title:'删除',
            btn: ['确定','取消'] 
        },function(thisID){
            thisElement.remove();

            //计数器标志
            var index = $('#add-basic').find('tbody').attr('index');
            if(index<1){
                index = 0;
            }else{
                index = parseInt(index) - 1;
            }
            $('#add-basic').find('tbody').attr('index',index);
            //更新序号
            $('#add-basic tbody').find('tr').each(function(key,elem){
                $(elem).find('td').eq(0).text(parseInt(key)+1);
            });

            layer.close(thisID);
        });

        //var thisElement = $(this).parent().parent().parent().parent();
        //thisElement.remove();
        

    });

}


/**
 * 获取所要格式的时间
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 * @param   {object}           [外部开放接口]
 */
var Time = (function(mod){
    /**
     * 小于10时的处理
     * @author mao
     * @version 1.0
     * @date    2016-01-06
     * @param   {number}   data 传入参数
     */
    function Convert(data){
        if(data>9){
            return data;
        }else{
            return '0'+data;
        }
    }

    /**
     * 获取时间
     * @author mao
     * @version 1.0
     * @date    2016-01-06
     * @param   {string} YYYY-MM-DD, YYYY-MM-DD hh:mm
     * @return  {string}   时间格式
     */
    mod.getTime = function(format){
        var now = new Date(),
            time = '';

        time = now.getFullYear()+'-'+(now.getMonth()>9?(now.getMonth()+1):'0'+(now.getMonth()+1))+'-'+Convert(now.getDate());

        if(format=='YYYY-MM-DD'){

            return time;
        }else if(format=='YYYY-MM-DD hh:mm'){

            return time += ' '+Convert(now.getHours())+':'+Convert(now.getMinutes());
        }else{

            return time += ' '+Convert(now.getHours())+':'+Convert(now.getMinutes())+':'+Convert(now.getSeconds());
        }
    }

    return mod;


})(window.Time||{});


/**
 * 时间选择
 * @author mao
 * @version 1.0
 * @date    2016-01-04
 * @param   {string}   background 图标地址
 * @return  {[type]}              [description]
 */
function timePicker(background){

    /**
     * 日期插件配置
     * @type {Object}
     */
    var option = {
        elem: '.end', //需显示日期的元素选择器
        event: 'click', //触发事件
        format: 'YYYY-MM-DD hh:mm', //日期格式
        istime: true, //是否开启时间选择
        isclear: false, //是否显示清空
        istoday: false, //是否显示今天
        issure: true, //是否显示确认
        festival: true, //是否显示节日
        min: laydate.now(), //最小日期
        max: '2099-12-31 23:59:59', //最大日期
        fixed: true, //是否固定在可视区域
        zIndex: 99999999, //css z-index
        choose: function(date){ //选择好日期的回调
            var thisElement = $(this.elem).parent();
            if(thisElement.prev().prev().length){
                var current = Date.parse(date.split('-').join('/')) - Date.parse((thisElement.prev().find('input[type=text]').val()).split('-').join('/'));
                var days = Math.floor(current/(1000*60*60*24)),
                    hours = Math.floor((current/(1000*60*60*24)-days)*24),
                    minutes = Math.round((((current/(1000*60*60*24)-days)*24)-hours)*60);
                thisElement.next().text(days+'天'+hours+'小时'+minutes+'分');
            }else{
                //加三小时
                var nextTime = new Date(Date.parse(date.split('-').join('/'))+60*3*60*1000);
                thisElement.next().find('input').val(formatDateTime(nextTime));

                var current = Date.parse((thisElement.next().find('input[type=text]').val()).split('-').join('/')) - Date.parse(date.split('-').join('/'));
                var days = Math.floor(current/(1000*60*60*24)),
                    hours = Math.floor((current/(1000*60*60*24)-days)*24),
                    minutes = Math.round((((current/(1000*60*60*24)-days)*24)-hours)*60);
                thisElement.next().next().text(days+'天'+hours+'小时'+minutes+'分');


                
            }
        }
    };

    /**
     * 标准化时间转化成所要格式
     * @author mao
     * @version 1.0
     * @date    2016-03-15
     * @param   {date}   date 传入时间
     * @return  {date}        所要格式时间
     */
    function formatDateTime(date) {  
        var y = date.getFullYear();  
        var m = date.getMonth() + 1;  
        m = m < 10 ? ('0' + m) : m;  
        var d = date.getDate();  
        d = d < 10 ? ('0' + d) : d;  
        var h = date.getHours();  
        var minute = date.getMinutes();  
        minute = minute < 10 ? ('0' + minute) : minute;  
        return y + '-' + m + '-' + d+' '+h+':'+minute;  
    }; 


    /**
     * 日期选择
     * @author mao
     * @version 1.0
     * @date    2016-01-04
     */
    $('table').on('click','.end',function(){

        //限制时间选择
        var thisElement = $(this).parent();
        if(!thisElement.prev().prev().length){
            option.max = (thisElement.next().find('input').val()).split(' ')[0];
        }else{
            option.min = (thisElement.prev().find('input[type="text"]').val()).split(' ')[0];
            option.max = '2099-12-31 23:59:59';
        }

        //每一次点击都进行一次随机
        var id = Math.floor(Math.random()*9999);
        id = id.toString();
        option.elem = '.'+id;
        $(this).addClass(id);
        $(this).attr('id',id);
        //数据绑定
        laydate(option);
    });

    /**
     * 显示图标
     * @author mao
     * @version 1.0
     * @date    2016-01-04
     */
    $('table').on('mouseleave','.laydate',function(){
        $(this).find('input').css('background-image','none')
    });

    $('table').on('mouseenter','.laydate',function(){
        //图标路径
        var url = background+"/skins/default/icon2.png";
        $(this).find('input').css('background-image','url('+url+')');
    });

}



function examroom_assignment(){


    //select2初始化
    //$(".js-example-basic-multiple").select2();

    /**
     * 将保存的数据保存
     */
    var arrStore = [],selected = []; //arrStore保存的数据，selected所有id值数组
    $('#examroom').find('tbody').find('tr').each(function(key,elem){

        var current = $(elem).find('td').eq(1).find('select').val();
        for(var i in current){
            selected.push(current[i]);
        }
    });

    //数组去重
    var _n = {},_m = {};//_n哈希表，_m哈希表记录数组元素重复次数
    for(var i = 0; i < selected.length; i++) 
    {
        if (!_n[selected[i]]) 
        {
            _n[selected[i]] = true;
            _m[selected[i]] = 1;
            arrStore.push({id:selected[i],count:1}); 
        }else{
            _m[selected[i]] += 1;
        }
    }

    //组装数据
    for(var i in arrStore){
        arrStore[i].count = _m[arrStore[i].id];
    }

    $('#examroom').find('tbody').attr('data',JSON.stringify(arrStore));


    /**
     * 遍历已选的id
     * @author mao
     * @version 1.0
     * @date    2016-01-18
     * @return  {array}   id数组
     */
    function getStations(){
        var arrStore = [];
        $('#examroom').find('tbody').find('tr').each(function(key,elem){

            var selected = $(elem).find('td').eq(1).find('select').val();
            for(var i in selected){
                arrStore.push(selected[i]);
            }

        });
        return arrStore;
    }

    /**
     * select2初始化
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    /*$.ajax({
        type:'get',
        async:true,
        url:pars.list,     //请求地址
        success:function(res){
            //数据处理
            var str = [];
            if(res.code!=1){
                layer.alert(res.message);
                return;
            }else{
                var data = res.data;
                for(var i in data){
                    str.push({id:data[i].id,text:data[i].name});
                }
                //动态加载进去数据
                $(".room-station").select2({data:str});
            }
        }
    });*/


    $('.room-station').select2({
        placeholder: "==请选择==",
        minimumResultsForSearch: Infinity,
        ajax:{
            url: pars.list,
            delay:0,
            data: function (elem) {

                //请求参数
                return {
                    station_id:getStations()
                };
            },
            dataType: 'json',
            processResults: function (res) {

                //数据格式化
                var str = [];
                var data = res.data;
                for(var i in data){
                    str.push({id:data[i].id,text:data[i].name});
                }

                //加载入数据
                return {
                    results: str
                };
            }

        }

    });





    /**
     * 选择必考项
     * @author mao
     * @version 1.0
     * @date    2016-01-05
     */
    $('#examroom').on('change','.js-example-basic-multiple',function(){
        //值初始化
        var current ,
            num = ['必考','必考','二选一','三选一','四选一','五选一','六选一','七选一','八选一','九选一','十选一'];

        current = $(this).val();
        if(current==undefined){

            $(this).parent().siblings('.necessary').text(num[0]);
        }else{

            $(this).parent().siblings('.necessary').text(num[current.length]);
        }

        /**
         * 选择数据
         * @author mao
         * @version 1.0
         * @date    2016-01-13
         */
    }).on("select2:select", function(e){

        //限制选择数量
        if(($(e.target).val()).length>10){
            layer.alert('不能大于10条数据！');
            return;
        }

        var select2_data = e.params.data;
        //检测id相同的教室 如果有就不存如返回，没有就请求并存入
        var rooms = $('#examroom').find('tbody').attr('data');
        var rooms_flag = 0;
        if(rooms==null){
            rooms = [];
            rooms.push({id:select2_data.id,count:1});
        }else{

            rooms = JSON.parse(rooms);
            var current = [],
                count = 0;
            for(var i in rooms){
                //有相同教室id
                if(rooms[i].id==select2_data.id){
                    var cr = rooms[i].count+1;
                    current.push({id:rooms[i].id,count:cr});
                    count = 1;
                }else{
                    current.push({id:rooms[i].id,count:rooms[i].count});
                }
            }
            //存入没有的教室id
            if(!count){
                current.push({id:select2_data.id,count:1});
            }
            //判断数据时候有变化
            if(current.length==rooms.length){
                rooms_flag = 1;
            }
            rooms = current;
        }
        $('#examroom').find('tbody').attr('data',JSON.stringify(rooms));
        //相同id不请求
        if(rooms_flag){
            return;
        }


        //考站数据请求
        $.ajax({
            type:'get',
            url:pars.url,
            data:{id:e.params.data.id},
            async:true,
            success:function(res){

                //记录数据
                var thisElement = $('#exam-place').find('tbody');

                if(res.code!=1){
                    layer.alert(res.message);
                    return;
                }else{
                    var data = res.data,
                        html = '';


                    //准备dom
                    var station_index = parseInt(thisElement.attr('index'));
                    for(var i in data){

                        var teacher = '<option value="">==请选择==</option>';
                        var typeValue = [0,'技能操作站','SP站','理论操作站'];

                        //写入dom 筛选操作，sp、理论、技能
                        if(data[i].type==2){

                            html += '<tr class="parent-id-'+e.params.data.id+'">'+
                                '<td>'+(station_index+parseInt(i)+1)+'<input type="hidden" name="station['+(station_index+parseInt(i)+1)+'][id]" value="'+data[i].id+'"/></td>'+
                                '<td>'+data[i].name+'</td>'+
                                '<td>'+typeValue[data[i].type]+'</td>'+
                                '<td>'+
                                '<select class="form-control teacher-teach js-example-basic-multiple" name="station['+(station_index+parseInt(i)+1)+'][teacher_id]">'+teacher+'</select>'+
                                '</td>'+
                                '<td class="sp-teacher">'+
                                '<div class="teacher-box pull-left">'+
                                '</div>'+
                                '<div class="pull-right" value="'+(station_index+parseInt(i)+1)+'">'+
                                /*'<select name="" class="teacher-list js-example-basic-multiple">'+
                                '<option>==请选择==</option>'+
                                '</select>'+*/
                                '<div class="btn-group">'+
                                  '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                                  '<span class="caret"></span>'+
                                  '</button>'+
                                  '<ul class="dropdown-menu">'+
                                  '</ul>'+
                                '</div>'+
                                '</div>'+
                                '</td>'+
                                '<td><a href="javascript:void(0)" class="invitaion-teacher">发起邀请</a></td>'+
                                '</tr>';
                        }else{

                            html += '<tr class="parent-id-'+e.params.data.id+'">'+
                                '<td>'+(station_index+parseInt(i)+1)+'<input type="hidden" name="station['+(station_index+parseInt(i)+1)+'][id]" value="'+data[i].id+'"/></td>'+
                                '<td>'+data[i].name+'</td>'+
                                '<td>'+typeValue[data[i].type]+'</td>'+
                                '<td>'+
                                '<select class="form-control teacher-teach js-example-basic-multiple" name="station['+(station_index+parseInt(i)+1)+'][teacher_id]">'+teacher+'</select>'+
                                '</td>'+
                                '<td class="sp-teacher">'+
                                '<div class="teacher-box pull-left">'+
                                '</div>'+
                                '<div class="pull-right" value="'+(station_index+parseInt(i)+1)+'">'+
                                /*'<select name="" class="teacher-list js-example-basic-multiple" disabled="disabled">'+
                                '<option>==请选择==</option>'+
                                '</select>'+*/
                                '<div class="btn-group">'+
                                  '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                                  '<span class="caret"></span>'+
                                  '</button>'+
                                  '<ul class="dropdown-menu">'+
                                  '</ul>'+
                                '</div>'+
                                '</div>'+
                                '</td>'+
                                '<td><a href="javascript:void(0)" class="invitaion-teacher">发起邀请</a></td>'+
                                '</tr>';
                        }

                    }
                    //动态插入考场安排
                    thisElement.append(html);
                    //计数器
                    thisElement.attr('index',(station_index+parseInt(i)+1));


                    /**
                     * 老师类型选择
                     * @author mao
                     * @version 1.0
                     * @date    2016-01-11
                     */
                    $('.teacher-teach').select2({
                        placeholder: "==请选择==",
                        ajax:{
                            url: pars.teacher_list,
                            delay:0,
                            data: function (params) {
                                var ids = [];
                                $('#exam-place').find('tbody').find('tr').each(function(key,elem){
                                    var id = $(elem).find('td').eq(3).find('select option:selected').val();
                                    if(id==null){
                                        return;
                                    }else{
                                        ids.push(id);
                                    }
                                    //ids.push($(elem).find('td').eq(3).find('select option:selected').val());
                                });

                                //谁添的无用代码
                                /*var data    =   new Array;
                                $('.teacher-teach').each(function(){
                                    id  =   $(this).val();
                                    if(id==null){
                                        return;
                                    }else{
                                        for (var i in id)
                                        {
                                            data.push(id[i]);
                                        }
                                    }
                                });*/
                                return {
                                    teacher:ids
                                };
                            },
                            dataType: 'json',
                            processResults: function (res) {

                                //数据格式化
                                var str = [];
                                for(var i in res.data){
                                    str.push({id:res.data[i].id,text:res.data[i].name});
                                }

                                //加载入数据
                                return {
                                    results: str
                                };
                            }

                        }
                    });
                    

                    /**
                     * sp老师选择
                     * @author mao
                     * @version 1.0
                     * @date    2016-01-22
                     */
                    /*var All = $('.btn-group');
                    $('#exam-place').on('click','.btn.btn-default',function(){

                        var btn_group = $(this);
                        var thisElem = $(this).siblings('.dropdown-menu');

                        //老师id
                        var ids = [];
                        All.parent().siblings('.teacher-box').find('.teacher').each(function(key,elem){
                            var id = $(elem).attr('value');
                            if(id==null){
                                return;
                            }else{
                                ids.push(id);
                            }
                        });

                        $.ajax({
                            type:'get',
                            async:true,
                            url:pars.spteacher_list,
                            data:{spteacher_id:ids,station_id:btn_group.parent().parent().attr('value')},
                            success:function(data){
                              var html = '';
                              res = data.data.rows;
                              //提示数据
                              if(res.length==0){
                                layer.alert('没有可选数据！',function(its){
                                    layer.close(its);
                                });
                            }
                              for(var i in res){
                                html += '<li><a href="javascript:void(0)" value="'+res[i].id+'">'+res[i].name+'</a></li>';
                              }
                              thisElem.html(html);
                            }
                          });

                    });*/



                    /*var select2_Object;
                    select2_Object = $('.teacher-list').select2({
                        placeholder: "==请选择==",
                        minimumResultsForSearch: Infinity,
                        ajax:{
                            url: pars.spteacher_list,
                            delay:0,
                            data: function (elem) {

                                //老师id
                                var ids = [];
                                $(select2_Object).parent().siblings('.teacher-box').find('.teacher').each(function(key,elem){
                                    var id = $(elem).attr('value');
                                    if(id==null){
                                        return;
                                    }else{
                                        ids.push(id);
                                    }
                                });


                                //请求参数
                                return {
                                    spteacher_id:ids,
                                    station_id:$(select2_Object).parent().attr('value')
                                };
                            },
                            dataType: 'json',
                            processResults: function (res) {

                                //数据格式化
                                var str = [];
                                for(var i in res.data.rows){
                                    str.push({id:res.data.rows[i].id,text:res.data.rows[i].name});
                                }

                                //加载入数据
                                return {
                                    results: str
                                };
                            }

                        }


                    });*/




                    //});


                }
            }
        });

        /**
         * 删除数据
         * @author mao
         * @version 1.0
         * @date    2016-01-13
         */
    }).on("select2:unselect", function(e){

        var select2_data_del = e.params.data;
        //检测id相同的教室 如果有就不存如返回，没有就请求并存入
        var rooms = JSON.parse($('#examroom').find('tbody').attr('data'));
        var current = [];
        for(var i in rooms){

            if(rooms[i].id==select2_data_del.id){
                if(rooms[i].count>1){
                    rooms[i].count -= 1;
                    current.push({id:rooms[i].id,count:rooms[i].count});
                }else{
                    //删除dom
                    var str = rooms[i].id;
                    $('.parent-id-'+str).remove();
                    var station_count = 1;
                    $('#exam-place').find('tbody').find('tr').each(function(key,elem){
                        var html = '';
                        station_count = key + 1;
                        html = station_count+'<input type="hidden" name="station['+station_count+'][id]" value="'+$(elem).find('td').eq(0).find('input').val()+'">';
                        $(elem).find('td').eq(0).html(html);

                        //更新name序号
                        $(elem).find('td').eq(3).find('select').attr('name','station['+station_count+'][teacher_id]');
                        $(elem).find('td').eq(4).find('.pull-right').attr('value',station_count);
                        $(elem).find('td').eq(4).find('.teacher-box').find('.teacher').each(function(m,n){

                            $(n).find('input').attr('name','station['+station_count+'][spteacher_id][]');
                        });
                    });
                    /*var station_count = 1;
                    $('#exam-place').find('tbody').find('tr').each(function(key,elem){
                        station_count = key + 1;
                        $(elem).find('td').eq(0).text(station_count);
                    });*/

                    //if(station_count==1)station_count -= 1;
                    $('#exam-place').find('tbody').attr('index',station_count);
                    continue;
                }
            }else{
                current.push({id:rooms[i].id,count:rooms[i].count});
            }
        }

        //当数据清空时，让计数归零
        if(current.length==0)$('#exam-place').find('tbody').attr('index',0);

        $('#examroom').find('tbody').attr('data',JSON.stringify(current));

    });

    /**
     * 发起邀请
     * @author mao
     * @version 1.0
     * @date    2016-01-13
     */
    $('#exam-place').on('click','.invitaion-teacher',function(){

        var thisElement = $(this);

        //老师id
        var ids = [];
        thisElement.parent().prev().find('.teacher-box').find('.teacher').each(function(key,elem){
            var id = $(elem).attr('value');
            if(id==null){
                return;
            }else{
                ids.push(id);
            }
        });

        //选择sp老师
        if(ids.length==0){
            layer.alert('请选择sp老师！');
            return;
        };

        //考站id
        var stationId = $(".station_id").val();
        if(stationId==undefined){
            layer.alert('请先保存数据！');
            return;
        }

        $.ajax({
            type:'get',
            url:pars.spteacher_invitition+'?exam_id='+($('.active').find('a').attr('href')).split('=')[1]+'&teacher_id='+ids+'&station_id='+stationId,
            success:function(res){
                if(res.code==1){
                    layer.alert('发起邀请成功！');
                }else{
                    layer.alert((res.message).split(':')[1],{title: '温馨提示'});
                }

            },
            error:function(res){
                var data = JSON.parse(res.responseText);
                var str = '';

                for(var i in data){
                    str += data[i][0];
                }

                layer.alert(str);
            }
        });
        //location.href = pars.spteacher_invitition+'?exam_id&teacher_id='+ids;
    })

    /**
     * 新增一条
     * @author  mao
     * @version  1.0
     * @date        2016-01-05
     */
    $('#add-new').click(function(){

        //新增dom
        var index = $('#examroom').find('tbody').attr('index');
        index = parseInt(index) + 1;

        var html = '<tr class="pid-'+index+'">'+
            '<td>'+index+'</td>'+
            '<td width="498">'+
            '<select class="form-control js-example-basic-multiple room-list" multiple="multiple" name="room['+index+'][]"></select>'+
            '</td>'+
            '<td class="necessary">必考</td>'+
            '<td>'+
            '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
            '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>'+
            '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>'+
            '</td>'+
            '</tr>';
                //记录计数
        $('#examroom').find('tbody').attr('index',index);
        $('#examroom').find('tbody').append(html);

        //ajax请求数据
        $.ajax({
            type:'get',
            async:true,
            url:pars.list,     //请求地址
            success:function(res){
                //数据处理
                var str = [];
                if(res.code!=1){
                    layer.alert(res.message);
                    return;
                }else{
                    var data = res.data;
                    for(var i in data){
                        str.push({id:data[i].id,text:data[i].name});
                    }
                    //动态加载进去数据
                    $(".room-list").select2({data:str});
                }
            }
        });

    });

    /**
     * 删除一条记录
     * @author mao
     * @version 1.0
     * @date    2016-01-05
     */
    $('#examroom').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        layer.confirm('确认删除？',{
            title:'删除',
            btn: ['确定','取消'] 
        },function(its){
            thisElement.remove();

            //计数器标志
            var index = $('#examroom').find('tbody').attr('index');
            if(index<1){
                index = 0;
            }else{
                index = parseInt(index) - 1;
            }
            $('#examroom').find('tbody').attr('index',index);
            //更新序号
            $('#examroom tbody').find('tr').each(function(key,elem){
                $(elem).find('td').eq(0).text(parseInt(key)+1);
            });


            //删除监考数据
            var now_data = thisElement.find('td').eq(1).find('select').val();
            var delStore = JSON.parse($('#examroom').find('tbody').attr('data'));  //存储数据
            var current = [];
            for(var j in now_data){
                for(var i in delStore){

                    if(delStore[i].id==now_data[j]){
                        if(delStore[i].count>1){
                            delStore[i].count -= 1;
                            current.push({id:delStore[i].id,count:delStore[i].count});
                        }else{
                            //删除dom
                            var str = delStore[i].id;
                            $('.parent-id-'+str).remove();
                            //重置序号
                            var station_count = 1;
                            $('#exam-place').find('tbody').find('tr').each(function(key,elem){
                                var html = '';
                                station_count = key + 1;
                                html = station_count+'<input type="hidden" name="station['+station_count+'][id]" value="'+$(elem).find('td').eq(0).find('input').val()+'">';
                                $(elem).find('td').eq(0).html(html);

                                //更新name序号
                                $(elem).find('td').eq(3).find('select').attr('name','station['+station_count+'][teacher_id]');
                                $(elem).find('td').eq(4).find('.pull-right').attr('value',station_count);
                                $(elem).find('td').eq(4).find('.teacher-box').find('.teacher').each(function(m,n){
                                    
                                    $(n).find('input').attr('name','station['+station_count+'][spteacher_id][]');
                                });

                            });
                            $('#exam-place').find('tbody').attr('index',station_count);
                            continue;
                        }
                    }else{
                        current.push({id:delStore[i].id,count:delStore[i].count});
                    }
                }
            }

            //当数据清空时，让计数归零
            if(current.length==0)$('#exam-place').find('tbody').attr('index',0);

            $('#examroom').find('tbody').attr('data',JSON.stringify(current));

            //关闭弹出
            layer.close(its);
        });

    });

    /**
     * 数据条目上移
     * @author marvine
     * @date    2016-01-05
     * @version [1.0]
     */
    $('#examroom').on('click','.fa-arrow-up',function(){
        var thisElement = $(this).parent().parent().parent().parent();

        if(thisElement.prev().length){

            var thisDom = thisElement.clone();
            var className = thisElement.attr('class');
            thisElement.prev().before(thisDom);
            thisElement.remove();

            //获得数据
            var data = [];
            thisElement.find('select').find('option:selected').each(function(key,elem){
                data.push({name:$(elem).text(),id:$(elem).attr('value')});
            });
            //准备option dom
            var html = '';
            for(var i in data){
                html += '<option selected="selected" value="'+data[i].id+'">'+data[i].name+'</option>';
            }
            //初始化
            $('#examroom').find('.'+className).find('td').eq(1).empty().html('<select class="form-control js-example-basic-multiple room-station" multiple="multiple">'+html+'</select>');
            var t = $('#examroom').find('.'+className).find('select').select2({
                placeholder: "==请选择==",
                minimumResultsForSearch: Infinity,
                ajax:{
                    url: pars.list,
                    delay:0,
                    data: function (elem) {
                        console.log(getStations())
                        //请求参数
                        return {
                            station_id:[]
                        };
                    },
                    dataType: 'json',
                    processResults: function (res) {

                        //数据格式化
                        var str = [];
                        var data = res.data;
                        for(var i in data){
                            str.push({id:data[i].id,text:data[i].name});
                        }

                        //加载入数据
                        return {
                            results: str
                        };
                    }

                }
            });

            //更新序号
            var room_index = 1;
            $('#examroom').find('tbody').find('tr').each(function(key,elem){
                $(elem).attr('class','pid-'+room_index);
                $(elem).find('td').eq(0).text(room_index);
                $(elem).find('select').attr('name','room['+room_index+'][]');
                room_index++;
            })
        }else{
            return;
        }
    });

    /**
     * 数据条目下移
     * @author marvine
     * @date    2016-01-05
     * @version [1.0]
     */
    $('#examroom').on('click','.fa-arrow-down',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        if(thisElement.next().length){

            var thisDom = thisElement.clone();
            var className = thisElement.attr('class');
            thisElement.next().after(thisDom);
            thisElement.remove();

            //获得数据
            var data = [];
            thisElement.find('select').find('option:selected').each(function(key,elem){
                data.push({name:$(elem).text(),id:$(elem).attr('value')});
            });
            //准备option dom
            var html = '';
            for(var i in data){
                html += '<option selected="selected" value="'+data[i].id+'">'+data[i].name+'</option>';
            }
            //初始化
            $('#examroom').find('.'+className).find('td').eq(1).empty().html('<select class="form-control js-example-basic-multiple room-station" multiple="multiple">'+html+'</select>');
            var t = $('#examroom').find('.'+className).find('select').select2({
                placeholder: "==请选择==",
                minimumResultsForSearch: Infinity,
                ajax:{
                    url: pars.list,
                    delay:0,
                    data: function (elem) {
                        console.log(getStations())
                        //请求参数
                        return {
                            station_id:[]
                        };
                    },
                    dataType: 'json',
                    processResults: function (res) {

                        //数据格式化
                        var str = [];
                        var data = res.data;
                        for(var i in data){
                            str.push({id:data[i].id,text:data[i].name});
                        }

                        //加载入数据
                        return {
                            results: str
                        };
                    }

                }
            });

            //更新序号
            var room_index = 1;
            $('#examroom').find('tbody').find('tr').each(function(key,elem){
                $(elem).attr('class','pid-'+room_index);
                $(elem).find('td').eq(0).text(room_index);
                $(elem).find('select').attr('name','room['+room_index+'][]');
                room_index++;
            })



        }else{
            return;
        }
    });

    /**
     * 选择老师
     * @author mao
     * @version 1.0
     * @date    2016-01-14
     */
    /*$('#exam-place').on('change',".teacher-list",function(){

        var $teacher= $(this).find('option:selected').text().split('==')[0];
        var id = $(this).find('option:selected').val();
        var thisElement = $(this);

        var sql='<div class="input-group teacher pull-left" value="'+id+'">'+
            '<input type="hidden" name="station['+thisElement.parent().attr('value')+'][spteacher_id][]" value="'+id+'">'+
            '<div class="pull-left">'+$teacher+'</div>'+
            '<div class="pull-left"><i class="fa fa-times"></i></div></div>';
        $(this).parents(".pull-right").prev().append(sql);
    })*/
    
    /**
     * 选择老师 界面效果修改
     * @author mao
     * @version 1.0
     * @date    2016-01-22
     */
    $('#exam-place').on('click',".dropdown-menu",function(e){

        var $teacher= $(e.target).text();
        var id = $(e.target).attr('value');
        var thisElement = $(this).parent();

        var sql='<div class="input-group teacher pull-left" value="'+id+'">'+
            '<input type="hidden" name="station['+thisElement.parent().attr('value')+'][spteacher_id][]" value="'+id+'">'+
            '<div class="pull-left">'+$teacher+'</div>'+
            '<div class="pull-left"><i class="fa fa-times"></i></div></div>';
        $(this).parents(".pull-right").prev().append(sql);
    })

    //删除
    $('#exam-place').on('click',".teacher-box i",function(){
        $(this).parents(".teacher").remove();
    })


    /**
     * 老师类型选择 初始化
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    $('#exam-place').on('click','.btn.btn-default',function(){

        var btn_group = $(this);
        var thisElem = $(this).siblings('.dropdown-menu');

        //老师id
        var ids = [];
        $('#exam-place').find('tbody').find('tr').each(function(n,m){
            $(m).find('td').eq(4).find('.teacher').each(function(key,elem){
                var id = $(elem).attr('value');
                if(id==null){
                    return;
                }else{
                    ids.push(id);
                }
            });
        });

        $.ajax({
            type:'get',
            async:true,
            url:pars.spteacher_list,
            data:{spteacher_id:ids,station_id:btn_group.parent().parent().parent().parent().eq(0).find('input').attr('value')},
            success:function(data){
                if(data.code!=1){
                    layer.alert(data.message);
                }else{
                  var html = '';
                  res = data.data.rows;
                  //提示数据
                  if(res.length==0){
                    layer.alert('没有可选数据！',function(its){
                        layer.close(its);
                    });
                }
                  for(var i in res){
                    html += '<li><a href="javascript:void(0)" value="'+res[i].id+'">'+res[i].name+'</a></li>';
                  }
                  thisElem.html(html);
                }
              
            }
          });

    });
    $('.teacher-teach').select2({
        placeholder: "==请选择==",
        ajax:{
            url: pars.teacher_list,
            delay:0,
            data: function (params) {

                var ids = [];
                $('#exam-place').find('tbody').find('tr').each(function(key,elem){
                    var id = $(elem).find('td').eq(3).find('select option:selected').val();
                    if(id==null){
                        return;
                    }else{
                        ids.push(id);
                    }
                    //ids.push($(elem).find('td').eq(3).find('select option:selected').val());
                });

                return {
                    teacher:ids
                };
            },
            dataType: 'json',
            processResults: function (res) {

                //数据格式化
                var str = [];
                for(var i in res.data){
                    str.push({id:res.data[i].id,text:res.data[i].name});
                }

                //加载入数据
                return {
                    results: str
                };
            }

        }
    });

    
    /**
     * 考场信息验证
     * @author mao
     * @version 1.0
     * @date    2016-01-27
     */
    $('#save').click(function(){

        var status_select = false;
        var status = true;
        $('#examroom tbody').find('select').each(function(key,elem){
            status = false;
            if($(elem).val()==null)status_select = true;
        });
        if(status||status_select){
            layer.alert('考场信息不能为空！');
            return false;
        }
    })

}


/**
 * 考试通知 新增
 * @author mao
 * @version 1.0
 * @date    2016-01-07
 */
function exam_notice_add(){


    /**
     * 表单验证信息
     * @type {String}
     */
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            'groups[]': {
                validators: {
                    notEmpty: {
                        message: '请勾选'
                    }
                }
            },
            title: {
                validators: {
                    notEmpty: {
                        message: '标题不能为空'
                    }
                }
            },
            content: {
                validators: {
                    notEmpty: {
                        message: '内容不能为空'
                    }
                }
            }
        }
    });

    //var ue = UE.getEditor('editor');
    var ue = UE.getEditor('editor',{
        serverUrl:'/osce/api/communal-api/editor-upload'
    });
    /**
     * 获取文本编辑内容
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     * @return  {[type]}   [为本内容]
     */
    function getContent(){

        var arr = [];
        arr.push(UE.getEditor('editor').getContent());
        return arr.join("\n");
    }

    function delAttch(){
        if(confirm('确认删除附件？'))
        {
            $(this).remove();
        }
    }

    //验证content
    $('.btn-primary').click(function(){
        if(getContent()==''){
            layer.alert('内容不能为空！');
            return false;
        }else{
            return true;
        }
    })

    /**
     * checkbox
     * @author mao
     * @version 1.0
     * @date    2016-01-20
     */
    $(".checkbox_input").click(function(){
        if($(this).find("input").is(':checked')){
            $(this).find(".check_icon ").addClass("check");
        }else{
            $(this).find(".check_icon").removeClass("check");
        }
    });

    /**
     * 附件上传
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    $(".images_uploads").change(function(){
    	var files=document.getElementById("file0").files;
    	var kb=Math.floor(files[0].size/1024);
    	//console.log(kb);
        if(kb>2048){
            layer.alert('文件大小不得超过2M!');
            $("#file0").val('');
            return false;
        }
        $.ajaxFileUpload({
            url:pars.url,
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',
            success: function (data, status){
               if(data.code==1){
                   str='<p><input type="hidden" name="attach[]" id="" value="'+data.data.path+'" />'+data.data.name+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                    //var ln=$(".upload_list").children("p").length;
                    //添加
                    $(".upload_list").append(str);
                }
            },
            error:function(res){
                var data = JSON.parse(res.responseText);
                var str = '';

                for(var i in data){
                    str += data[i][0];
                }

                layer.alert(str);
            }
        });
    }) ;

    /**
     * 删除
     * @author mao
     * @version 1.0
     * @date    2016-01-19
     */
    $(".upload_list").on("click",".fa-remove",function(){

        var thisElement = $(this);
        layer.confirm('确认删除？',{
            title:'删除',
            btn: ['确定','取消'] 
        }, function(index){

            thisElement.parent("p").remove();
            layer.close(index);
        }); 

    });


}

/**
 * 考试通知 编辑
 * @author mao
 * @version 1.0
 * @date    2016-01-15
 */
function exam_notice_edit(){

    /**
     * 表单验证信息
     * @type {String}
     */
    $('#sourceForm').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {/*输入框不同状态，显示图片的样式*/
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {/*验证*/
            'groups[]': {
                validators: {
                    notEmpty: {
                        message: '请勾选'
                    }
                }
            },
            title: {
                validators: {
                    notEmpty: {
                        message: '标题不能为空'
                    }
                }
            },
            content: {
                validators: {
                    notEmpty: {
                        message: '内容不能为空'
                    }
                }
            }
        }
    });


    /**
     * 获取文本编辑内容
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     * @return  {[type]}   [为本内容]
     */
    function getContent(){

        var arr = [];
        arr.push(UE.getEditor('editor').getContent());
        return arr.join("\n");
    }


    //验证content
    $('.btn-primary').click(function(){
        if(getContent()==''){
            layer.alert('内容不能为空！');
            return false;
        }else{
            return true;
        }
    })

    var content =   $('#content').html();

    //初始化
    var ue = UE.getEditor('editor',{
        serverUrl:'/osce/api/communal-api/editor-upload'
    });
    //UE.setContent(content);


    //测试数据
    var test_data = '<p style="line-height: 16px;"><img style="vertical-align: middle; margin-right: 2px;" src="http://www.mis.hx/osce/admin/plugins/js/plugins/UEditor/dialogs/attachment/fileTypeImages/icon_jpg.gif"/><a style="font-size:12px; color:#0066cc;" href="/ueditor/php/upload/file/20160115/1452835178916146.jpg" title="1452835178916146.jpg">1452835178916146.jpg</a></p><p style="line-height: 16px;"><img style="vertical-align: middle; margin-right: 2px;" src="http://www.mis.hx/osce/admin/plugins/js/plugins/UEditor/dialogs/attachment/fileTypeImages/icon_jpg.gif"/><a style="font-size:12px; color:#0066cc;" href="/ueditor/php/upload/file/20160115/1452834723154699.jpg" title="1452834723154699.jpg">1452834723154699.jpg</a></p><p style="line-height: 16px;"><img style="vertical-align: middle; margin-right: 2px;" src="http://www.mis.hx/osce/admin/plugins/js/plugins/UEditor/dialogs/attachment/fileTypeImages/icon_jpg.gif"/><a style="font-size:12px; color:#0066cc;" href="/ueditor/php/upload/file/20160107/1452155293584887.jpg" title="1452155293584887.jpg">1452155293584887.jpg</a></p><p style="line-height: 16px;"><img style="vertical-align: middle; margin-right: 2px;" src="http://www.mis.hx/osce/admin/plugins/js/plugins/UEditor/dialogs/attachment/fileTypeImages/icon_jpg.gif"/><a style="font-size:12px; color:#0066cc;" href="/ueditor/php/upload/file/20160107/1452154121985298.jpg" title="1452154121985298.jpg">1452154121985298.jpg</a></p><p><em>fdsafd</em><br/></p><p><span style="font-size: 24px; text-decoration: underline;">csavfdf</span></p><p><strong><span style="font-size: 24px;">fdsafgdsaf</span></strong></p><p><br/></p><p><br/></p><p><span style="font-size: 24px; background-color: rgb(255, 255, 0);">fdsafdaf<img src="http://img.baidu.com/hi/jx2/j_0058.gif"/><span style="font-size: 24px; background-color: rgb(255, 255, 0);"></span></span></p>'

    /**
     * checkbox
     * @author mao
     * @version 1.0
     * @date    2016-01-20
     */
    $(".checkbox_input").click(function(){
        if($(this).find("input").is(':checked')){
            $(this).find(".check_icon ").addClass("check");
        }else{
            $(this).find(".check_icon").removeClass("check");
        }
    });


    /**
     * 插入插入内容
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     * @param   {String}  isAppendTo [插入的内容]
     */
    function setContent(isAppendTo) {
        UE.getEditor('editor').setContent(isAppendTo);
    }

    /**
     * 编辑的富文本编辑内容
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    //数据编辑显示
    var thisID = setInterval(function(){
            setContent(content)
            clearInterval(thisID);
        },1000);

    /**
     * checkbox
     * @author mao
     * @version 1.0
     * @date    2016-01-20
     */
    $(".checkbox_input").click(function(){
        if($(this).find("input").is(':checked')){
            $(this).find(".check_icon ").addClass("check");
        }else{
            $(this).find(".check_icon").removeClass("check");
        }
    });


    /**
     * 附件上传
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    $(".images_uploads").change(function(){
    	var files=document.getElementById("file0").files;
    	var kb=Math.floor(files[0].size/1024);
    	//console.log(kb);
        if(kb>2048){
            layer.alert('文件大小不得超过2M!');
            $("#file0").val('');
            return false;
        }

        $.ajaxFileUpload({
            url:pars.url,
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',
            success: function (data, status){
                 if(data.code==1){
                   str='<p><input type="hidden" name="attach[]" id="" value="'+data.data.path+'" />'+data.data.name+'&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>';
                    //var ln=$(".upload_list").children("p").length;
                    //添加
                    $(".upload_list").append(str);
                }
            },
            error: function (data, status, e){
                layer.alert('通讯失败!');
            }
        });
    }) ;

    /**
     * 删除
     * @author mao
     * @version 1.0
     * @date    2016-01-19
     */
    $(".upload_list").on("click",".fa-remove",function(){

        var thisElement = $(this);
        layer.confirm('确认删除？',{
            title:'删除',
            btn: ['确定','取消'] 
        }, function(index){

            thisElement.parent("p").remove();
            layer.close(index);
        }); 

    });



    /**
     * 获取文本编辑内容
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     * @return  {[type]}   [为本内容]
     */
    function getContent(){
        //
        var arr = [];
        arr.push(UE.getEditor('editor').getContent());
        return arr.join("\n");
    }



}


/**
 * 资讯&通知
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function exam_notice() {
    $('.fa-trash-o').click(function(){
        var thisElement = $(this)
        layer.confirm('确认删除？', {
            title:'删除',
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type:'get',
                url:pars.URL,  //请求地址
                data:{id:thisElement.parent().parent().attr('value')},
                success:function(res){
                    if(res.code!=1){
                        layer.alert(res.message);
                    }else{
                        location.href = pars.reloads;
                    }
                }
            });
        });
    });
}

/*
 * 考试通知 新增
 * @author lizhiyuan
 * @version 2.0
 * @date    2016-01-09
 */

function smart_assignment(){
    //var testData={"code":1,"message":"success","data":{"1":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452556815","end":1452557715,"items":[{"id":3,"name":"\u6d4b\u8bd5\u5b66\u751f6665","exam_id":1,"user_id":54,"idcard":"51068119592467","mobile":"13699450870","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":2,"name":"\u6d4b\u8bd5\u5b66\u751f5910","exam_id":1,"user_id":52,"idcard":"51068119021099","mobile":"13699451304","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"}]},"2":{"begin":"1452557715","end":1452558615,"items":[{"id":1,"name":"\u6d4b\u8bd5\u5b66\u751f2959","exam_id":1,"user_id":50,"idcard":"51068119352986","mobile":"13699450075","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":4,"name":"\u6d4b\u8bd5\u5b66\u751f3870","exam_id":1,"user_id":56,"idcard":"51068119920106","mobile":"13699450386","code":null,"avator":null,"create_user_id":1,"created_at":null,"updated_at":null}]},"3":{"begin":"1452558615","end":1452559515,"items":[]},"4":{"begin":"1452559515","end":1452560415,"items":[]},"5":{"begin":"1452560415","end":1452561315,"items":[]},"6":{"begin":"1452561315","end":1452562215,"items":[]},"7":{"begin":"1452562215","end":1452563115,"items":[]},"8":{"begin":"1452563115","end":1452564015,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452556815","end":1452557715,"items":[]},"2":{"begin":"1452557715","end":1452558615,"items":[]},"3":{"begin":"1452558615","end":1452559515,"items":[]},"4":{"begin":"1452559515","end":1452560415,"items":[]},"5":{"begin":"1452560415","end":1452561315,"items":[]},"6":{"begin":"1452561315","end":1452562215,"items":[]},"7":{"begin":"1452562215","end":1452563115,"items":[]},"8":{"begin":"1452563115","end":1452564015,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452556815","end":1452557715,"items":[{"id":3,"name":"\u6d4b\u8bd5\u5b66\u751f6665","exam_id":1,"user_id":54,"idcard":"51068119592467","mobile":"13699450870","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":2,"name":"\u6d4b\u8bd5\u5b66\u751f5910","exam_id":1,"user_id":52,"idcard":"51068119021099","mobile":"13699451304","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"}]},"2":{"begin":"1452557715","end":1452558615,"items":[{"id":1,"name":"\u6d4b\u8bd5\u5b66\u751f2959","exam_id":1,"user_id":50,"idcard":"51068119352986","mobile":"13699450075","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":4,"name":"\u6d4b\u8bd5\u5b66\u751f3870","exam_id":1,"user_id":56,"idcard":"51068119920106","mobile":"13699450386","code":null,"avator":null,"create_user_id":1,"created_at":null,"updated_at":null}]},"3":{"begin":"1452558615","end":1452559515,"items":[]},"4":{"begin":"1452559515","end":1452560415,"items":[]},"5":{"begin":"1452560415","end":1452561315,"items":[]},"6":{"begin":"1452561315","end":1452562215,"items":[]},"7":{"begin":"1452562215","end":1452563115,"items":[]},"8":{"begin":"1452563115","end":1452564015,"items":[]}}}},"2":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452564015","end":1452564915,"items":[]},"2":{"begin":"1452564915","end":1452565815,"items":[]},"3":{"begin":"1452565815","end":1452566715,"items":[]},"4":{"begin":"1452566715","end":1452567615,"items":[]},"5":{"begin":"1452567615","end":1452568515,"items":[]},"6":{"begin":"1452568515","end":1452569415,"items":[]},"7":{"begin":"1452569415","end":1452570315,"items":[]},"8":{"begin":"1452570315","end":1452571215,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452564015","end":1452564915,"items":[]},"2":{"begin":"1452564915","end":1452565815,"items":[]},"3":{"begin":"1452565815","end":1452566715,"items":[]},"4":{"begin":"1452566715","end":1452567615,"items":[]},"5":{"begin":"1452567615","end":1452568515,"items":[]},"6":{"begin":"1452568515","end":1452569415,"items":[]},"7":{"begin":"1452569415","end":1452570315,"items":[]},"8":{"begin":"1452570315","end":1452571215,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452564015","end":1452564915,"items":[]},"2":{"begin":"1452564915","end":1452565815,"items":[]},"3":{"begin":"1452565815","end":1452566715,"items":[]},"4":{"begin":"1452566715","end":1452567615,"items":[]},"5":{"begin":"1452567615","end":1452568515,"items":[]},"6":{"begin":"1452568515","end":1452569415,"items":[]},"7":{"begin":"1452569415","end":1452570315,"items":[]},"8":{"begin":"1452570315","end":1452571215,"items":[]}}}},"4":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452571215","end":1452572115,"items":[]},"2":{"begin":"1452572115","end":1452573015,"items":[]},"3":{"begin":"1452573015","end":1452573915,"items":[]},"4":{"begin":"1452573915","end":1452574815,"items":[]},"5":{"begin":"1452574815","end":1452575715,"items":[]},"6":{"begin":"1452575715","end":1452576615,"items":[]},"7":{"begin":"1452576615","end":1452577515,"items":[]},"8":{"begin":"1452577515","end":1452578415,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452571215","end":1452572115,"items":[]},"2":{"begin":"1452572115","end":1452573015,"items":[]},"3":{"begin":"1452573015","end":1452573915,"items":[]},"4":{"begin":"1452573915","end":1452574815,"items":[]},"5":{"begin":"1452574815","end":1452575715,"items":[]},"6":{"begin":"1452575715","end":1452576615,"items":[]},"7":{"begin":"1452576615","end":1452577515,"items":[]},"8":{"begin":"1452577515","end":1452578415,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452571215","end":1452572115,"items":[]},"2":{"begin":"1452572115","end":1452573015,"items":[]},"3":{"begin":"1452573015","end":1452573915,"items":[]},"4":{"begin":"1452573915","end":1452574815,"items":[]},"5":{"begin":"1452574815","end":1452575715,"items":[]},"6":{"begin":"1452575715","end":1452576615,"items":[]},"7":{"begin":"1452576615","end":1452577515,"items":[]},"8":{"begin":"1452577515","end":1452578415,"items":[]}}}},"5":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452578415","end":1452579315,"items":[]},"2":{"begin":"1452579315","end":1452580215,"items":[]},"3":{"begin":"1452580215","end":1452581115,"items":[]},"4":{"begin":"1452581115","end":1452582015,"items":[]},"5":{"begin":"1452582015","end":1452582915,"items":[]},"6":{"begin":"1452582915","end":1452583815,"items":[]},"7":{"begin":"1452583815","end":1452584715,"items":[]},"8":{"begin":"1452584715","end":1452585615,"items":[]},"9":{"begin":"1452585615","end":1452586515,"items":[]},"10":{"begin":"1452586515","end":1452587415,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452578415","end":1452579315,"items":[]},"2":{"begin":"1452579315","end":1452580215,"items":[]},"3":{"begin":"1452580215","end":1452581115,"items":[]},"4":{"begin":"1452581115","end":1452582015,"items":[]},"5":{"begin":"1452582015","end":1452582915,"items":[]},"6":{"begin":"1452582915","end":1452583815,"items":[]},"7":{"begin":"1452583815","end":1452584715,"items":[]},"8":{"begin":"1452584715","end":1452585615,"items":[]},"9":{"begin":"1452585615","end":1452586515,"items":[]},"10":{"begin":"1452586515","end":1452587415,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452578415","end":1452579315,"items":[]},"2":{"begin":"1452579315","end":1452580215,"items":[]},"3":{"begin":"1452580215","end":1452581115,"items":[]},"4":{"begin":"1452581115","end":1452582015,"items":[]},"5":{"begin":"1452582015","end":1452582915,"items":[]},"6":{"begin":"1452582915","end":1452583815,"items":[]},"7":{"begin":"1452583815","end":1452584715,"items":[]},"8":{"begin":"1452584715","end":1452585615,"items":[]},"9":{"begin":"1452585615","end":1452586515,"items":[]},"10":{"begin":"1452586515","end":1452587415,"items":[]}}}}}}

    var timesGroup  =   [];
    var timeHeight  =   20;
    var eariestTime =   [];
    var endtime=[];
    var plan    =   $('#plan').html();
    var testData=eval('('+plan+')');
    $('.classroom-box').html('');//清空排考
    maketotal(testData);//页面加载执行排考
    makeTime();
    //最里面dom数据遍历，节点dd
    function makeItem(data){

        var dl  =   $('<dl class="clearfloat">');
        var items   =   data.items;
        var everyHeight=data.end-data.start;//每个单元格的高度
        everyHeight=everyHeight/timeHeight;

        if(timesGroup[data.screening]!=undefined)
        {
            var times   =   timesGroup[data.screening];
        }
        else
        {
            var times   =   [];
        }

        times.push(data.start);
        var startTimeData =   parseInt(data.start)? parseInt(data.start):0;
        var eariestTimeData =   parseInt(eariestTime[data.screening])? parseInt(eariestTime[data.screening]):startTimeData;
        eariestTime[data.screening] =   eariestTimeData<startTimeData? eariestTimeData:startTimeData;
        //时间数组，data.screening代表是哪场考试的时间戳
        timesGroup[data.screening] =   times;
        var endTimeData =   endtime[data.screening];
        if(endTimeData==undefined)
        {
            endTimeData=0;
        }
        endTimeData =   data.end>endTimeData? data.end:endTimeData;//取最大的结束时间
        endtime[data.screening]    =   endTimeData;
        dl.css("height",everyHeight+"px");
        for(var i in items)
        {
            var dd  = $('<dd>');
            if(items[i]!=null)
            {
                dd.text(items[i].name+",");
                dd.attr("sid",items[i].id);
                dd.addClass('student_'+items[i].id).addClass('stu');
                dd.attr("data-sid",items[i].id);
                dd.bind("click",changeStudent);
            }
            dl.append(dd);
        }
        return dl;
    }
    //交换考生
    function changeStudent(){
        if($(this).hasClass('clicked'))
        {
            $(this).removeClass('clicked');
        }
        else
        {
            $(this).addClass('clicked');
            if($('.clicked').length==2)
            {
                var students    =   new Array;
                $('.clicked').each(function(){
                    //场次ID-批次序号-考场ID-考站ID-学生ID
                    var studentId,batchIndex,screeningId,roomId,stationId;
                    studentId   =   $(this).data('sid');
                    batchIndex  =   $(this).parents('.batch_inner_row').data('batchindex');
                    var roomstation      =   $(this).parents('.roomStatioin').data('roomstation');
                    var RoomStatioinIdInfo    =   roomstation.split('-');
                    roomId      =   RoomStatioinIdInfo[0];
                    stationId   =   RoomStatioinIdInfo[1];
                    screeningId =   $(this).parents('.table').data('screeningid');
                    var studentLocationArray =  new Array;
                    studentLocationArray.push(screeningId);
                    studentLocationArray.push(roomId);
                    studentLocationArray.push(stationId);
                    studentLocationArray.push(batchIndex);
                    studentLocationArray.push(studentId);
                    var studentLocation =   studentLocationArray.join('-');
                    students.push(studentLocation);
                });
                var exam_id=$('[name=exam_id]').val();
                $('.clicked').removeClass('clicked');
                //$.get('/osce/admin/exam/change-student',{'first':students[0],'second':students[1],'exam_id':exam_id},function(data){
                //    var redList =   data.data.redmanList;
                //    if(redList.length>0)
                //    {
                //
                //    }
                //    var obs =   $('.clicked');
                //    var newObs  =   obs.clone().bind('click',changeStudent);
                //    obs.eq(0).after(newObs.eq(1));
                //    obs.eq(1).after(newObs.eq(0));
                //    obs.remove();
                //    $('.stu').removeClass('red');
                //    for (var i in redList)
                //    {
                //
                //
                //        $('.student_'+redList[i]).addClass('red');
                //    }
                //    $('.clicked').removeClass('clicked');
                //});
            }
        }
    }
    //一列表格数据遍历，li
    function makeCols(data){
        var ul  =   $('<ul>');
        var child   =   data.child;
        var titleName   =   data.name.length>16? data.name.substr(0,16)+'…':data.name;
        var title   =   $('<li class="title">').text(titleName);
        title.attr('title',data.name);
        ul.append(title);
        ul.addClass('roomStatioin')
        var perTime=0;
        for(var i in child)
        {

            var itemData    =   child[i];
            var li  =   $('<li>');
            li.addClass("rows"+i);
            li.addClass("batch_inner_row");
            li.attr('data-batchIndex',i);
            if (perTime != 0) {
                var tempPerTime = child[perTime];
                var emptyTime   =   makeEmptyTime(tempPerTime,itemData);
                li.append(emptyTime);
            }
            var item    =   makeItem(itemData);
            ul.append(li);
            li.append(item);
            perTime = i;
        }
        //ul.children().eq(2).addClass('first');
        return ul;
    }

    function makeEmptyTime(tempPerTime,itemData) {
        var tempPerTimeBegin = tempPerTime.start;
        var tempPerTimeEnd = tempPerTime.end;
        var tempItemDataBegin = itemData.start;
        var tempItemDataEnd = itemData.end;

        var height = parseInt((parseInt(tempItemDataBegin) - parseInt(tempPerTimeEnd))/timeHeight);
        return buildEmptyTime(height);
    }

    function buildEmptyTime(height){
        var dt = $('<dt>').css({
            height:height
        }).addClass('emptyTime');
        if(height<=14)
        {
            var span    =   $('<span>').css({
                'fontSize':8
            });
        }
        else
        {
            var span    =   $('<span>');
        }
        span.attr('title','时间闲置');
        dt.append(span);
        return dt;
    }
    //生成一整行数据
    function makeAll(data){
        var ul =    $('<ul class="clearfloat tables">');
        for(var i in data)
        {
            var colData     =   data[i];
            var colul       =   makeCols(colData);
            var li          =   $('<li>');
            colul.attr('data-roomStation',i);
            li.append(colul);
            li.addClass("cols"+i);
            li.addClass("room_inner_col");
            ul.append(li);
        }

        var html = $('<div class="box-table">');
        html.append(ul);
        return html;
    }
    //生成列表
    function maketotal(data){
        for (var i in data){
            var groupData=data[i];
            var dom =   makeAll(groupData);
            dom.attr('data-screeningId',i);
            dom.addClass('screening_'+i);
            addScreeningFirstEmptyTime(dom);
            var sql=$('<div>');
            sql.addClass('screening_box clearfloat');
            sql.append(dom);
            $('.classroom-box').append(sql);
        }
    }

    function addScreeningFirstEmptyTime(dom){
        //screening_
        var screening_id    =   dom.data('screeningid');
        dom.find('.title').each(function(){
            var time    =   $(this).next().data('batchindex');
            time    =   parseInt(time)? parseInt(time):0;
            var eariestTimeData =   eariestTime[screening_id]? eariestTime[screening_id]:0;

            time-=eariestTimeData;
            var emptyDt =   buildEmptyTime(parseInt(time/timeHeight));
            $(this).after(emptyDt);
        });
    }
    //智能排考
    function makePlan(){
        //加载中
        var index = layer.load(0, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.post(pars.makePlanUrl,function(testData){

            if(testData.code!=1)
            {
                layer.msg(testData.message,{skin:'msg-error',icon:1});
                //关闭加载
                layer.close(index);
            }
            else
            {
                $('.classroom-box').html('');
                //$('.time-list>ul').html('');
                maketotal(testData.data);
                //$('#makePlan').one('click',makePlan);
                makeTime();

                //关闭加载
                layer.close(index);
                layer.msg('排考成功！',{skin:'msg-success',icon:1});
            }
        });
    }
    $('#makePlan').click(function(){

        //数据初始化
        timesGroup  =   [];
        eariestTime =   [];
        endtime=[];

        makePlan();
    })


    /**
     * 生成标尺
     * @author mao
     * @version 2.0.1
     * @date    2016-03-22
     * @param   {string}   startTime 开始时间
     * @param   {string}   endTime   结束时间
     * @param   {[number]}   step      [多少px一个标度]
     * @param   {[number]}   pxs       [对应1px的1s]
     * @return  {[string]}             [dom结构]
     */
    function generateAxis(startTime,endTime,step,pxs) {

        if(pxs===undefined)pxs = 20;
        
        //firefox,ie等的兼容性问题
        var startTime = startTime.split('-').join('/'),
            endTime = endTime.split('-').join('/');

        var allSeconds = Date.parse(endTime) - Date.parse(startTime),
            heigth = (allSeconds/1000/pxs), //20s对应1px
            count = Math.ceil(heigth/step),
            flag = 0,
            html = '<div><div class="axis"><dl><dd class="tick-bar"><span>'+formatShowTime(new Date(startTime))+'</span></dd>';
         
        for(var i = 1; i <= count; i++) {
            var time = new Date(i*1000*pxs*step + Date.parse(startTime)),
                className = (flag < 5 ?'tick':'tick-bar');

            html += '<dd class="item"></dd><dd class="'+className+'" title="'+ formatShowTime(time) +'"><span>'+ (flag < 5 ?'' : formatShowTime(time)) +'</span></dd>';
            //间隔标签
            if(flag < 5) {
                flag ++;
            }else{
                flag = 0;
            }
        }

        html += '</dl></div></div>';

        /*//渲染dom
        $('.axis dl').html(html);*/
        //设定间隔的高度
        //$('.axis dl .item').css('height',(step-2) + 'px');
        return html;

    }

    /**
     * 标准化时间转化成所要格式
     * @author mao
     * @version 2.0.1
     * @date    2016-03-15
     * @param   {date}   date 传入时间
     * @return  {date}        所要格式时间
     */
    function formatDateTime(date) {
        var y = date.getFullYear();  
        var m = date.getMonth() + 1;  
        m = m < 10 ? ('0' + m) : m;  
        var d = date.getDate();  
        d = d < 10 ? ('0' + d) : d;  
        var h = date.getHours();  
        var minute = date.getMinutes();

        h = h < 10 ? ('0' + h) : h; 
        minute = minute < 10 ? ('0' + minute) : minute;
        return y + '-' + m + '-' + d+' '+h+':'+minute;  
    };

    /**
     * 标准化显示时间转化成所要格式
     * @author mao
     * @version 2.0.1
     * @date    2016-03-25
     * @param   {date}   date 传入时间
     * @return  {date}        所要格式时间
     */
    function formatShowTime(date) {
        var y = date.getFullYear();  
        var m = date.getMonth() + 1;  
        m = m < 10 ? ('0' + m) : m;  
        var d = date.getDate();  
        d = d < 10 ? ('0' + d) : d;  
        var h = date.getHours();  
        var minute = date.getMinutes();

        h = h < 10 ? ('0' + h) : h; 
        minute = minute < 10 ? ('0' + minute) : minute;
        return m + '-' + d+' '+h+':'+minute;
    }

//生成时间轴
    function makeTime(){


        for(var i in timesGroup ){
            //实例化标尺
            $(".screening_"+i).before(generateAxis(formatDateTime(new Date(eariestTime[i]*1000)),formatDateTime(new Date(endtime[i]*1000)),10));
            //调整每个刻度的高度
            $('.axis dl .item').css('height','8px');
          
        }


        //$(".time-list>ul").css("height",timeHeight+"px");
        //$(".time-list>ul>li:not(.title)").css("height",every+"px");
    }
//数组去重
    function unique (arr){
        var obj = {},newArr = [];
        for(var i = 0;i < arr.length;i++){
            var value = arr[i];
            if(!obj[value]){
                obj[value] = 1;
                newArr.push(value);
            }
        }
        return newArr;
    }

//点击两个表格可进行交换
    function changeTwo(){

        if($(this).hasClass('dd-active'))
        {
            $(this).removeClass('dd-active');
            return ;
        }
        $(this).addClass('dd-active');
        if($(".dd-active").length==2){
            var change1=$($(".dd-active")[0]).html();
            var change2=$($(".dd-active")[1]).html();
            $($(".dd-active")[1]).html(change1);
            $($(".dd-active")[0]).html(change2);
            if($(".error").length==0){
            }else{

            }
            console.log($($(".dd-active")[1]).parent().parent().parent().parent().attr("class"))
            $.ajax({
                url:"",
                type:"get",
                dataType:"json",
                data:{
                    id1:$($(".dd-active")[0]).attr("sid"),
                    id2:$($(".dd-active")[1]).attr("sid"),
                    row1:$($(".dd-active")[0]).parent().parent().attr("class"),
                    row2:$($(".dd-active")[1]).parent().parent().attr("class"),
                    col1:$($(".dd-active")[0]).parent().parent().parent().parent().attr("class"),
                    col2:$($(".dd-active")[1]).parent().parent().parent().parent().attr("class")
                },
                success: function(result) {
                    console.log(result);
                    $("dd").removeClass("dd-active");
                    var status=1//冲突状态
                    if(status==1){
                        $($(".dd-active")[0]).addClass("error");
                        $($(".dd-active")[1]).addClass("error");
                        $(".save").attr("disabled");
                    }else{
                        $($(".dd-active")[0]).removeClass("error");
                        $($(".dd-active")[1]).removeClass("error");
                        $(".save").removeAttr("disabled");
                    }
                }})
        }
    }

}







/*
 * 考生管理
 * @author lizhiyuan
 * @version 2.0
 * @date    2016-01-12
 */
function examinee_manage(){
    //导入考生
    $("#file1").change(function(){
        var id=pars.id;
        var url = pars.excel;
        url += '/'+id;
        //加载中
        var index = layer.load(0, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        $.ajaxFileUpload
        ({
            url:url,
            type:'post',
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',
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
                layer.alert(data.message);
            }
        });

    }) ;
    //删除考生
    $(".delete").click(function(){
        var sid=$(this).attr("sid");
        var examId=$(this).attr("examid");

        $.ajax({
            type:'post',
            async:true,
            url:pars.judgeUrl,
            data:{id:sid,exam_id:examId},
            success:function(res){
                if(res.code!=1){
                    layer.alert(res.message);
                }else{
                    layer.confirm(res.message,{
                        title:'删除',
                        btn: ['确定','取消'] 
                    },function(){
                        $.ajax({
                            type:'post',
                            async:true,
                            url:pars.deleteUrl,
                            data:{id:sid,exam_id:examId},
                            success:function(data){
                                if(data.code ==1){
                                    layer.msg('删除成功！',{'skin':'msg-success','icon':1});
                                    location.reload();
                                }else {
                                    layer.msg(data.message,{'skin':'msg-error','icon':1});
                                }
                            }
                        })
                        //window.location.href=pars.deleteUrl+"?id="+sid+"&exam_id="+examId;
                    });
                }
            }
        });
    })
}

/*
 * 新增考生
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function examinee_manage_add(){

    $(".img_box").delegate(".del_img","click",function(){
        $(this).parent("li").remove();
        $('#images_upload').attr("class","images_upload");
    });
    /*{}{
     * 下面是进行插件初始化
     * 你只需传入相应的键值对
     * */
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
                    }
                }
            },
            code: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '学号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                exam_id: $("#exam_id").val(),
                                code: $('[name="whateverNameAttributeInYourForm"]').val()
                            };
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '学号不能为空'
                    },
                    regexp:{
                        regexp: /^\d+$/,
                        message: '请输入正确的学号'
                    }
                }
            },
            idcard: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        message: '身份证号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                exam_id:$("#exam_id").val(),
                                idcard: $('[name="whateverNameAttributeInYourForm"]').val()
                            };
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
            exam_sequence:{
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.exam_sequence,//验证地址
                        message: '准考证号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                exam_id:$("#exam_id").val(),
                                exam_sequence: $('[name="whateverNameAttributeInYourForm"]').val()
                            };
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '准考证号不能为空'
                    }
                }
            },
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        message: '手机号码已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                exam_id:$("#exam_id").val(),
                                mobile: $('[name="whateverNameAttributeInYourForm"]').val()
                            };
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
                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            email:{
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^[a-z\d]+(\.[a-z\d]+)*@([\da-z](-[\da-z])?)+(\.{1,2}[a-z]+)+$/,
                        message: '请输入正确的邮箱'
                    }
                }
            }
        }
    });

    $("#images_upload").change(function(){
        $.ajaxFileUpload
        ({

            url:pars.header,
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
                layer.msg("通讯失败");
            }
        });
    }) ;

    //图片检测
    $('#save').click(function(){
        if($('.img_box').find('img').attr('src')==undefined){
            layer.msg('请上传图片！',{skin:'msg-error',icon:1});
            return false;
        }
    });

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


    $(".return-pre").click(function() {
        location.href=pars.preUrl;
    })
}

/**
 * 考生编辑
 * @author mao
 * @version 2.0.1
 * @date    2016-03-18
 */
function examinee_manage_edit() {
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
                    }
                }
            },
            code: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.code,//验证地址
                        message: '学号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: pars.id,
                                exam_id: pars.exam_id,
                                code:  $('[name="whateverNameAttributeInYourForm"]').val()
                            };
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '学号不能为空'
                    },
                    regexp:{
                        regexp: /^\d+$/,
                        message: '请输入正确的学号'
                    }
                }
            },
            idcard: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.idcard,//验证地址
                        message: '身份证号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: pars.id,
                                exam_id: pars.exam_id,
                                idcard:  $('[name="whateverNameAttributeInYourForm"]').val()
                            };
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
            mobile: {
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.mobile,//验证地址
                        message: '手机号码已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: pars.id,
                                exam_id: pars.exam_id,
                                mobile:  $('[name="whateverNameAttributeInYourForm"]').val()
                            };
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
                        regexp: /^1[3|5|7|8]{1}[0-9]{9}$/,
                        message: '请输入正确的手机号码'
                    }
                }
            },
            exam_sequence:{
                validators: {
                    threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                        url: pars.exam_sequence,//验证地址
                        message: '准考证号已经存在',//提示消息
                        delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                        /*自定义提交数据，默认值提交当前input value*/
                        data: function(validator) {
                            return {
                                id: pars.id,
                                exam_id: pars.exam_id,
                                exam_sequence: $('[name="whateverNameAttributeInYourForm"]').val()
                            };
                        }
                    },
                    notEmpty: {/*非空提示*/
                        message: '准考证号不能为空'
                    }
                }
            },
            email:{
                validators: {
                    notEmpty: {/*非空提示*/
                        message: '邮箱不能为空'
                    },
                    regexp: {
                        regexp: /^[a-z\d]+(\.[a-z\d]+)*@([\da-z](-[\da-z])?)+(\.{1,2}[a-z]+)+$/,
                        message: '请输入正确的邮箱'
                    }
                }
            }
        }
    });

    $("#images_upload").change(function () {
        $.ajaxFileUpload
        ({
            url: pars.header,
            secureuri: false,//
            fileElementId: 'file0',//必须要是 input file标签 ID
            dataType: 'json',//
            success: function (data, status) {
                if (data.code) {
                    var href = data.data.path;
                    $('.img_box').find('li').remove();
                    $('#images_upload').before('<li><img src="' + href + '"/><input type="hidden" name="images_path[]" value="' + href + '"/><i class="fa fa-remove font16 del_img"></i></li>');
                }
            },
            error: function (data, status, e) {
                $.alert({
                    title: '提示：',
                    content: '通讯失败!',
                    confirmButton: '确定',
                    confirm: function () {
                    }
                });
            }
        });
    });

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

    //建立一個可存取到該file的url
    var url = '';
    function getObjectURL(file) {
        if (window.createObjectURL != undefined) { // basic
            url = window.createObjectURL(file);
        } else if (window.URL != undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(file);
        } else if (window.webkitURL != undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(file);
        }
        return url;
    }
}

function station_assignment(){

    //考站名称
    var stationName = ['一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'],
        examId = (location.href).split('=')[1],
        typeToName = ['','技能考站','sp考站','理论考站'],
        chioceToName = ['','必考','选考'];

    /**
     * 初始化
     * @author mao
     * @version 3.4
     * @date    2016-04-07
     */
    $.ajax({
        type:'get',
        url: pars.data_list,
        async: false,
        data: {exam_id: examId},
        success: function(res) {
            if(res.code != 1) {
                layer.msg('数据加载失败！',{skin:'msg-error',icon:1});
            } else {
                InitData(res.data);
            }
        }
    });


    /**
     * 数据渲染
     * @author mao
     * @version 3.4
     * @date    2016-04-07
     */
    function InitData(data) {

        var html = '';
        var arr = [];

        for(var i in data) {
            //记录dom结构
            var str = '';
            for(var j in data[i].item) {
                str += '<tr class="item-id-'+j+'" item-id="'+data[i].item[j].id+'">'+
                                '<td type="2"><select class="form-control exam-item"><option selected="selected" value="'+data[i].item[j].subject_id+'">'+data[i].item[j].subject_name+'</option></select></td>'+
                                '<td type="2"><select class="form-control exam-station"><option selected="selected" value="'+data[i].item[j].station_id+'">'+data[i].item[j].station_name+'</option></select></td>'+
                                '<td type="2">'+typeToName[data[i].item[j].station_type]+'</td>'+
                                '<td type="2"><select class="form-control station-belong"><option selected="selected" value="'+data[i].item[j].room_id+'">'+data[i].item[j].room_name+'</option></select></td>'+
                                '<td type="2"><select class="form-control station-chioce"><option selected="selected" value="'+data[i].item[j].effected+'">'+chioceToName[data[i].item[j].effected]+'</option></select></td>'+
                                '<td>'+
                                    '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                                    '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                '</td>'+
                            '</tr>';
                
                //存储select2 class
                arr.push({table:'.table-id-'+i, tr:'.item-id-'+j});
            }


            //一个站的dom结构
            html += '<div class="form-group">'+
                        '<label class="col-sm-2 control-label">&nbsp;</label>'+
                        '<div class="col-sm-10">'+
                            '<div class="row">'+
                                '<div class="col-sm-4"><label class="control-label" order="'+data[i].order+'">'+data[i].name+'</label></div>'+
                                '<div class="col-sm-6">'+
                                        '<label class="control-label col-sm-2">阶段：</label>'+
                                        '<select class="form-control col-sm-10 select-stage" style="width: 381px;" type="2">'+stageRender(data[i].exam_gradation_id)+'</select>'+
                                '</div>'+
                                '<div class="col-sm-2">'+
                                    '<a class="btn btn-primary" href="javascript:void(0)">必考</a>'+
                                    '<a  href="javascript:void(0)" class="btn btn-primary del-station" style="float: right;">删除</a>'+
                                '</div>'+
                            '</div>'+
                            '<table class="table table-bordered table-id-'+i+'" table-order="'+i+'" station-id="'+data[i].id+'">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<td>考试项目</td>'+
                                        '<td>考站</td>'+
                                        '<td>考站类型</td>'+
                                        '<td>所属考场</td>'+
                                        '<td>必考&选考</td>'+
                                        '<td>操作</td>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody index="'+j+'">'+str+'</tbody>'+
                            '</table>'+
                        '</div>'+
                    '</div>';
        }
        $('.station-container').html(html);
        $('.station-container').attr('index',data.length);

        //触发select2
        for(var i in arr) {
            select2Init($(arr[i].table).find(arr[i].tr));
        }
    }
    //模拟数据触发
    //InitData(data);

    
    /**
     * 阶段列表
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     * @param   {[number]}   count [第几个阶段]
     * @return  {[string]}         [option dom]
     */
    function stageRender(count) {
       var exam_stage_str = '';
        $.ajax({
            type: 'get',
            async: false,
            url: pars.exam_stage,
            success: function(res) {
                var data = res.data;

                for(var i in data) {
                    var str = data[i].order - 1;
                    if(data[i].order == count) {
                        exam_stage_str += '<option value="'+data[i].order+'" selected="selected">阶段'+stationName[str]+'</option>';
                    } else {
                        exam_stage_str += '<option value="'+data[i].order+'">阶段'+stationName[str]+'</option>';
                    }
                }
            }
        });
        
        return exam_stage_str; 
    }

    //阶段选择
    $('.station-container').on('change', '.select-stage', function() {
        //考站dom
        var $that = $(this).parent().parent().parent().parent(),
            req = {};

        req['exam_id'] = examId;
        req['name'] = $(this).parent().siblings('.col-sm-4').find('label').text();
        req['order'] = $(this).parent().siblings('.col-sm-4').find('label').attr('order')
        req['exam_gradation_id'] = $(this).val();
        req['type'] = $(this).attr('type');
        req['flow_id'] = $that.find('table').attr('station-id');

        $.ajax({
            type:'post',
            url: pars.stationAdd,
            data: req,
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('数据更新失败！',{skin:'msg-error',icon:1});
                } else {
                    return true;
                }
            }
        });
    });


    /**
     * 新建一个考站
     * @author mao
     * @version 3.3
     * @date    2016-04-05
     */
    $('#station-add').click(function() {

        var req = {},
            html = '',
            index = parseInt($('.station-container').attr('index'));

        //请求数据
        req['exam_id'] = examId;
        req['name'] = '第'+stationName[index]+'站';
        req['order'] = index;
        req['exam_gradation_id'] = 1;

        //阶段列表
        var exam_stage_str = '';
        $.ajax({
            type: 'get',
            async: false,
            data: req,
            url: pars.exam_stage,
            success: function(res) {
                var data = res.data;

                for(var i in data) {
                    var str = data[i].order - 1;
                    exam_stage_str += '<option value="'+data[i].order+'">阶段'+stationName[str]+'</option>';
                }
            }
        });

        /**
         * 新增考站 获取考站id
         * @author mao
         * @version 3.4
         * @date    2016-04-06
         */
        $.ajax({
            type: 'post',
            async: true,
            data: req,
            url: pars.stationAdd,
            success: function(res) {
                if(res.code !=1) {
                    layer.msg('新增考站失败！',{skin:'msg-error',icon:1});
                } else {
                    html += '<div class="form-group">'+
                                '<label class="col-sm-2 control-label">&nbsp;</label>'+
                                '<div class="col-sm-10">'+
                                    '<div class="row">'+
                                        '<div class="col-sm-4"><label class="control-label" order="'+index+'">第'+stationName[index]+'站</label></div>'+
                                        '<div class="col-sm-6">'+
                                                '<label class="control-label col-sm-2">阶段：</label>'+
                                                '<select class="form-control col-sm-10 select-stage" style="width: 381px;" type="3">'+stageRender(1)+'</select>'+
                                        '</div>'+
                                        '<div class="col-sm-2">'+
                                            '<a class="btn btn-primary" href="javascript:void(0)">必考</a>'+
                                            '<a  href="javascript:void(0)" class="btn btn-primary del-station" style="float: right;">删除</a>'+
                                        '</div>'+
                                    '</div>'+
                                    '<table class="table table-bordered table-id-'+index+'" table-order="'+index+'" station-id="'+res.data.id+'">'+
                                        '<thead>'+
                                            '<tr>'+
                                                '<td>考试项目</td>'+
                                                '<td>考站</td>'+
                                                '<td>考站类型</td>'+
                                                '<td>所属考场</td>'+
                                                '<td>必考&选考</td>'+
                                                '<td>操作</td>'+
                                            '</tr>'+
                                        '</thead>'+
                                        '<tbody index="0">'+
                                            '<tr class="item-id-'+index+'" item-id="'+res.data.draft_id+'">'+
                                                '<td type="3"><select class="form-control exam-item"><option value="请选择">请选择</option></select></td>'+
                                                '<td type="3"><select class="form-control exam-station"><option value="请选择">请选择</option></select></td>'+
                                                '<td type="3"></td>'+
                                                '<td type="3"><select class="form-control station-belong"><option value="请选择">请选择</option></select></td>'+
                                                '<td type="3"><select class="form-control station-chioce"><option value="1">必考</option><option value="2">选考</option></select></td>'+
                                                '<td>'+
                                                    '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                                                    '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                                '</td>'+
                                            '</tr>'+
                                        '</tbody>'+
                                    '</table>'+
                                '</div>'+
                            '</div>';

                    //插入dom
                    $('.station-container').append(html);
                    $('.station-container').attr('index',index + 1);
                    $('.station-container').find('.item-id-'+index).attr('index',index + 1);
                    //初始化select2
                    select2Init($('.station-container').find('.table-id-'+index).find('.item-id-'+index));
                }
            }
        });

        

    });



    /**
     * 删除考站
     * @author mao
     * @version 3.3
     * @date    2016-04-05
     */
    $('.station-container').on('click', '.del-station', function() {
        //删除dom
        var $that = $(this).parent().parent().parent().parent(),
            judgeType = $that.find('.select-stage').attr('type') == 2 ? 2 :5;

        $.ajax({
            type:'get',
            url: pars.del_flow,
            data: {exam_id:examId,flow_id:$that.find('table').attr('station-id'),type:judgeType},
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('删除失败！',{skin:'msg-error',icon:1});
                } else {
                    $that.remove();
                    $('.station-container').attr('index',parseInt($('.station-container').attr('index'))-1);
                }
            }
        });
    });

    /**
     * 新增一条记录
     * @author mao
     * @version 3.3
     * @date    2016-04-05
     */
    $('.station-container').on('click', '.fa-plus', function() {
        var html = '',
            $that = $(this).parent().parent().parent().parent().parent(),
            judgeType = $that.parent().parent().find('.select-stage').attr('type') == 2 ? 1 :4,
            index = parseInt($that.attr('index')) + 1;

        $.ajax({
            type:'post',
            url: pars.update_data,
            data: {exam_id:examId,flow_id:$that.parent().attr('station-id'),type:judgeType},
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('新增失败！',{skin:'msg-error',icon:1});
                } else {
                    html += '<tr class="item-id-'+index+'" item-id="'+res.data.id+'">'+
                                '<td type="3"><select class="form-control exam-item"><option value="请选择">请选择</option></select></td>'+
                                '<td type="3"><select class="form-control exam-station"><option value="请选择">请选择</option></select></td>'+
                                '<td type="3"></td>'+
                                '<td type="3"><select class="form-control station-belong"><option value="请选择">请选择</option></select></td>'+
                                '<td type="3"><select class="form-control station-chioce"><option value="1">必考</option><option value="2">选考</option></select></td>'+
                                '<td>'+
                                    '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                                    '<a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                '</td>'+
                            '</tr>';

                    //插入dom
                    $that.append(html);
                    $that.attr('index',index);
                    //select2初始化
                    select2Init($that.find('.item-id-'+index));
                }
            }
        });

        
    });

    /**
     * 删除一条记录
     * @author mao
     * @version 3.3
     * @date    2016-04-05
     */
    $('.station-container').on('click', '.fa-trash-o', function() {
        //删除dom
        var $that = $(this).parent().parent().parent().parent(),
            judgeType = $(this).parent().parent().parent().prev().attr('type') == 2 ? 2 :5;

        //删除整个站 在记录只有一条的情况下
        if($that.parent().attr('index') == 0){
            layer.confirm('确认要删除整个站吗？',{
                title:'删除',
                btn: ['确定','取消'] 
            }, function(index){

                //删除大站，在一条的情况
                var judgeType = $that.parent().parent().parent().parent().find('.select-stage').attr('type') == 2 ? 2 :5;

                $.ajax({
                    type:'get',
                    url: pars.del_flow,
                    data: {exam_id:examId,flow_id:$that.parent().parent().parent().parent().find('table').attr('station-id'),type:judgeType},
                    success: function(res) {
                        if(res.code != 1) {
                            layer.msg('删除失败！',{skin:'msg-error',icon:1});
                        } else {
                            $that.parent().parent().parent().parent().remove();
                            $('.station-container').attr('index',parseInt($('.station-container').attr('index'))-1);
                        }
                    }
                });

                //$that.parent().parent().parent().parent().remove();
                layer.close(index);

            });

            return;
        }


        $.ajax({
            type:'get',
            url: pars.del_draft,
            data: {exam_id:examId,flow_id:$that.parent().parent().attr('station-id'),draft_id: $that.attr('item-id'),type:judgeType},
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('删除失败！',{skin:'msg-error',icon:1});
                } else {
                    //序数更新
                    $that.parent().attr('index', parseInt($that.parent().attr('index'))-1);
                    $that.remove();
                }
            }
        });

    });

    /**
     * 更新数据
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     * @param   {object}   req 请求数据
     * @param   {string}   url 请求地址
     */
    function ajaxUpdate(req,url) {
        $.ajax({
            type:'post',
            url: url,
            data:req,
            success: function(res) {
                return true;
            }
        })
    }

    /**
     * 初始化select2
     * @author mao
     * @version 3.3
     * @date    2016-04-05
     */
    function select2Init($elem) {

        $elem.find('.exam-item').select2({
            placeholder:'请选择',
            tags: true,
            ajax: {
                type:'get',
                url: pars.exam_item,
                delay: 250,
                processResults: function (res) {

                    //数据格式化
                    var str = [];
                    var data = res.data;
                    for(var i in data){
                        str.push({id:data[i].id,text:data[i].title});
                    }
                    str.push({id:-999,text:'新增考试项目'});

                    //加载入数据
                    return {
                        results: str
                    };
                }
            }

        //数据更新，交互数据
        }).on('select2:select', function(e) {

            //请求数据
            var req = {
                exam_id:examId,
                type:$elem.find('.exam-item').parent().attr('type'),
                draft_id:$elem.attr('item-id'),
                flow_id:$elem.parent().parent().attr('station-id'),
                subject:e.params.data.id
            };

            //新增页面
            if(e.params.data.id == -999) {

                layer.open({
                  type: 2,
                  title: '新增考试项目',
                  shadeClose: true,
                  shade: 0.8,
                  area: ['90%', '90%'],
                  end: function() {
                    //更改请求数据
                    req.subject = $elem.find('.exam-item').val();

                    ajaxUpdate(req, pars.update_data);
                  },
                  content: pars.add_subject+'?status=1&table='+$elem.find('.exam-item').parent().parent().parent().parent().attr('table-order')+'&tr='+$elem.find('.exam-item').parent().parent().attr('class')
                });
            } else {
                ajaxUpdate(req, pars.update_data);
            }
        });

        //考站列表获取
        $elem.find('.exam-station').select2({
            placeholder:'请选择',
            tags: true,
            ajax: {
                type:'get',
                url: pars.station_list,
                delay: 250,
                processResults: function (res) {

                    //数据格式化
                    var str = [];
                    var data = res.data;
                    for(var i in data){
                        str.push({id:data[i].id,text:data[i].name,type:data[i].type});
                    }
                    str.push({id:-999,text:'新增考站'});

                    //加载入数据
                    return {
                        results: str
                    };
                }
            }

        //数据更新，交互数据
        }).on('select2:select', function(e) {console.log(e.params.data)
            //请求数据
            var req = {
                exam_id:examId,
                type:$elem.find('.exam-station').parent().attr('type'),
                draft_id:$elem.attr('item-id'),
                flow_id:$elem.parent().parent().attr('station-id'),
                station:e.params.data.id
            };

            //新增页面
            if(e.params.data.id == -999) {
                layer.open({
                  type: 2,
                  title: '新增考站',
                  shadeClose: true,
                  shade: 0.8,
                  area: ['90%', '90%'],
                  content: pars.add_station+'?status=1&table='+$elem.find('.exam-station').parent().parent().parent().parent().attr('table-order')+'&tr='+$elem.find('.exam-station').parent().parent().attr('class')
                });
            } else {
               $.ajax({
                    type:'post',
                    url: pars.update_data,
                    data:req,
                    success: function(res) {
                        //更新考站类型
                        $elem.find('.exam-station').parent().next().text(typeToName[e.params.data.type])
                    }
                }) 
            }

            
        });

        //$elem.find('.station-type').select2();

        //所属考场
        $elem.find('.station-belong').select2({
            placeholder:'请选择',
            tags: true,
            ajax: {
                type:'get',
                url: pars.room_list,
                delay: 250,
                processResults: function (res) {

                    //数据格式化
                    var str = [];
                    var data = res.data;
                    for(var i in data){
                        str.push({id:data[i].id,text:data[i].name});
                    }
                    str.push({id:-999,text:'新增考场'});

                    //加载入数据
                    return {
                        results: str
                    };
                }
            }

        //数据更新，交互数据
        }).on('select2:select', function(e) {
            //请求数据
            var req = {
                exam_id:examId,
                type:$elem.find('.station-belong').parent().attr('type'),
                draft_id:$elem.attr('item-id'),
                station_id:$elem.parent().parent().attr('station-id'),
                room:e.params.data.id
            };

            //新增页面
            if(e.params.data.id == -999) {
                layer.open({
                  type: 2,
                  title: '新增考场',
                  shadeClose: true,
                  shade: 0.8,
                  area: ['90%', '90%'],
                  end: function(){
                    //更改请求数据
                    req.room = $elem.find('.station-belong').val();

                    ajaxUpdate(req, pars.update_data);
                  },
                  content: pars.add_room+'?status=1&table='+$elem.find('.station-belong').parent().parent().parent().parent().attr('table-order')+'&tr='+$elem.find('.station-belong').parent().parent().attr('class')
                });
            } else {
                ajaxUpdate(req, pars.update_data);
            }

        });

        //选考必考
        $elem.find('.station-chioce').select2({data:[{id:1,text:'必考'},{id:2,text:'选考'}]}).on("change", function (e) {
            //请求数据
            var req = {
                exam_id:examId,
                type:$elem.find('.station-chioce').parent().attr('type'),
                draft_id:$elem.attr('item-id'),
                station_id:$elem.parent().parent().attr('station-id'),
                chioce: $elem.find('.station-chioce').val()
            };

            $.ajax({
                type:'post',
                url: pars.update_data,
                data:req,
                success: function(res) {
                    return true;
                }
            })
         });

    }

    /**
     * 保存数据
     * @author mao
     * @version 3.4
     * @date    2016-04-07
     */
    $('#save').click(function() {
        $.ajax({
            type:'post',
            url: pars.save,
            data:{exam_id:examId},
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('保存数据失败！',{skin:'msg-error',icon:1});
                } else {
                    layer.msg('数据保存成功！',{skin:'msg-success',icon:1});
                }
            }
        })
    });

}


/**
 * 考官安排
 * @author chenxia
 * @version 3.3
 * @date    2016-04-05
 * @param   {object} 传入数据
 */
function examiner_manage() {


    $("#add-basic .custom-teacher").select2({
        placeholder:'请选择',
        ajax: {
            type:'get',
            url: pars.teacher_list,
            dataType: 'json',
            data:function() {
                var ids = [];
                $('#add-basic tbody').find('tr').each(function() {
                    var temp = $(this).find('td').eq(3).find('select').val();
                    for(var i in temp) {
                        ids.push(temp[i]);
                    }
                })

                return {
                    status:1,
                    teahcer_id:ids
                };
            },
            delay: 250,
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
            }
        }
    });
    $("#add-basic .custom-sp").select2({
        placeholder:'请选择',
        ajax: {
            type:'get',
            url: pars.teacher_list,
            dataType: 'json',
            data:function() {
                var ids = [];
                $('#add-basic tbody').find('tr').each(function() {
                    var temp = $(this).find('td').eq(4).find('select').val();
                    for(var i in temp) {
                        ids.push(temp[i]);
                    }
                })

                return {
                    status:1,
                    teahcer_id:ids
                };
            },
            delay: 250,
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
            }
        }
    });

    var data = [
        /*{
    var data = [
        {   subject_id:1,
            exam_item:{id:12,name:'胸腔1'},
            station:{id:212,name:'考站1'},
            station_type:{id:323,name:'技能站1'},
            teacher:[{id:5,name:'zhang1',status:1},{id:34,name:'张老师1',status:1}],
            sp_teacher:[{id:45,name:'成张老师1',status:1},{id:344,name:'杨老师1',status:2}]
        },
        {   
            subject_id:12,
            exam_item:{id:12,name:'胸腔2'},
            station:{id:212,name:'考站2'},
            station_type:{id:323,name:'技能站2'},
            teacher:[{id:5,name:'zhang2',status:1},{id:34,name:'张老师2',status:1}],
            sp_teacher:[{id:45,name:'成张老师2',status:1},{id:344,name:'杨老师2',status:2}]
        },
        {
            subject_id:11,
            exam_item:{id:12,name:'胸腔3'},
            station:{id:212,name:'考站3'},
            station_type:{id:323,name:'技能站3'},
            teacher:[{id:5,name:'zhang3',status:1},{id:34,name:'张老师3',status:1}],
            sp_teacher:[{id:45,name:'成张老师3',status:1},{id:344,name:'杨老师3',status:2}]
        }*/
    ];

    var teacherArr = [];

    /**
     * 初始化数据
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     * @param   {object}   data 传入数据
     */

    function initTable(data) {
        var html="";
        for(var i in data){
            var str_teacher = '',
                str_sp = '';
            /**
             * 这里是生成考官
             */
            for(var j in data[i].teacher) {
                str_teacher += '<option value="'+data[i].teacher[j].id+'" selected="selected">'+data[i].teacher[j].name+'</option>';
            }
            /**
             * 这里是生成ＳＰ
             */
            for(var j in data[i].sp_teacher) {
                str_sp += '<option value="'+data[i].sp_teacher[j].id+'" selected="selected">'+data[i].sp_teacher[j].name+'</option>';
            }

            //dom准备
            html += '<tr value="'+data[i].subject_id+'">'+
                        '<td>'+data[i].exam_item.name+'</td>'+
                        '<td>'+data[i].station.name+'</td>'+
                        '<td>'+data[i].station_type.name+'</td>'+
                        '<td style="width:481px;">'+
                            '<div class="col-sm-10">'+
                            '<select class="form-control custom-teacher"  name=""  multiple="multiple">'+str_teacher+
                            '</select>'+
                            '</div>'+
                        '</td>'+
                        '<td style="width:481px;">'+
                            '<div class="col-sm-10">'+
                            '<select class="form-control custom-sp"  name=""  multiple="multiple">'+str_sp+
                            '</select>'+
                            '</div>'+
                        '</td>'+
                        '<td>'+
                            '<a href="javascript:void(0)" class="invitaion-teacher">发送邀请</a>'+
                        '</td>'+
                    '</tr>';
        }

        //插入dom
        $('#add-basic tbody').html(html);

        //初始化select2 老师邀请
        $('#add-basic tbody').find('tr').each(function(key) {
            var $that = $(this);

            teacherInit($that);
            teacher_spInit($that);
            //老师邀请
            sp_invation($that);

            //老师列表数组
            teacherArr.push({subject_id:$that.attr('value'), teacher:$that.find('.custom-teacher').val(), sp_teacher: $that.find('.custom-sp').val()});

        });
    }

    /**
     * 保存成功
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     */
    $('#save').click(function() {
        $.ajax({
            type:'get',
            url: '',
            data:{data:teacherArr},
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('保存成功失败！',{skin:'msg-error',icon:1});
                } else {
                    layer.msg('保存成功成功！',{skin:'msg-success',icon:1});
                }
            }
        })
    });

    /**
     * 全部发送邀请
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     */
    $('#invation-all').click(function() {
        $.ajax({
            type:'get',
            url: '',
            data:{data:teacherArr},
            success: function(res) {
                if(res.code != 1) {
                    layer.msg('发送邀请失败！',{skin:'msg-error',icon:1});
                } else {
                    layer.msg('发送邀请成功！',{skin:'msg-success',icon:1});
                }
            }
        })
    });



    //模拟数据
    initTable(data);
    /**
     * sp_invation 邀请
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     * @param   {[object]}   $elem [选择器]
     */
    function sp_invation($elem) {
        var req = {
            teacher:$elem.find('.custom-teacher').val(),
            sp_teacher:$elem.find('.custom-sp').val(),
            subject_id:$elem.attr('value')
        };

        $elem.find('.invitaion-teacher').click(function() {
            $.ajax({
                type:'get',
                url: '',
                data:req,
                success: function(res) {
                    if(res.code != 1) {
                        layer.msg('发送邀请失败！',{skin:'msg-error',icon:1});
                    } else {
                        layer.msg('发送邀请成功！',{skin:'msg-success',icon:1});
                    }
                }
            })
        });
    }

    /**
     * 老师选择
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     * @param   {object} dom选择器
     */
    function teacherInit($elem) {
        $elem.find('.custom-teacher').select2({
            placeholder:'请选择',
            ajax: {
                type:'get',
                dataType: 'json',
                url: pars.teacher_list,
                data:function() {

                    return {
                        subject_id:$elem.attr('value'),
                        type:1
                    };
                },
                delay: 250,
                processResults: function (res) {

                    //数据格式化
                    var str = [];
                    var data = res.data;
                    for(var i in data){
                        str.push({id:data[i].teacher_id,text:data[i].name});
                    }

                    //加载入数据
                    return {
                        results: str
                    };
                }
            }
        });
    }
    

    /**
     * sp老师选择
     * @author mao
     * @version 3.4
     * @date    2016-04-08
     * @param   {object} dom选择器
     */
    function teacher_spInit($elem) {
        $elem.find('.custom-sp').select2({
            placeholder:'请选择',
            ajax: {
                type:'get',
                dataType: 'json',
                url: pars.teacher_list,
                data:function() {

                    return {
                        subject_id:$elem.attr('value'),
                        type:2
                    };
                },
                delay: 250,
                processResults: function (res) {

                    //数据格式化
                    var str = [];
                    var data = res.data;
                    for(var i in data){
                        str.push({id:data[i].teacher_id,text:data[i].name});
                    }

                    //加载入数据
                    return {
                        results: str
                    };
                }
            }
        });
    }
    

    //$("#add-basic .custom-teacher").select2();
    //$("#add-basic .custom-sp").select2();
}




