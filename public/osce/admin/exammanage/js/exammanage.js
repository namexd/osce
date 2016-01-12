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
        case "smart_assignment":smart_assignment();break;
        case "examinee_manage":examinee_manage();break;
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

        var html = '<tr>'+
                        '<td>'+parseInt(index)+'</td>'+
                        '<td class="laydate">'+
                            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][begin_dt]" value="'+Time.getTime('YYYY-MM-DD')+' 00:00"/>'+
                        '</td>'+
                        '<td class="laydate">'+
                            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][end_dt]" value="'+Time.getTime('YYYY-MM-DD hh:mm')+'"/>'+
                        '</td>'+
                        '<td>3:00</td>'+
                        '<td>'+
                            '<a href="javascript:void(0)"><span class="read  state1"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        '</td>'+
                    '</tr>'+
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
     * 新增一条
     * @author  mao
     * @version  1.0
     * @date        2016-01-05
     */
    $('#add-new').click(function(){
        //计数器标志
        var index = $('#add-basic').find('tbody').attr('index');
        index = parseInt(index) + 1;

        var html = '<tr>'+
	                    '<td>'+parseInt(index)+'</td>'+
                        '<td class="laydate">'+
                            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][begin_dt]" value="'+Time.getTime('YYYY-MM-DD')+' 00:00"/>'+
                        '</td>'+
                        '<td class="laydate">'+
                            '<input type="text" class="laydate-icon end" name="time['+parseInt(index)+'][end_dt]" value="'+Time.getTime('YYYY-MM-DD hh:mm')+'"/>'+
                        '</td>'+
	                    '<td>3:00</td>'+
	                    '<td>'+
	                        '<a href="javascript:void(0)"><span class="read  state1"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
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
                var current = Date.parse(date) - Date.parse(thisElement.prev().find('input').val());
                var hours = Math.floor(current/(1000*60*60)),
                    minutes = Math.round((current/(1000*60*60)-hours)*60);
                thisElement.next().text(hours+':'+(minutes>9?minutes:('0'+minutes)));
            }else{
                var current = Date.parse(thisElement.next().find('input').val()) - Date.parse(date);
                var hours = Math.floor(current/(1000*60*60)),
                    minutes = Math.round((current/(1000*60*60)-hours)*60);
                thisElement.next().next().text(hours+':'+(minutes>9?minutes:('0'+minutes))); 
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

    /**
     * 数组去重
     * @author mao
     * @version 1.0
     * @date    2016-01-08
     * @param   {array}   arr1 [description]
     * @param   {array}   arr2 [description]
     * @return  {array}        结果数据
     */
    function tab(arr1,arr2){
        var arr = arr1.concat(arr2);
        var lastArr = [];
        for(var i = 0;i<arr.length;i++){
            var current = unique(arr[i],lastArr);
            if(! current.flag){

                lastArr.push(arr[i]);
                if(lastArr[lastArr.length-1]['flag']==undefined){
                    lastArr[lastArr.length-1]['flag'] = 1;
                }else{
                    lastArr[lastArr.length-1]['flag'] = lastArr[lastArr.length-1].flag +1;
                }
            }else{
                if(lastArr[lastArr.length-1].flag==undefined){
                    lastArr[lastArr.length-1]['flag'] = 1;
                }else{
                    lastArr[lastArr.length-1]['flag'] = lastArr[lastArr.length-1].flag +1;
                }
            }
        }
        return lastArr;
    }
    function unique(n,arr)
    {
        for(var i=0;i<arr.length;i++){
            //对相同id的数据进行去重
            if(n.id==arr[i].id){
                return {id:i,flag:true};
            }
        }
        return {flag:false};
    }

	//select2初始化
    $(".js-example-basic-multiple").select2();

    //数据选择计数器
    var select2_data,
        select2_data_del;

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
            //选择的数据
            $(this).on("select2:select", function(e){

                //让select只进入一次
                if(select2_data!=undefined){
                    if(select2_data.id==e.params.data.id){
                        select2_data = null;
                        return;
                    }
                    select2_data = e.params.data;
                }else{
                    select2_data = e.params.data;
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
                        var sp_no=null;
                        if(thisElement.attr('data')==undefined){
                            sp_no = '{}';
                        }
                        else{
                            sp_no = JSON.parse(thisElement.attr('data'));
                        }

                        if(res.code!=1){
                            layer.alert(res.message);
                            return;
                        }else{
                            var res_data = res.data,
                                html = '',
                                data;
                            
                            //thisElement.attr('data',JSON.stringify(data));
                            //数据去重
                            if(sp_no!='{}'){
                                data = tab(sp_no,res_data);
                            }else{
                                data = tab([],res_data);
                            }

                            //准备dom
                            for(var i in data){

                                var teacher = '<option>==请选择==</option>';
                                var typeValue = [0,'技能操作站','SP站'];
                                /*for(var j in data[i].teacher){
                                    teacher += '<option value="'+data[i].teacher[j].id+'">'+data[i].teacher[j].name+'</option>'
                                }*/

                                html += '<tr>'+
                                            '<td>'+(parseInt(i)+1)+'<input type="hidden" name="station['+(parseInt(i)+1)+'][id]" value="'+data[i].id+'"/></td>'+
                                            '<td>'+data[i].name+'</td>'+
                                            '<td>'+typeValue[data[i].type]+'</td>'+
                                            '<td>'+
                                                '<select class="form-control teacher-teach js-example-basic-multiple" multiple="multiple" name="station['+(parseInt(i)+1)+'][teacher_id]">'+teacher+'</select>'+
                                            '</td>'+
                                            '<td class="sp-teacher">'+
                                                '<div class="teacher-box pull-left">'+
                                                '</div>'+
                                                '<div class="pull-right" value="'+(parseInt(i)+1)+'">'+
                                                    '<select name="" class="teacher-list js-example-basic-multiple">'+
                                                        '<option>==请选择==</option>'+
                                                    '</select>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td><a href="">发起邀请</a></td>'+
                                        '</tr>';
                            }
                            //动态插入考场安排
                            thisElement.html(html);
                            thisElement.attr('data',JSON.stringify(data));


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
                             * @date    2016-01-11
                             */
                            var select2_Object;
                            select2_Object = $('.teacher-list').select2({
                                placeholder: "==请选择==",
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
                                        for(var i in res.data){
                                            str.push({id:res_data[i].id,text:res.data[i].name});
                                        }

                                        //加载入数据
                                        return {
                                            results: str
                                        };
                                    }

                                }
                            });

                        }
                    }
                });
            });

            //删除数据
            $(this).on("select2:unselect", function(e){

                //让select只进入一次
                if(select2_data_del!=undefined){
                    if(select2_data_del.id==e.params.data.id){
                        select2_data = null;
                        return;
                    }
                    select2_data_del = e.params.data;
                }else{
                    select2_data_del = e.params.data;
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
                        var sp_no;
                        if(thisElement.attr('data')==undefined){
                            sp_no = '{}';
                        }
                        else{
                            sp_no = JSON.parse(thisElement.attr('data'));
                        }

                        if(res.code!=1){
                            layer.alert(res.message);
                            return;
                        }else{

                            var res_data = res.data,
                                html = '',
                                data = [];

                            //数据去重 删除数据
                            for(var i in sp_no){
                                for(var j in res_data){
                                    if(sp_no[i].id==res_data[j].id){sp_no[i].flag--}
                                }
                                if(sp_no[i].flag!=0)data.push(sp_no[i]);
                            }

                            //准备dom
                            for(var i in data){

                                var teacher = '<option>==请选择==</option>';
                                var typeValue = [0,'技能操作站','SP站'];

                                var teacher = '<option>==请选择==</option>';
                                for(var j in data[i].teacher){
                                    teacher += '<option value="'+data[i].teacher[j].id+'">'+data[i].teacher[j].name+'</option>'
                                }

                                html += '<tr>'+
                                            '<td>'+(parseInt(i)+1)+'<input type="hidden" name="station['+(parseInt(i)+1)+'][id]" value="'+data[i].id+'"/></td>'+
                                            '<td>'+data[i].name+'</td>'+
                                            '<td>'+typeValue[data[i].type]+'</td>'+
                                            '<td>'+
                                                '<select class="form-control teacher-teach" name="station['+(parseInt(i)+1)+'][teacher_id]">'+teacher+'</select>'+
                                            '</td>'+
                                            '<td class="sp-teacher">'+
                                                '<div class="teacher-box pull-left">'+
                                                '</div>'+
                                                '<div class="pull-right" value="'+(parseInt(i)+1)+'">'+
                                                    '<select name="" class="teacher-list">'+
                                                        '<option value="">==请选择==</option>'+
                                                    '</select>'+
                                                '</div>'+
                                            '</td>'+
                                            '<td><a href="">发起邀请</a></td>'+
                                        '</tr>';
                            }
                            //动态插入考场安排
                            thisElement.html(html);
                            thisElement.attr('data',JSON.stringify(data));
                        }
                    }
                });

            });

        }
    });

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
                        '<select class="form-control js-example-basic-multiple" multiple="multiple" name="room['+index+']"></select>'+
                    '</td>'+
                    '<td class="necessary">必考</td>'+
                    '<td>'+
                        '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>'+
                    '</td>'+
                '</tr>'+
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
                    $(".js-example-basic-multiple").select2({data:str});
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


    $('#exam-place').on('change',".teacher-list",function(){

        var $teacher= $(this).find('option:selected').text().split('==')[0];
        var id = $(this).find('option:selected').val();
        var thisElement = $(this);

        var sql='<div class="input-group teacher pull-left" value="'+id+'">'+
                '<input type="hidden" name="name="station['+thisElement.parent().attr('value')+'][spteacher_id][]" value="'+id+'">'+
                '<div class="pull-left">'+$teacher+'</div>'+
                '<div class="pull-left"><i class="fa fa-times"></i></div></div>';
        $(this).parents(".pull-right").prev().append(sql);
    })

    //删除
    $('#exam-place').on('click',".teacher-box i",function(){
        $(this).parents(".teacher").remove();
    })



   /* $('#exam-place').on('click','.teacher-teach',function(){

        var thisElement = $(this);
        $.ajax({
            type:'get',
            url: pars.teacher_list,   //修改请求地址
            async:true,
            data:{teacher:[]},
            success:function(res){

                var source = [];

                if(res.code!=1){
                    layer.alert('res.message');
                }else{
                    var data = res.data;
                    var html = '<option>==选择==</option>';
                    for(var i in data){

                        html += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                   thisElement.html(html);
                }
            }
        });
    });*/

    $('.teacher-teach').select2({
        placeholder: "==请选择==",
        ajax:{
            url: pars.teacher_list,
            dataType: 'json',
            data: function (term, page) {console.log(term,page)
                return {
                        input: term
                    };
            },
            results: function (data) {
                allOption=data;
                   return {results:data};
                }

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

    var ue = UE.getEditor('editor');
}

/*
 * 考试通知 新增
 * @author lizhiyuan
 * @version 2.0
 * @date    2016-01-09
*/
function smart_assignment(){


        //模拟数据

        //var everyli=1000/smartlist.length;
        //var lists="";//代表列
        //var rooms="";//代表教室
        //for(var i=0;i<smartlist.length;i++){
        //    lists+="<li style='width: "+everyli+"px'><dl><dt>"+smartlist[i].name+"</dt><dl/><li/>";
        //    for(var j=0;j<smartlist[i].child.length;j++){
        //        for(var k=0;k<smartlist[i].child[j].items.length;k++){
        //            $(".classroom-box>ul").append("<li style='width:"+everyli+"px'><dl><dt>"+smartlist[i].name+"</dt>" +
        //                "<dd>"+smartlist[i].child[j].items[k]+"<dd/></dl></li>");
        //        }
        //        console.log(smartlist[i].child[j].items.length);
        //
        //
        //    }
        //}


}
function makeItem(data){
    //var data    ={
    //        'begin':1000,
    //        'end':1600,
    //        'items':[
    //            {
    //                id:1,
    //                name:'李治远3'
    //            },
    //            {
    //                id:2,
    //                name:'李治远2'
    //            },
    //            {
    //                id:3,
    //                name:'李治远1'
    //            }
    //        ]
    //    };

    var dl  =   $('<dl class="clearfloat">');

    var items   =   data.items;
    var everyHeight=data.end-data.begin;
    dl.css("height",everyHeight/10+"px");

    for(var i in items)
    {
        var dd  = $('<dd>').text(items[i].name);
        dd.attr("sid",items[i].id);

        dd.bind("click",changeTwo);
        dl.append(dd);
    }
    return dl;
}
function makeCols(data){
    //var data    =   {
    //    'name':'教室404',
    //    'child':[
    //        {
    //            'begin':1000,
    //            'end':1500,
    //            'items':[
    //                '罗海华2',
    //                '李治远3',
    //                '毛云刚5',
    //            ]
    //        },
    //        {
    //            'begin':1500,
    //            'end':2800,
    //            'items':[
    //                '罗海华6',
    //                '李治远7',
    //                '毛云刚6',
    //            ]
    //        },
    //
    //    ],
    //
    //};
    var ul  =   $('<ul>');


    var child   =   data.child;
    var title   =   $('<li class="title">').text(data.name);
    ul.append(title);
    for(var i in child)
    {

        var itemData    =   child[i];
        var li  =   $('<li>');
        li.addClass("rows"+i);
        var item    =   makeItem(itemData);
        ul.append(li);
        li.append(item);
    }

    return ul;
}
function makeAll(data){
    var ul =    $('<ul class="clearfloat">');
    var liWidth=1000/data.length;
    for(var i in data)
    {

        var colData     =   data[i];
        var colul       =   makeCols(colData);
        var li          =   $('<li>');
        li.append(colul);
        li.css("width",liWidth+"px");
        li.addClass("cols"+i);
        ul.append(li);
    }
    return ul;
}
$(function(){
    var smartlist=[
        {
            'name':'教室404',
            'child':[
                {
                    'begin':1000,
                    'end':1500,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },
                {
                    'begin':1500,
                    'end':2800,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },

            ],

        },
        {
            'name':'教室403',
            'child':[
                {
                    'begin':1000,
                    'end':1600,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },
                {
                    'begin':1600,
                    'end':2200,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },

            ],
        },
        {
            'name':'教室403',
            'child':[
                {
                    'begin':1000,
                    'end':1600,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },
                {
                    'begin':1600,
                    'end':2200,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },

            ],
        },
        {
            'name':'教室403',
            'child':[
                {
                    'begin':1000,
                    'end':2000,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },
                {
                    'begin':1600,
                    'end':2200,
                    'items':[
                        {
                            id:1,
                            name:'李治远3'
                        },
                        {
                            id:2,
                            name:'李治远2'
                        },
                        {
                            id:3,
                            name:'李治远1'
                        }
                    ]
                },

            ],
        },
    ];
    var dom =   makeAll(smartlist);
    $('.classroom-box').append(dom);
    //changeTwo();
});

//点击两个表格可进行交换
function changeTwo(){

    if($(this).hasClass('active'))
    {
        $(this).removeClass('active');
        return ;
    }
    $(this).addClass('active');
    if($(".active").length-1==2){
        var change1=$($(".active")[1]).html();
        var change2=$($(".active")[2]).html();
        $($(".active")[2]).html(change1);
        $($(".active")[1]).html(change2);
        if($(".error").length==0){
        }else{

        }
        console.log($($(".active")[1]).parent().parent().parent().parent().attr("class"))
        console.log($($(".active")[2]).parent().parent().parent().parent().attr("class"))
        $.ajax({
            url:"",
            type:"get",
            dataType:"json",
            data:{
                    id1:$($(".active")[1]).attr("sid"),
                    id2:$($(".active")[2]).attr("sid"),
                    row1:$($(".active")[1]).parent().parent().attr("class"),
                    row2:$($(".active")[2]).parent().parent().attr("class"),
                    col1:$($(".active")[1]).parent().parent().parent().parent().attr("class"),
                    col2:$($(".active")[2]).parent().parent().parent().parent().attr("class")
            },
            success: function(result) {
                console.log(result);
                $("dd").removeClass("active");
                var status=1//冲突状态
                if(status==1){
                    $($(".active")[1]).addClass("error");
                    $($(".active")[2]).addClass("error");
                    $(".save").attr("disabled");
                }else{
                    $($(".active")[1]).removeClass("error");
                    $($(".active")[2]).removeClass("error");
                    $(".save").removeAttr("disabled");
                }
            }})
    }
}





//文件导入
function examinee_manage(){
    alert(pars.excel);
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
                    layer.alert('导入成功！');

                    /*/!**
                     * 数据导入
                     * @author mao
                     * @version 1.0
                     * @date    2016-01-08
                     *!/
                     var html = '';
                     var res = data.data;
                     var index = parseInt($('tbody').attr('index'));

                     for(var i in res){
                     if(res[i].level==1){

                     index++;
                     //添加父级dom
                     html += '<tr parent="'+index+'" current="0"  class="pid-'+index+'">'+
                     '<td>'+index+'</td>'+
                     '<td>'+
                     '<div class="form-group">'+
                     '<label class="col-sm-2 control-label">考核点:</label>'+
                     '<div class="col-sm-10">'+
                     '<input id="select_Category"  class="form-control" value="'+res[i].check_point+'" name="content['+res[i].sort+'][title]"/>'+
                     '</div>'+
                     '</div>'+
                     '</td>'+
                     '<td>'+
                     '<select class="form-control" name="score['+index+'][total]">'+
                     '<option value="'+res[i].score+'">'+res[i].score+'</option>'+
                     '<option value="1">1</option>'+
                     '<option value="2">2</option>'+
                     '<option value="3">3</option>'+
                     '<option value="4">4</option>'+
                     '</select>'+
                     '</td>'+
                     '<td>'+
                     '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                     '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>'+
                     '</td>'+
                     '</tr>';

                     for(var j in res){
                     if(res[j].level==2&&res[j].pid==res[i].sort){

                     //处理子级dom
                     html += '<tr child="'+res[j].sort+'" class="pid-'+index+'" >'+
                     '<td>'+index+'-'+res[j].sort+'</td>'+
                     '<td>'+
                     '<div class="form-group">'+
                     '<label class="col-sm-2 control-label">考核项:</label>'+
                     '<div class="col-sm-10">'+
                     '<input id="select_Category"  class="form-control" value="'+res[j].check_item+'" name="content['+res[i].score+']['+res[j].sort+']"/>'+
                     '</div>'+
                     '</div>'+
                     '<div class="form-group">'+
                     '<label class="col-sm-2 control-label">评分标准:</label>'+
                     '<div class="col-sm-10">'+
                     '<input id="select_Category"  class="form-control" value="'+res[j].answer+'" name="description['+res[i].score+']['+res[j].sort+']"/>'+
                     '</div>'+
                     '</div>'+
                     '</td>'+
                     '<td>'+
                     '<select class="form-control" name="score['+index+']['+res[j].sort+']">'+
                     '<option value="'+res[j].score+'">'+res[j].score+'</option>'+
                     '<option value="1">1</option>'+
                     '<option value="2">2</option>'+
                     '<option value="3">3</option>'+
                     '<option value="4">4</option>'+
                     '</select>'+
                     '</td>'+
                     '<td>'+
                     '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                     '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>'+
                     '<a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>'+
                     '</td>'+
                     '</tr>';
                     }
                     }
                     }
                     }
                     $('tbody').attr('index',index);
                     $('tbody').append(html);*/



                }
            },
            error: function (data, status, e)
            {
                layer.alert('导入失败！');
            }
        });
    }) ;
}

