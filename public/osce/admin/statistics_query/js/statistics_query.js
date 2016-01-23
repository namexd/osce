/**
 * Created by Administrator on 2016/1/28 0028.
 */
var pars;
$(function(){
    pars = JSON.parse(($("#parameter").val()).split("'").join('"'));
    switch(pars.pagename){
        case "exam_vcr":exam_vcr();break;
    }
});

/*
科目考试视频
author:lizhiyuan
date:2016/1/28
*/
function exam_vcr(){
    var subject_vcr = (function(mod){

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
                    alert(szInfo);
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
            $('#stopplay').click(function(){
                var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
                    szInfo = "";

                if (oWndInfo != null) {
                    var iRet = WebVideoCtrl.I_Stop();
                    if (0 == iRet) {
                        szInfo = "停止播放成功！";
                    } else {
                        szInfo = "停止播放失败！";
                    }
                    alert(oWndInfo.szIP + " " + szInfo);
                }
            });
        }
        mod.StartPlayback=function(g_iWndIndex){
            $(".points li").click(function(){
                var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
                    szIP = $("#ip").val(),
                    bZeroChannel = $("#channels option").eq($("#channels").get(0).selectedIndex).attr("bZero") == "true" ? true : false,
                    iChannelID = $("#channels").val(),//通道号
                    szStartTime = $("#starttime").val(),//开始时间
                    szEndTime = $("#endtime").val(),//结束时间
                    szInfo = "",
                    bChecked = $("#transstream").prop("checked"),
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
                    iRet = WebVideoCtrl.I_StartPlayback(szIP, {
                        iChannelID: iChannelID,
                        szStartTime: szStartTime,
                        szEndTime: szEndTime
                    });
                }

                if (0 == iRet) {
                    szInfo = "开始回放成功！";
                } else {
                    szInfo = "开始回放失败！";
                }
                alert(szIP + " " + szInfo);
            })
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
        return mod;

    })(subject_vcr||{})
    //初始化
    subject_vcr.initVideo(800,600,1,"divPlugin");
    //登录
    subject_vcr.Login({ip:pars.ip,ports:pars.port,user:pars.username,passwd:pars.password});
    //停止播放
    subject_vcr.stopPlay();

    // 暂停
    function clickPause() {
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szInfo = "";

        if (oWndInfo != null) {
            var iRet = WebVideoCtrl.I_Pause();
            if (0 == iRet) {
                szInfo = "暂停成功！";
            } else {
                szInfo = "暂停失败！";
            }
            alert(oWndInfo.szIP + " " + szInfo);
        }
    }
    $("#pause").click(clickPause);
// 恢复
    function clickResume() {
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szInfo = "";

        if (oWndInfo != null) {
            var iRet = WebVideoCtrl.I_Resume();
            if (0 == iRet) {
                szInfo = "恢复成功！";
            } else {
                szInfo = "恢复失败！";
            }
            alert(oWndInfo.szIP + " " + szInfo);
        }
    }
    $("#resume").click(clickResume);
// 慢放
    function clickPlaySlow() {
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szInfo = "";

        if (oWndInfo != null) {
            var iRet = WebVideoCtrl.I_PlaySlow();
            if (0 == iRet) {
                szInfo = "慢放成功！";
            } else {
                szInfo = "慢放失败！";
            }
            alert(oWndInfo.szIP + " " + szInfo);
        }
    }
    $("#playslow").click(clickPlaySlow);
// 快放
    function clickPlayFast() {
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szInfo = "";

        if (oWndInfo != null) {
            var iRet = WebVideoCtrl.I_PlayFast();
            if (0 == iRet) {
                szInfo = "快放成功！";
            } else {
                szInfo = "快放失败！";
            }
            alert(oWndInfo.szIP + " " + szInfo);
        }
    }
    $("#playfast").click(clickPlayFast);
}


