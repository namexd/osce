/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "exam_assignment":exam_assignment();break;
        case "exam_add":exam_add();break;
        case "add_basic":add_basic();break;
        case "sp_invitation":sp_invitation();break;
        case "examroom_assignment":examroom_assignment();break;
        case "exam_notice_add":exam_notice_add();break;
        case "exam_notice_edit":exam_notice_edit();break;
        case "smart_assignment":smart_assignment();break;
        case "examinee_manage":examinee_manage();break;
        case "examinee_add":examinee_add();break;
        case "station_assignment":station_assignment();break;
    }
});

/**
 * 考试安排
 * @author mao
 * @version 1.0
 * @date    2016-01-06
 */
function exam_assignment(){

    $('table').on('click','.fa-trash-o',function(){

        var thisElement = $(this);
        layer.alert('确认删除？',function(){
            $.ajax({
                type:'post',
                async:true,
                url:pars.deletes,
                data:{id:thisElement.parent().parent().parent().attr('value')},
                success:function(res){
                    location.reload();
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
function exam_add(){
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
            }
        }
    });
    /**
     * 新增一条
     * @author  mao
     * @version  1.0
     * @date        2016-01-05
     */
    $('#add-new').click(function(){
        //计数器标志
        var index = $('#exam_add').find('tbody').attr('index');
        index = parseInt(index) + 1;

        //时长默认值
        var timeLength = (Time.getTime('YYYY-MM-DD hh:mm')).split(' ')[1];
        var hours = timeLength.split(':')[0];
        var minutes = timeLength.split(':')[1];

        var html = '<tr>'+
            '<td>'+parseInt(index)+'</td>'+
            '<td class="laydate">'+
            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][begin_dt]" value="'+Time.getTime('YYYY-MM-DD')+' 00:00"/>'+
            '</td>'+
            '<td class="laydate">'+
            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][end_dt]" value="'+Time.getTime('YYYY-MM-DD hh:mm')+'"/>'+
            '</td>'+
            '<td>0天'+hours+'小时'+minutes+'分</td>'+
            '<td>'+
            '<a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
            '</td>'+
            '</tr>';
        //记录计数
        $('#exam_add').find('tbody').attr('index',index);
        $('#exam_add').find('tbody').append(html);
    });


    /**
     * 删除一条记录
     * @author mao
     * @version 1.0
     * @date    2016-01-05
     */
    $('#exam_add').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        layer.alert('确认为删除？',function(thisID){
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
}

/**
 * 新增考试 基础信息
 * @author mao
 * @version 1.0
 * @date    2016-01-04
 */
function add_basic(){
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
            },
            sequence_mode: {
                validators: {
                    notEmpty: {
                        message: '排序方式不能为空'
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
     * 新增一条
     * @author  mao
     * @version  1.0
     * @date        2016-01-05
     */
    $('#add-new').click(function(){
        //计数器标志
        var index = $('#add-basic').find('tbody').attr('index');
        index = parseInt(index) + 1;

        //时长默认值
        var timeLength = (Time.getTime('YYYY-MM-DD hh:mm')).split(' ')[1];
        var hours = timeLength.split(':')[0];
        var minutes = timeLength.split(':')[1];

        var html = '<tr>'+
            '<td>'+parseInt(index)+'</td>'+
            '<td class="laydate">'+
            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][begin_dt]" value="'+Time.getTime('YYYY-MM-DD')+' 00:00"/>'+
            '</td>'+
            '<td class="laydate">'+
            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][end_dt]" value="'+Time.getTime('YYYY-MM-DD hh:mm')+'"/>'+
            '</td>'+
            '<td>0天'+hours+'小时'+minutes+'分</td>'+
            '<td>'+
            '<a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
            '</td>'+
            '</tr>';
        //记录计数
        $('#add-basic').find('tbody').attr('index',index);
        $('#add-basic').find('tbody').append(html);
    });


    /**
     * 删除一条记录
     * @author mao
     * @version 1.0
     * @date    2016-01-05
     */
    $('#add-basic').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();

        layer.alert('确认为删除？',function(thisID){
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
        isclear: true, //是否显示清空
        istoday: true, //是否显示今天
        issure: true, //是否显示确认
        festival: true, //是否显示节日
        min: '1900-01-01 00:00:00', //最小日期
        max: '2099-12-31 23:59:59', //最大日期
        start: '2014-6-15 23:00:00',    //开始日期
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
                var current = Date.parse((thisElement.next().find('input[type=text]').val()).split('-').join('/')) - Date.parse(date.split('-').join('/'));
                var days = Math.floor(current/(1000*60*60*24)),
                    hours = Math.floor((current/(1000*60*60*24)-days)*24),
                    minutes = Math.round((((current/(1000*60*60*24)-days)*24)-hours)*60);
                thisElement.next().next().text(days+'天'+hours+'小时'+minutes+'分');
            }
        }
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
            option.min = '1900-01-01 00:00:00';
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


/*
 * 邀约sp老师
 * @author lizhiyuan
 * @version 2.0
 * @date    2016-01-05
 */
function sp_invitation(){

    //select2初始化
    $(".js-example-basic-single").select2();

    /**
     * 获取数据
     * @author mao
     * @version 1.0
     * @date    2016-01-06
     */
    $('.teacher-list').click(function(){
        var thisElement = $(this);

        var id = $(this).parent().parent().parent().attr('value');
        var selected = [];

        //选择的数据
        thisElement.parent().siblings('.teacher-box').find('.teacher').each(function(key,elem){
            selected.push($(elem).attr('value'));
        });
        $.ajax({
            type:'get',
            url: pars.teacher_list,   //修改请求地址
            data:{id:id,selected:selected},
            success:function(res){

                var source = [];

                if(res.code!=1){
                    layer.alert('res.message');
                }else{
                    var data = res.data.rows;
                    var html = '<option>选择</option>';
                    for(var i in data){

                        html += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                    thisElement.html(html);
                }
            }

        });
    });

    $(".teacher-list").change(function(){

        var $teacher=$(".teacher-list option:selected").text();
        var id = $(".teacher-list option:selected").val();
        var thisElement = $(this);

        var sql='<div class="input-group teacher pull-left" value="'+id+'">'+
            '<div class="pull-left">'+$teacher+'</div>'+
            '<div class="pull-left"><i class="fa fa-times"></i></div></div>';
        $(this).parents(".pull-right").prev().append(sql);
    })

    //删除
    $(".teacher-box").delegate("i","click",function(){
        $(this).parents(".teacher").remove();
    })
}


function examroom_assignment(){


    //select2初始化
    //$(".js-example-basic-multiple").select2();

    /**
     * 将保存的数据保存
     */
    var arrStore = [];
    $('#examroom').find('tbody').find('tr').each(function(key,elem){


        var selected = $(elem).find('td').eq(1).find('select').val();
        for(var i in selected){
            if(arrStore.length==0){
                arrStore.push({id:selected[i],count:1});
            }else{
                for(var j in arrStore){
                    if(arrStore[j].id==selected[i]){
                        arrStore[j].count += 1;
                    }else{
                        arrStore.push({id:selected[i],count:1});
                    }
                }
            }
        }
    });
    $('#examroom').find('tbody').attr('data',JSON.stringify(arrStore));

    /**
     * select2初始化
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
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
                $(".room-station").select2({data:str});
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

                        var teacher = '<option>==请选择==</option>';
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

                                var data    =   new Array;
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
                                });
                                return {
                                    teacher:data
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
                    //重置序号
                    var station_count = 1;
                    $('#exam-place').find('tbody').find('tr').each(function(key,elem){
                        station_count = key + 1;
                        $(elem).find('td').eq(0).text(station_count);
                    });
                    $('#exam-place').find('tbody').attr('index',station_count);
                    continue;
                }
            }else{
                current.push({id:rooms[i].id,count:rooms[i].count});
            }
        }

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
        location.href = pars.spteacher_invitition+'?exam_id&teacher_id='+ids;
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
        layer.alert('确认为删除？',function(its){
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
                            });
                            $('#exam-place').find('tbody').attr('index',station_count);
                            continue;
                        }
                    }else{
                        current.push({id:delStore[i].id,count:delStore[i].count});
                    }
                }
            }

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

            var thisSelect = thisElement.find('select').val(),
                prevSelect = thisElement.prev().find('select').val();

            //交换数据
            thisElement.find('select').val(prevSelect).trigger("change");
            prevSelect = thisElement.prev().find('select').val(thisSelect).trigger("change");
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

            var thisSelect = thisElement.find('select').val(),
                nextSelect = thisElement.next().find('select').val();

            //交换数据
            thisElement.find('select').val(nextSelect).trigger("change");
            nextSelect = thisElement.next().find('select').val(thisSelect).trigger("change");
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

    });
    /*$('.teacher-teach').select2({
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
    });*/

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

    var ue = UE.getEditor('editor');

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
        layer.alert('确认为删除？',function(index){
            thisElement.parent("p").remove();
            layer.close(index);
        })
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

    var content =   $('#content').val();

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
        layer.alert('确认为删除？',function(index){
            thisElement.parent("p").remove();
            layer.close(index);
        })
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

/*
 * 考试通知 新增
 * @author lizhiyuan
 * @version 2.0
 * @date    2016-01-09
 */

function smart_assignment(){
    //var testData={"code":1,"message":"success","data":{"1":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452556815","end":1452557715,"items":[{"id":3,"name":"\u6d4b\u8bd5\u5b66\u751f6665","exam_id":1,"user_id":54,"idcard":"51068119592467","mobile":"13699450870","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":2,"name":"\u6d4b\u8bd5\u5b66\u751f5910","exam_id":1,"user_id":52,"idcard":"51068119021099","mobile":"13699451304","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"}]},"2":{"begin":"1452557715","end":1452558615,"items":[{"id":1,"name":"\u6d4b\u8bd5\u5b66\u751f2959","exam_id":1,"user_id":50,"idcard":"51068119352986","mobile":"13699450075","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":4,"name":"\u6d4b\u8bd5\u5b66\u751f3870","exam_id":1,"user_id":56,"idcard":"51068119920106","mobile":"13699450386","code":null,"avator":null,"create_user_id":1,"created_at":null,"updated_at":null}]},"3":{"begin":"1452558615","end":1452559515,"items":[]},"4":{"begin":"1452559515","end":1452560415,"items":[]},"5":{"begin":"1452560415","end":1452561315,"items":[]},"6":{"begin":"1452561315","end":1452562215,"items":[]},"7":{"begin":"1452562215","end":1452563115,"items":[]},"8":{"begin":"1452563115","end":1452564015,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452556815","end":1452557715,"items":[]},"2":{"begin":"1452557715","end":1452558615,"items":[]},"3":{"begin":"1452558615","end":1452559515,"items":[]},"4":{"begin":"1452559515","end":1452560415,"items":[]},"5":{"begin":"1452560415","end":1452561315,"items":[]},"6":{"begin":"1452561315","end":1452562215,"items":[]},"7":{"begin":"1452562215","end":1452563115,"items":[]},"8":{"begin":"1452563115","end":1452564015,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452556815","end":1452557715,"items":[{"id":3,"name":"\u6d4b\u8bd5\u5b66\u751f6665","exam_id":1,"user_id":54,"idcard":"51068119592467","mobile":"13699450870","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":2,"name":"\u6d4b\u8bd5\u5b66\u751f5910","exam_id":1,"user_id":52,"idcard":"51068119021099","mobile":"13699451304","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"}]},"2":{"begin":"1452557715","end":1452558615,"items":[{"id":1,"name":"\u6d4b\u8bd5\u5b66\u751f2959","exam_id":1,"user_id":50,"idcard":"51068119352986","mobile":"13699450075","code":"","avator":"","create_user_id":1,"created_at":"-0001-11-30 00:00:00","updated_at":"-0001-11-30 00:00:00"},{"id":4,"name":"\u6d4b\u8bd5\u5b66\u751f3870","exam_id":1,"user_id":56,"idcard":"51068119920106","mobile":"13699450386","code":null,"avator":null,"create_user_id":1,"created_at":null,"updated_at":null}]},"3":{"begin":"1452558615","end":1452559515,"items":[]},"4":{"begin":"1452559515","end":1452560415,"items":[]},"5":{"begin":"1452560415","end":1452561315,"items":[]},"6":{"begin":"1452561315","end":1452562215,"items":[]},"7":{"begin":"1452562215","end":1452563115,"items":[]},"8":{"begin":"1452563115","end":1452564015,"items":[]}}}},"2":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452564015","end":1452564915,"items":[]},"2":{"begin":"1452564915","end":1452565815,"items":[]},"3":{"begin":"1452565815","end":1452566715,"items":[]},"4":{"begin":"1452566715","end":1452567615,"items":[]},"5":{"begin":"1452567615","end":1452568515,"items":[]},"6":{"begin":"1452568515","end":1452569415,"items":[]},"7":{"begin":"1452569415","end":1452570315,"items":[]},"8":{"begin":"1452570315","end":1452571215,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452564015","end":1452564915,"items":[]},"2":{"begin":"1452564915","end":1452565815,"items":[]},"3":{"begin":"1452565815","end":1452566715,"items":[]},"4":{"begin":"1452566715","end":1452567615,"items":[]},"5":{"begin":"1452567615","end":1452568515,"items":[]},"6":{"begin":"1452568515","end":1452569415,"items":[]},"7":{"begin":"1452569415","end":1452570315,"items":[]},"8":{"begin":"1452570315","end":1452571215,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452564015","end":1452564915,"items":[]},"2":{"begin":"1452564915","end":1452565815,"items":[]},"3":{"begin":"1452565815","end":1452566715,"items":[]},"4":{"begin":"1452566715","end":1452567615,"items":[]},"5":{"begin":"1452567615","end":1452568515,"items":[]},"6":{"begin":"1452568515","end":1452569415,"items":[]},"7":{"begin":"1452569415","end":1452570315,"items":[]},"8":{"begin":"1452570315","end":1452571215,"items":[]}}}},"4":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452571215","end":1452572115,"items":[]},"2":{"begin":"1452572115","end":1452573015,"items":[]},"3":{"begin":"1452573015","end":1452573915,"items":[]},"4":{"begin":"1452573915","end":1452574815,"items":[]},"5":{"begin":"1452574815","end":1452575715,"items":[]},"6":{"begin":"1452575715","end":1452576615,"items":[]},"7":{"begin":"1452576615","end":1452577515,"items":[]},"8":{"begin":"1452577515","end":1452578415,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452571215","end":1452572115,"items":[]},"2":{"begin":"1452572115","end":1452573015,"items":[]},"3":{"begin":"1452573015","end":1452573915,"items":[]},"4":{"begin":"1452573915","end":1452574815,"items":[]},"5":{"begin":"1452574815","end":1452575715,"items":[]},"6":{"begin":"1452575715","end":1452576615,"items":[]},"7":{"begin":"1452576615","end":1452577515,"items":[]},"8":{"begin":"1452577515","end":1452578415,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452571215","end":1452572115,"items":[]},"2":{"begin":"1452572115","end":1452573015,"items":[]},"3":{"begin":"1452573015","end":1452573915,"items":[]},"4":{"begin":"1452573915","end":1452574815,"items":[]},"5":{"begin":"1452574815","end":1452575715,"items":[]},"6":{"begin":"1452575715","end":1452576615,"items":[]},"7":{"begin":"1452576615","end":1452577515,"items":[]},"8":{"begin":"1452577515","end":1452578415,"items":[]}}}},"5":{"1":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452578415","end":1452579315,"items":[]},"2":{"begin":"1452579315","end":1452580215,"items":[]},"3":{"begin":"1452580215","end":1452581115,"items":[]},"4":{"begin":"1452581115","end":1452582015,"items":[]},"5":{"begin":"1452582015","end":1452582915,"items":[]},"6":{"begin":"1452582915","end":1452583815,"items":[]},"7":{"begin":"1452583815","end":1452584715,"items":[]},"8":{"begin":"1452584715","end":1452585615,"items":[]},"9":{"begin":"1452585615","end":1452586515,"items":[]},"10":{"begin":"1452586515","end":1452587415,"items":[]}}},"2":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452578415","end":1452579315,"items":[]},"2":{"begin":"1452579315","end":1452580215,"items":[]},"3":{"begin":"1452580215","end":1452581115,"items":[]},"4":{"begin":"1452581115","end":1452582015,"items":[]},"5":{"begin":"1452582015","end":1452582915,"items":[]},"6":{"begin":"1452582915","end":1452583815,"items":[]},"7":{"begin":"1452583815","end":1452584715,"items":[]},"8":{"begin":"1452584715","end":1452585615,"items":[]},"9":{"begin":"1452585615","end":1452586515,"items":[]},"10":{"begin":"1452586515","end":1452587415,"items":[]}}},"3":{"name":"\u6d4b\u8bd5\u6559\u5ba4001","child":{"1":{"begin":"1452578415","end":1452579315,"items":[]},"2":{"begin":"1452579315","end":1452580215,"items":[]},"3":{"begin":"1452580215","end":1452581115,"items":[]},"4":{"begin":"1452581115","end":1452582015,"items":[]},"5":{"begin":"1452582015","end":1452582915,"items":[]},"6":{"begin":"1452582915","end":1452583815,"items":[]},"7":{"begin":"1452583815","end":1452584715,"items":[]},"8":{"begin":"1452584715","end":1452585615,"items":[]},"9":{"begin":"1452585615","end":1452586515,"items":[]},"10":{"begin":"1452586515","end":1452587415,"items":[]}}}}}}
    var times=[];
    var liwidth=0;//表格的列宽
    var plan    =   $('#plan').html();
    var testData=eval('('+plan+')');
    $('.classroom-box').html('');//清空排考
    maketotal(testData);//页面加载执行排考
    $(".table>li").css("width",liwidth+"px");//给表格设置列宽

    function makeItem(data){

        var dl  =   $('<dl class="clearfloat">');
        var items   =   data.items;
        var everyHeight=data.end-data.start;

        times.push(data.start);
        dl.css("height",everyHeight+"px");
        for(var i in items)
        {
            var dd  = $('<dd>').text(items[i].name);
            dd.attr("sid",items[i].id);
            dd.addClass('student_'+items[i].id).addClass('stu');
            dd.attr("data-sid",items[i].id);
            dd.bind("click",changeStudent);
            dl.append(dd);
        }
        return dl;
    }
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
                $.get('/osce/admin/exam/change-student',{'first':students[0],'second':students[1],'exam_id':exam_id},function(data){
                    var redList =   data.data.redmanList;
                    if(redList.length>0)
                    {

                    }
                    var obs =   $('.clicked');
                    var newObs  =   obs.clone().bind('click',changeStudent);
                    obs.eq(0).after(newObs.eq(1));
                    obs.eq(1).after(newObs.eq(0));
                    obs.remove();
                    $('.stu').removeClass('red');
                    for (var i in redList)
                    {


                        $('.student_'+redList[i]).addClass('red');
                    }
                    $('.clicked').removeClass('clicked');
                });
            }
        }
    }
    function makeCols(data){
        var ul  =   $('<ul>');
        var child   =   data.child;
        var title   =   $('<li class="title">').text(data.name);
        ul.append(title);
        ul.addClass('roomStatioin')
        for(var i in child)
        {

            var itemData    =   child[i];
            var li  =   $('<li>');
            li.addClass("rows"+i);
            li.addClass("batch_inner_row");
            li.attr('data-batchIndex',i);
            var item    =   makeItem(itemData);
            ul.append(li);
            li.append(item);
        }

        return ul;
    }
    function makeAll(data){
        var liNums=0;
        var ul =    $('<ul class="clearfloat table">');

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
            liNums++;
        }
        liwidth=1400/liNums;
        return ul;
    }
    //生成列表
    function maketotal(data){
        for (var i in data){
            var groupData=data[i];
            var dom =   makeAll(groupData);
            dom.attr('data-screeningId',i);
            $('.classroom-box').append(dom);
        }
    }

    //智能排考
    function makePlan(){
        $.get(pars.makePlanUrl,function(testData){
            $('.classroom-box').html('');
            $('.time-list>ul').html('');
            maketotal(testData.data);
            $(".table>li").css("width",liwidth+"px");//给表格设置列宽
            $('#makePlan').one('click',makePlan);
            makeTime();
        });
    }
    $('#makePlan').one('click',makePlan);

//生成时间轴
    function makeTime(){
        times=unique(times);
        times=times.sort(function(a,b){return a>b?1:-1});
        var timeTitle=$('<li class="title">时间</li>');
        $(".time-list>ul").append(timeTitle);
        for(var i in times){
            var li=$('<li>');
            var p1=$('<p>');
            var p2=$('<p>');
            li.append(p1).append(p2);
            var dat=new Date(times[i]*1000);
            var year=dat.getFullYear();
            var month = dat.getMonth()+1;//取得月,js从0开始取,所以+1
            var date1 = dat.getDate(); //取得天
            var hour = dat.getHours();//取得小时
            hour<10?hour='0'+hour:hour=hour;
            var minutes = dat.getMinutes();//取得分钟
            minutes<10?minutes='0'+minutes:minutes=minutes;
            p1.html(year+"/"+month+"/"+date1);
            p2.html(hour+":"+minutes);
            $(".time-list>ul").append(li);

            //var timeHeight=times[times.length-1]-times[0];//时间轴的总高度值
            //var every=timeHeight/(times.length-1);//每段时间高度
            if(i>=times.length-1)
            {
                continue;
            }
            else
            {
                var next    =   times[parseInt(i)+parseInt(1)];
                var every   =   next-times[i];
                li.css("height",every+"px");
            }
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
        $.ajaxFileUpload
        ({
            url:url,
            type:'post',
            fileElementId:'file0',//必须要是 input file标签 ID
            dataType: 'json',
            success: function (data, status)
            {
                if(data.code == 1){
                    layer.alert('导入成功！');
                    location.reload();
                }else{
                    layer.alert(data.message);
                }
            },
            error: function (data, status, e)
            {
                layer.alert(data.message);
            }
        });

    }) ;
    //删除考生
    $(".delete").click(function(){
        var sid=$(this).attr("sid");
        var examId=$(this).attr("examid");
        layer.alert('确认删除？',{btn:['确认','取消']},function(){
            $.ajax({
                type:'post',
                async:true,
                url:pars.deleteUrl,
                data:{id:sid,exam_id:examId},
                success:function(data){
                    if(data.code ==1){
                        layer.msg('删除成功！');
                        location.reload();
                    }else {
                        layer.msg(data.message);
                    }
                }
            })
            //window.location.href=pars.deleteUrl+"?id="+sid+"&exam_id="+examId;
        });
    })
}

/*
 * 新增考生
 * @author lizhiyuan
 * @version 2.0
 * @date    2016-01-12
 */
function examinee_add(){
    $(".return-pre").click(function() {
        location.href=pars.preUrl;
    })
}

function station_assignment(){


    //select2初始化
    $(".js-example-basic-multiple").select2();

    /**
     * 将保存的数据保存
     */
    var arrStore = [];
    $('#examroom').find('tbody').find('tr').each(function(key,elem){


        var selected = $(elem).find('td').eq(1).find('select').val();
        for(var i in selected){
            if(arrStore.length==0){
                arrStore.push({id:selected[i],count:1});
            }else{
                for(var j in arrStore){
                    if(arrStore[j].id==selected[i]){
                        arrStore[j].count += 1;
                    }else{
                        arrStore.push({id:selected[i],count:1});
                    }
                }
            }
        }
    });
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
     * 获取所有上一张表格里的id
     * @author mao
     * @version 1.0
     * @date    2016-01-18
     */
    function getStationID(data){
        var station_ids = [];
        for(var i in arrStore){station_ids.push(arrStore[i].id)}
        return station_ids;
    }
    /**
     * select2初始化
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    $.ajax({
        type:'get',
        async:true,
        url:pars.list,     //请求地址
        data:{station_id:getStationID(arrStore)},
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

                        var teacher = '<option>==请选择==</option>';
                        var typeValue = [0,'技能操作站','SP站','理论操作站'];

                        //写入dom 筛选操作，sp、理论、技能
                        if(data[i].type==2){

                            html += '<tr class="parent-id-'+e.params.data.id+'">'+
                                '<td>'+(station_index+parseInt(i)+1)+'<input type="hidden" name="form_data['+(station_index+parseInt(i)+1)+'][station_id]" value="'+data[i].id+'"/></td>'+
                                '<td>'+data[i].name+'</td>'+
                                '<td>'+typeValue[data[i].type]+'</td>'+
                                '<td>'+
                                '<select class="form-control teacher-teach js-example-basic-multiple" name="form_data['+(station_index+parseInt(i)+1)+'][teacher_id]">'+teacher+'</select>'+
                                '</td>'+
                                '<td class="sp-teacher">'+
                                '<div class="teacher-box pull-left">'+
                                '</div>'+
                                '<div class="pull-right" value="'+(station_index+parseInt(i)+1)+'">'+
                                '<select name="" class="teacher-list js-example-basic-multiple">'+
                                '<option>==请选择==</option>'+
                                '</select>'+
                                '</div>'+
                                '</td>'+
                                '<td><a href="javascript:void(0)" class="invitaion-teacher">发起邀请</a></td>'+
                                '</tr>';
                        }else{

                            html += '<tr class="parent-id-'+e.params.data.id+'">'+
                                '<td>'+(station_index+parseInt(i)+1)+'<input type="hidden" name="form_data['+(station_index+parseInt(i)+1)+'][station_id]" value="'+data[i].id+'"/></td>'+
                                '<td>'+data[i].name+'</td>'+
                                '<td>'+typeValue[data[i].type]+'</td>'+
                                '<td>'+
                                '<select class="form-control teacher-teach js-example-basic-multiple" name="form_data['+(station_index+parseInt(i)+1)+'][teacher_id]">'+teacher+'</select>'+
                                '</td>'+
                                '<td class="sp-teacher">'+
                                '<div class="teacher-box pull-left">'+
                                '</div>'+
                                '<div class="pull-right" value="'+(station_index+parseInt(i)+1)+'">'+
                                '<select name="" class="teacher-list js-example-basic-multiple" disabled="disabled">'+
                                '<option>==请选择==</option>'+
                                '</select>'+
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
                    //重置序号
                    var station_count = 1;
                    $('#exam-place').find('tbody').find('tr').each(function(key,elem){
                        station_count = key + 1;
                        $(elem).find('td').eq(0).text(station_count);
                    });
                    $('#exam-place').find('tbody').attr('index',station_count);
                    continue;
                }
            }else{
                current.push({id:rooms[i].id,count:rooms[i].count});
            }
        }

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
        location.href = pars.spteacher_invitition+'?exam_id&teacher_id='+ids;
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
            '</tr>'+
                //记录计数
            $('#examroom').find('tbody').attr('index',index);
        $('#examroom').find('tbody').append(html);


        $('.room-list').select2({
            placeholder: "==请选择==",
            minimumResultsForSearch: Infinity,
            ajax:{
                url:pars.list,     //请求地址
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
        

    });

    /**
     * 删除一条记录
     * @author mao
     * @version 1.0
     * @date    2016-01-05
     */
    $('#examroom').on('click','.fa-trash-o',function(){
        var thisElement = $(this).parent().parent().parent().parent();
        $.alert({
            title: '提示：',
            content: '确认为删除？',
            confirmButton: '确定',
            confirm: function(){
                thisElement.remove();
            }
        });

        layer.alert('确认为删除？',function(its){
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
                            });
                            $('#exam-place').find('tbody').attr('index',station_count);
                            continue;
                        }
                    }else{
                        current.push({id:delStore[i].id,count:delStore[i].count});
                    }
                }
            }

            $('#examroom').find('tbody').attr('data',JSON.stringify(current));


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

            var thisSelect = thisElement.find('select').val(),
                prevSelect = thisElement.prev().find('select').val();

            //交换数据
            thisElement.find('select').val(prevSelect).trigger("change");
            prevSelect = thisElement.prev().find('select').val(thisSelect).trigger("change");
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

            var thisSelect = thisElement.find('select').val(),
                nextSelect = thisElement.next().find('select').val();

            //交换数据
            thisElement.find('select').val(nextSelect).trigger("change");
            nextSelect = thisElement.next().find('select').val(thisSelect).trigger("change");
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
    $('#exam-place').on('change',".teacher-list",function(){

        var $teacher= $(this).find('option:selected').text().split('==')[0];
        var id = $(this).find('option:selected').val();
        var thisElement = $(this);

        var sql='<div class="input-group teacher pull-left" value="'+id+'">'+
            '<input type="hidden" name="form_data['+thisElement.parent().attr('value')+'][spteacher_id]" value="'+id+'">'+
            '<div class="pull-left">'+$teacher+'</div>'+
            '<div class="pull-left"><i class="fa fa-times"></i></div></div>';
        $(this).parents(".pull-right").prev().append(sql);
    })

    //删除
    $('#exam-place').on('click',".teacher-box i",function(){
        $(this).parents(".teacher").remove();
    })


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
            data:{teacher:ids},
            success:function(data){
              var html = '';
              res = data.data;
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

    });

    /**
     * 老师类型选择 初始化
     * @author mao
     * @version 1.0
     * @date    2016-01-15
     */
    /*$('.teacher-teach').select2({
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
    });*/

}
