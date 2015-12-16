/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
$(function(){
    pars=JSON.parse($("#parameter").val().split("'").join('"'));
    switch(pars.pagename){
        case "course_observe":course_observe();break;
        case "course_observe_detail":
            //courseObserveDetail.chart({xAxis:["1","2","3","4","5","6"],yAxis:[5, 20, 40, 10, 10, 20]});
            //初始化
            courseObserveDetail.initVideo(1130,600,1,"divPlugin");
            //登录
            courseObserveDetail.Login({ip:'192.168.1.250',ports:'80',user:'admin',passwd:'misrobot123'});
            //切换视频
            courseObserveDetail.changeVideo();

            courseObserveDetail.stopPlay(0)
            break;
    }
})
/*课程监管首页引用
 lizhiyuan
 qq:973261287
 2015/12/15*/
function course_observe(){
    //二级菜单展开
    $(".first-level>p").click(function(){
        if($(this).attr("flag")=="false"){
            $(this).attr("flag","true");
            $(this).find(".glyphicon-chevron-right").hide();
            $(this).find(".glyphicon-chevron-down").show();
            $(this).next().show();
        }else{
            $(this).attr("flag","false");
            $(this).find(".glyphicon-chevron-right").show();
            $(this).find(".glyphicon-chevron-down").hide();
            $(this).next().hide();
        }
    })
    //三级菜单
    $(".second-level>p").click(function(){
        /*if($(this).attr("flag")=="false"){
         $(this).attr("flag","true");
         $(this).find(".glyphicon-chevron-right").hide();
         $(this).find(".glyphicon-chevron-down").show();
         $(this).next().show();
         }else{
         $(this).attr("flag","false");
         $(this).find(".glyphicon-chevron-right").show();
         $(this).find(".glyphicon-chevron-down").hide();
         $(this).next().hide();
         }*/
        $(".second-level>p").removeClass("active");
        $(this).addClass("active");
        var $classroomId=$(this).attr("id");
        getLesson($classroomId,pars.lessonUrl);
    })
    //ajax获取课程和老师信息
    function getLesson(id,url){
        $.ajax({
            url:url,
            type:"get",
            dataType:"json",
            data:{
                id:id
            },
            success: function(result){
                $("#lesson").html(result.content);
                $("#teacher").html(result.teacher);
            }
        });
    }
    //获取当前时间
    function getCurrentTime(){
        var d = new Date();
        var year = d.getFullYear()+"-"+d.getMonth()+"-"+d.getDate();
        if(d.getSeconds()<10){
            var hour= d.getHours()+":"+ d.getMinutes()+":"+ "0"+d.getSeconds();
        }else{
            var hour= d.getHours()+":"+ d.getMinutes()+":"+ d.getSeconds();
        }
        $("#year").html(year);
        $("#hour").html(hour);
        setTimeout(getCurrentTime,1000);
    }
    //页面加载就执行部分
    getCurrentTime();
}



/**
 *课程监管详情页
 *mao
 *qq:3226543648
 *2015/12/16
 */
var courseObserveDetail = (function(mod){

   /**
     *统计图
     */
    mod.chart = function (res){
        var myChart = echarts.init(document.getElementById('main')); 
        var option = {
            tooltip: {
                show: true
            },
            xAxis : [
                {   
                    type : 'category',
                    name : '习题',
                    data : res.xAxis//["1","2","3","4","5","6"]
                }
            ],
            yAxis : [
                {   name : '错误量',
                    type : 'value'
                }
            ],
            series : [
                {
                    
                    type:"bar",
                    smooth:true,
                    itemStyle: {
                        normal: {
                            color:"#74A9FF",
                            lineStyle: {
                                width:3

                            }
                        }
                    },
                    data:res.yAxis//[5, 20, 40, 10, 10, 20]
                }
            ]
        }; 
        // 为echarts对象加载数据 
        myChart.setOption(option);
    };

    /**
     *初始化video
     *width 视频窗口宽度
     *height 视频窗口高度
     *count 显示窗口数  1:1x1,2：2x2
     *elem dom id字符串
     */
    mod.initVideo = function(width,height,count,elem){
        /**
         *检查插件是否已经安装过
         */
        if (-1 == WebVideoCtrl.I_CheckPluginInstall()) {
            alert("您还未安装过插件，双击开发包目录里的WebComponents.exe安装！");
            return;
        }
        
        /**
         *初始化插件参数及插入插件
         */
        WebVideoCtrl.I_InitPlugin(width, height, {
            iWndowType: count,
            cbSelWnd: function (xmlDoc) {
                g_iWndIndex = $(xmlDoc).find("SelectWnd").eq(0).text();
                var szInfo = "当前选择的窗口编号：" + g_iWndIndex;
                alert(szInfo);
            }
        });
        WebVideoCtrl.I_InsertOBJECTPlugin(elem);

        /**
         *检查插件是否最新
         */
        if (-1 == WebVideoCtrl.I_CheckPluginVersion()) {
            alert("检测到新的插件版本，双击开发包目录里的WebComponents.exe升级！");
            return;
        }

    }

    /**
     *登录
     *req {}  ip 端口 用户名 密码
     */
    mod.Login = function (req) {
        //测试数据
        var szIP = req.ip,//'192.168.1.250',
            szPort = req.ports,//'80',
            szUsername = req.user,//'admin',
            szPassword = req.passwd;//'misrobot123';

        if ("" == szIP || "" == szPort) {
            return;
        }

        var iRet = WebVideoCtrl.I_Login(szIP, 1, szPort, szUsername, szPassword, {
            success: function (xmlDoc) {
                alert(szIP + " 登录成功！");
            },
            error: function () {
                alert(szIP + " 登录失败！");
            }
        });

        if (-1 == iRet) {
            alert(szIP + " 已登录过！");
        }
    }

    /**
     *开始预览
     *g_iWndIndex窗口数索引 0，1，2，3
     */
    mod.StartRealPlay = function (g_iWndIndex,iChannelID,szIP) {
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            //szIP = '192.168.1.250';//$("#ip").val(),
            iStreamType = parseInt('1', 10),  //默认主码流
            iChannelID = parseInt(iChannelID, 10),  //通道号
            bZeroChannel =  false,
            szInfo = "";

        if ("" == szIP) {
            return;
        }

        if (oWndInfo != null) {// 已经在播放了，先停止
            WebVideoCtrl.I_Stop();
        }

        var iRet = WebVideoCtrl.I_StartRealPlay(szIP, {
            iStreamType: iStreamType,
            iChannelID: iChannelID,
            bZeroChannel: bZeroChannel
        });

        if (0 == iRet) {
            szInfo = "开始预览成功！";
        } else {
            szInfo = "开始预览失败！";
        }

        alert(szIP + " " + szInfo);
    }

    /**
     *停止播放
     */
    mod.stopPlay = function(g_iWndIndex){
        $('.fa-times').click(function(){
            var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szInfo = "";

            if (oWndInfo != null) {
                var iRet = WebVideoCtrl.I_Stop();
                if (0 == iRet) {
                    szInfo = "停止预览成功！";
                } else {
                    szInfo = "停止预览失败！";
                }
                alert(oWndInfo.szIP + " " + szInfo);
            }
        });
    }

    /**
     *选择不同的摄像头
     */
    mod.changeVideo = function (){
        $('.nav-bar').on('click','li',function(){
            var thisElement = $(this);
            $('.nav-bar li a').removeClass('active');
            thisElement.find('a').addClass('active');
            //切换通道即切换视频 
            var iChannelID = thisElement.attr('value'); //通道号
            mod.StartRealPlay(0,iChannelID,'192.168.1.250');
        });
    }
    

    /**
     *返回列表
     */
    $('.fa-arrows-alt').click(function(){
        //WebVideoCtrl.I_FullScreen(true);
    });

    
    return mod;

})(courseObserveDetail||{})




