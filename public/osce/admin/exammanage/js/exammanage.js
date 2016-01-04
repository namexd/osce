/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "exam_add":exam_add();break; 
    }

});

/**
 * 新增考试
 * @author mao
 * @version 1.0
 * @date    2016-01-04
 * @return  {[type]}   [description]
 */
function exam_add(){

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
         choose: function(dates){ //选择好日期的回调

         }
    };

    /**
     * 日期选择
     * @author mao
     * @version 1.0
     * @date    2016-01-04
     */
    $('.end').click(function(){
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
    $('.laydate').on('mouseleave',function(){
        $(this).find('span').css('background-image','none')
    });

    $('.laydate').on('mouseenter',function(){
        //图标路径
        var url = pars.background_img+"/skins/default/icon2.png";
        $(this).find('span').css('background-image','url('+url+')');
    });

}