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
                            '<span class="laydate-icon end">'+Time.getTime('YYYY-MM-DD')+' 00:00</span>'+
                        '</td>'+
                        '<td class="laydate">'+
                            '<span class="laydate-icon end">'+Time.getTime('YYYY-MM-DD hh:mm')+'</span>'+
                        '</td>'+
                        '<td>3:00</td>'+
                        '<td>'+
                            '<a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
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
	                        '<span class="laydate-icon end">'+Time.getTime('YYYY-MM-DD')+' 00:00</span>'+
	                    '</td>'+
	                    '<td class="laydate">'+
	                        '<span class="laydate-icon end">'+Time.getTime('YYYY-MM-DD hh:mm')+'</span>'+
	                    '</td>'+
	                    '<td>3:00</td>'+
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
                var current = Date.parse(date) - Date.parse(thisElement.prev().find('span').text());
                var hours = Math.floor(current/(1000*60*60)),
                    minutes = Math.round((current/(1000*60*60)-hours)*60);
                thisElement.next().text(hours+':'+(minutes>9?minutes:('0'+minutes)));
            }else{
                var current = Date.parse(thisElement.next().find('span').text()) - Date.parse(date);
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
        $(this).find('span').css('background-image','none')
    });

    $('table').on('mouseenter','.laydate',function(){
        //图标路径
        var url = background+"/skins/default/icon2.png";
        $(this).find('span').css('background-image','url('+url+')');
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

        thisElement.parent().siblings('.teacher-box').find('.teacher').each(function(key,elem){
            selected.push($(elem).attr('value'));
        });
        console.log(selected)
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
    $(".js-example-basic-multiple").select2()

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
                //考站数据请求
                $.ajax({
                    type:'get',
                    url:'',
                    data:{id:e.params.data.id},
                    async:true,
                    success:function(res){

                        if(res.code!=1){
                            layer.alert(res.message);
                            return;
                        }else{
                            var data = res.data.rows;
                            var html = '';
                            for(var i in data){

                                html += '<tr>'+
                                            '<td>'+data[i].id+'</td>'+
                                            '<td>'+data[i].name+'</td>'+
                                            '<td>'+
                                                '<select class="form-control">'+
                                                    '<option>==请选择==</option>'+
                                                    '<option>李老师</option>'+
                                                    '<option>张老师</option>'+
                                                '</select>'+
                                            '</td>'+
                                        '</tr>';
                                $('exam-place').find('tbody').append(html);
                            }
                        }
                    }
                });
            });
            //删除数据
            $(this).on("select2:unselect", function(e){console.log(e.params.data.id);});

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
                    '<td>'+index+'<input type="hiddin"  name="id['+index+'][id]" value="'+index+'"/></td>'+
                    '<td width="498">'+
                        '<select class="form-control js-example-basic-multiple" multiple="multiple" name="name['+index+'][]"></select>'+
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
            url:'',     //请求地址
            success:function(res){
                //数据处理
                var str = [];
                if(res.code!=1){
                    layer.alert(res.message);
                    return;
                }else{
                    var data = res.data.rows;
                    for(var i in data){
                        str.push({id:data[i].id,text:data[i].name});
                    }
                    //动态加载进去数据
                    $(".js-example-basic-multiple").select2({data:str});
                }
            }
        });
        var data = [{ id: 0, text: 'enhancement' }, { id: 1, text: 'bug' }, { id: 2, text: 'duplicate' }, { id: 3, text: 'invalid' }, { id: 4, text: 'wontfix' }];
        $(".js-example-basic-multiple").select2({data:data});

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
}

