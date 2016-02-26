/**
 * Created by Administrator on 2015/12/15 0015.
 */
var pars;
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
    mod.initVideo = function(width,height,count,elem,download_url){
        /**
         *检查插件是否已经安装过
         */
        if (-1 == WebVideoCtrl.I_CheckPluginInstall()) {
            alert("您还未安装过插件，双击开发包目录里的WebComponents.exe安装！");
            var iframe  =$('<iframe>').attr('src',download_url);
            var box=    $('<div>').css('display','none');
            box.append(iframe);
            $('body').append(box);
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
                //alert(szInfo);
            }
        });
        WebVideoCtrl.I_InsertOBJECTPlugin(elem);

        /**
         *检查插件是否最新
         */
        if (-1 == WebVideoCtrl.I_CheckPluginVersion()) {
            alert("检测到新的插件版本，双击开发包目录里的WebComponents.exe升级！");
            var iframe  =$('<iframe>').attr('src',download_url);
            var box=    $('<div>').css('display','none');
            box.append(iframe);
            $('body').append(box);
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
                console.log(szIP + " 登录成功！");
            },
            error: function () {
                console.log(szIP + " 登录失败！");
            }
        });

        if (-1 == iRet) {
            console.log(szIP + " 已登录过！");
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

        //alert(szIP);
        //alert(iStreamType);
        //alert(iChannelID);
        //alert(bZeroChannel);
        function playVideo(){
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
        }
        setTimeout(playVideo,1000);
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
    mod.changeVideo = function (channel){
        //$('.nav-bar').on('click','li',function(){
        //    var thisElement = $(this);
        //    $('.nav-bar li a').removeClass('active');
        //    thisElement.find('a').addClass('active');
        //    //切换通道即切换视频
        //    var iChannelID = thisElement.attr('value'); //通道号
        //    mod.StartRealPlay(0,iChannelID,'192.168.1.250');
        //});
        var iChannelID = channel; //通道号
        mod.StartRealPlay(0,iChannelID,'192.168.1.250');
    }

    /**
     *检测是否大于10
     */
    function testTime(res){
        return res>=10?res:'0'+res;
    }

    /**
     *当前时间写入
     */
    function nowTime(){
        var nowDay = ((new Date()).getFullYear()) +'-'+((new Date()).getMonth()>=9?((new Date()).getMonth()+parseInt(1)):('0'+((new Date()).getMonth()+parseInt(1))))+'-'+((new Date()).getDate()>=10?(new Date()).getDate():('0'+((new Date()).getDate()+parseInt(0))));
        var time = testTime((new Date()).getHours())+':'+testTime((new Date()).getMinutes())+':'+testTime((new Date()).getSeconds());
        $('#nowDay').text(nowDay);
        $('#time').text(time);
    }

    /**
     *实时写入
     */
    mod.update = function(){
        setInterval(nowTime,1000);
    }

    /**
     *返回列表
     */
    $('.fa-arrows-alt').click(function(){
        //WebVideoCtrl.I_FullScreen(true);
    });

    /**
     *下载
     *downVideo 下载地址
     *req 请求数据
     */
    mod.download = function(downVideo,req){
        $('#download').click(function(){
            $.ajax({
                type:'get',
                async:true,
                url:downVideo,
                data:req,
                success:function(res){
                    if(res.code==1){
                        var iframe  =$('<iframe>').attr('src',res.url);
                        var box=    $('<div>').css('display','none');
                        box.append(iframe);
                        $('body').append(box);
                    }
                    else if(res.code==2){
                        alert(res.message);
                    }else{
                        alert(res.message.split(':')[1]);
                    }
                }
            });
        });
    }
    mod.download = function(downVideo,req){
        $('#download').click(function(){
            $.ajax({
                type:'get',
                async:true,
                url:downVideo,
                data:req,
                success:function(res){
                    if(res.code==1){
                        var iframe  =$('<iframe>').attr('src',res.url);
                        var box=    $('<div>').css('display','none');
                        box.append(iframe);
                        $('body').append(box);
                    }
                    else if(res.code==2){
                        alert(res.message);
                    }else{
                        alert(res.message.split(':')[1]);
                    }
                }
            });
        });
    }
    mod.StartPlayback=function(g_iWndIndex,szIP,szStartTime,szEndTime,iChannelID) {

        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
        //szIP = $("#ip").val(),
            bZeroChannel = false,
            //iChannelID = 2,
            //szStartTime = "2016-01-30 10:33:23",
            //szEndTime = "2016-01-30 11:33:23",
            szInfo = "",
            bChecked = false,
            iRet = -1;
        if ("" == szIP) {
            return;
        }

        if (bZeroChannel) {// 零通道不支持回放
            return;
        }

        if (oWndInfo != null) {// 已经在播放了，先停止
            WebVideoCtrl.I_Stop();
        }

        if (bChecked) {// 启用转码回放
            var oTransCodeParam = {
                TransFrameRate: "16",// 0：全帧率，5：1，6：2，7：4，8：6，9：8，10：10，11：12，12：16，14：15，15：18，13：20，16：22
                TransResolution: "2",// 255：Auto，3：4CIF，2：QCIF，1：CIF
                TransBitrate: "23"// 2：32K，3：48K，4：64K，5：80K，6：96K，7：128K，8：160K，9：192K，10：224K，11：256K，12：320K，13：384K，14：448K，15：512K，16：640K，17：768K，18：896K，19：1024K，20：1280K，21：1536K，22：1792K，23：2048K，24：3072K，25：4096K，26：8192K
            };
            iRet = WebVideoCtrl.I_StartPlayback(szIP, {
                iChannelID: iChannelID,
                szStartTime: szStartTime,
                szEndTime: szEndTime,
                oTransCodeParam: oTransCodeParam
            });
        } else {
            var times=setTimeout(function(){
                iRet = WebVideoCtrl.I_StartPlayback(szIP, {
                    iChannelID: iChannelID,
                    szStartTime: szStartTime,
                    szEndTime: szEndTime
                });

                if (0 == iRet) {
                    szInfo = "开始回放成功！";
                } else {
                    szInfo = "开始回放失败！";
                }
                console.log(szIP + " " + szInfo);

            },2000);
        }

    }


    return mod;

})(courseObserveDetail||{})
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    //进度条板块变量
    var timer=null;
    var end=new Date(pars.endtime.split(" ")[0].split("-")[0],pars.endtime.split(" ")[0].split("-")[1]-1,pars.endtime.split(" ")[0].split("-")[2],
        pars.endtime.split(" ")[1].split(":")[0], pars.endtime.split(" ")[1].split(":")[1], pars.endtime.split(" ")[1].split(":")[2]);
    end=Date.parse(end)/1000;
    var start=new Date(pars.starttime.split(" ")[0].split("-")[0],pars.starttime.split(" ")[0].split("-")[1]-1,pars.starttime.split(" ")[0].split("-")[2],
        pars.starttime.split(" ")[1].split(":")[0], pars.starttime.split(" ")[1].split(":")[1], pars.starttime.split(" ")[1].split(":")[2]);
    start=Date.parse(start)/1000;
    var allTime=end-start;//结束时间减去开始时间
    var step=allTime/600;//代表几秒向右前进1px;
    var time_count = 0;
    //初始化
    courseObserveDetail.initVideo(600,300,1,"divPlugin",'');
    //登录
    courseObserveDetail.Login({ip:pars.ip,ports:pars.port,user:pars.username,passwd:pars.password});
    //切换视频
    //courseObserveDetail.changeVideo(1);
    //开始回放
    courseObserveDetail.StartPlayback(0,pars.ip,pars.starttime,pars.endtime,pars.channel);
    //进度条
    progressMove(time_count);
    //暂停
    clickPause(0);
    //恢复
    clickResume(0);
    //默认打开声音
    clickOpenSound();
    //停止
    courseObserveDetail.stopPlay(0);
    //数据实时更新
    courseObserveDetail.update();
    //下载
    courseObserveDetail.download('',{id:$('.active').parent().attr('value'),start:$('#start').val(),end:$('#end').val()});

    /*function progressMove(){
        var i= $(".progress-bar").css("width").split("p")[0];//获取进度条长度
        i  ++;
        $(".progress-bar").css("width",i+"px");
        if(i>=600){
            clearTimeout(timer);
            i=0;
            $(".progress-bar").css("width",0);
            courseObserveDetail.StartPlayback(0,pars.ip,pars.starttime,pars.endtime,pars.channel);

            var oWndInfo = WebVideoCtrl.I_GetWindowStatus(0),
                szInfo   = "";
            endToStartPause();
        }else{
            timer=setTimeout(progressMove,step*100);
        }
        return i;
    }*/
    function progressMove(count){
        var i= count;//$(".progress-bar").css("width").split("p")[0];//获取进度条长度
        i  ++;
        $(".progress-bar").css("width",i+"px");
        if(i>=600){
            clearTimeout(timer);
            i=0;
            $(".progress-bar").css("width",0);
            courseObserveDetail.StartPlayback(0,pars.ip,pars.starttime,pars.endtime,pars.channel);

            var oWndInfo = WebVideoCtrl.I_GetWindowStatus(0),
                szInfo   = "";
            endToStartPause();
        }else{
            timer=setTimeout(function(){progressMove(i)},step*1000);
        }
        //return i;
    }


    function pause(){
        var iRet = WebVideoCtrl.I_Pause();
        if (0 != iRet) {
            setTimeout(pause,1000);
        }
    }
    //进度条重回
    function endToStartPause(){
        var starTime    =   clickGetOSDTime(0);
        if(starTime==undefined||starTime<0)
        {
            setTimeout(endToStartPause);
        }
        else
        {
            //alert(starTime);
            var starTimeInfo    =   starTime.split(' ');
            var datInfo         =   starTimeInfo[0].split('-');
            var timeInfo         =   starTimeInfo[1].split(':');

            var nowDateTime=new Date(datInfo[0],parseInt(datInfo[1])-1,datInfo[2],timeInfo[0],timeInfo[1],timeInfo[2]);
            var nowSeconds =   Date.parse(nowDateTime);
            if(nowSeconds>1)
            {
                pause();
            }
            else
            {
                setTimeout(endToStartPause);
            }
        }
    }
    // 暂停
    function clickPause(g_iWndIndex) {
        clearTimeout(timer);
        $(".resume").click(function(){
            $(".pause").show();
            $(".resume").hide();
            var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
                szInfo = "";

            if (oWndInfo != null) {
                var iRet = WebVideoCtrl.I_Pause();
                if (0 == iRet) {
                    szInfo = "暂停成功！";
                    console.log("成功");
                    clearTimeout(timer);
                } else {
                    szInfo = "暂停失败！";
                    console.log("失败");
                    setTimeout(function(){
                        clickPause(g_iWndIndex);
                    },1000);
                }
                //alert(oWndInfo.szIP + " " + szInfo);
            }
        })
    }

    //获取视频时间
    function clickGetOSDTime(g_iWndIndex) {
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex);

        //if (oWndInfo != null) {
            var szTime = WebVideoCtrl.I_GetOSDTime();

            if (szTime != -1) {
                return szTime;
            } else {
                return szTime;
                //showOPInfo(oWndInfo.szIP + " 获取OSD时间失败！");
            }
        //}
    }
// 恢复
    function clickResume(g_iWndIndex){
        progressMove(time_count);
        $(".pause").click(function(){
            $(".pause").hide();
            $(".resume").show();
            var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
                szInfo = "";

            if (oWndInfo != null) {
                var iRet = WebVideoCtrl.I_Resume();
                if (0 == iRet) {
                    szInfo = "恢复成功！";
                    timer=setTimeout(progressMove,step*1000);
                } else {
                    szInfo = "恢复失败！";
                }
                //alert(oWndInfo.szIP + " " + szInfo);
            }
        })
    }
    function times(res){
        res<10?res='0'+res:res=res;
        return res;
    }
    // 打开声音
    function clickOpenSound(){
        var szInfo="";
        setTimeout(function(){
            var iRet = WebVideoCtrl.I_OpenSound();

            if (0 == iRet) {
                szInfo = "打开声音成功！";
            } else {
                szInfo = "打开声音失败！";
            }
        },2000);
        //alert( szInfo);
    }
    // 关闭声音
    function clickCloseSound() {
        var szInfo = "";
            var iRet = WebVideoCtrl.I_CloseSound();
            if (0 == iRet) {
                szInfo = "关闭声音成功！";
            } else {
                szInfo = "关闭声音失败！";
            }
            //alert(szInfo);
    }
    //获取点击相对位置
    $("#progress").click(function(e){
        var left=e.clientX-($("#progress").offset().left);
        console.log(left);
        //var current=progressMove();
        //$(".progress-bar").css("width",left+"px");
        //alert(left*step);
        var dateTime=new Date(pars.starttime.split(" ")[0].split("-")[0],pars.starttime.split(" ")[0].split("-")[1]-1,pars.starttime.split(" ")[0].split("-")[2],
            pars.starttime.split(" ")[1].split(":")[0], pars.starttime.split(" ")[1].split(":")[1], pars.starttime.split(" ")[1].split(":")[2]);
        var seconds =   Date.parse(dateTime);
        //alert(seconds+left*step*1000);
        seconds     =   seconds+left*step*1000;
        var time_count = seconds;
        clearTimeout(timer);
        progressMove(time_count);
        var dat=new Date(seconds);
        var year=dat.getFullYear();
        var month=dat.getMonth()+1;
        month=times(month);
        var days=dat.getDate();
        days=times(days);
        var hour=dat.getHours();
        hour=times(hour);
        var min=dat.getMinutes();
        min=times(min);
        var s=dat.getSeconds();
        s=times(s);
        var newstart=year+"-"+month+"-"+days+" "+hour+":"+min+":"+s;
        courseObserveDetail.StartPlayback(0,pars.ip,newstart,pars.endtime,pars.channel);
    })
    //选择标记点跳转视频
    $(".points li").click(function(){
        var point=$(this).find("span").text();
        var pointTime=new Date(point.split(" ")[0].split("-")[0],point.split(" ")[0].split("-")[1]-1,point.split(" ")[0].split("-")[2],
            point.split(" ")[1].split(":")[0], point.split(" ")[1].split(":")[1], point.split(" ")[1].split(":")[2]);
        pointTime=Date.parse(pointTime);
        var move=(pointTime/1000-start)/step;//点击锚点时进度条应跳的位置
        time_count = pointTime/1000-start;
        clearTimeout(timer);
        progressMove(time_count);
        courseObserveDetail.StartPlayback(0,pars.ip,point,pars.endtime,pars.channel);
    })
})







