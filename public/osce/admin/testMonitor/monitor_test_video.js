



/**
 * 视频播放
 * @author mao
 * @version 1.0
 * @date    2016-03-14
 * @param   {object}   mod  实例化入口
 */
var videoPlay = (function(mod){
    
    /**
     * 初始化video
     * @author mao
     * @version 1.0
     * @date    2016-03-14
     * @param   {number}   width        视频窗口宽度
     * @param   {number}   height       视频窗口高度
     * @param   {number}   count        显示窗口数 1:1x1,2:2x2
     * @param   {[object]}   elem         [dom id]
     * @param   {string}   download_url [下载插件地址]
     */
    mod.initVideo = function(width,height,count,elem,download_url){
        /**
         *检查插件是否已经安装过
         */
        if (-1 == WebVideoCtrl.I_CheckPluginInstall()) {
            alert("您还未安装过插件，请先安装！");
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
                console.log(szInfo);
            }
        });
        WebVideoCtrl.I_InsertOBJECTPlugin(elem);

        /**
         *检查插件是否最新
         */
        if (-1 == WebVideoCtrl.I_CheckPluginVersion()) {
            alert("检测到新的插件版本，请先升级！");
            var iframe  =$('<iframe>').attr('src',download_url);
            var box=    $('<div>').css('display','none');
            box.append(iframe);
            $('body').append(box);
            return;
        }

    }

    
    /**
     * 视频登录
     * @author mao
     * @version 1.0
     * @date    2016-03-14
     * @param   {object}   req {ip,port,username,password}
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
     * 实时预览
     * @author mao
     * @version 1.0
     * @date    2016-03-14
     * @param   {窗口索引}   g_iWndIndex 0，1，2，3
     */
    mod.StartRealPlay = function (g_iWndIndex) {
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
            console.log(szInfo);
        }

        setTimeout(playVideo,1000);
    }

    /**
     *停止播放
     */
    mod.stopPlay = function(g_iWndIndex){
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szInfo = "";

        if (oWndInfo != null) {
            var iRet = WebVideoCtrl.I_Stop();
            if (0 == iRet) {
                szInfo = "停止预览成功！";
            } else {
                szInfo = "停止预览失败！";
            }
            console.log(oWndInfo.szIP + " " + szInfo);
        }
    }

    /**
     * 暂停
     * @author mao
     * @version 1.0
     * @date    2016-03-14
     * @param   {[type]}   g_iWndIndex [description]
     */
    mod.pausePlay = function(g_iWndIndex){

    	var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
                szInfo = "";

            if (oWndInfo != null) {
                var iRet = WebVideoCtrl.I_Pause();
                if (0 == iRet) {
                    szInfo = "暂停成功！";
                } else {
                    szInfo = "暂停失败！";
                }
                console.log(oWndInfo.szIP + " " + szInfo);
            }
    }

    /**
     * 继续播放
     * @author mao
     * @version 1.0
     * @date    2016-03-14
     * @param   {[type]}   g_iWndIndex [description]
     */
    mod.resumePlay = function(g_iWndIndex){
    	var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
                szInfo = "";

            if (oWndInfo != null) {
                var iRet = WebVideoCtrl.I_Resume();
                if (0 == iRet) {
                    szInfo = "恢复成功！";
                } else {
                    szInfo = "恢复失败！";
                }
                console.log(oWndInfo.szIP + " " + szInfo);
            }
    }


    /**
     * 下载
     * @author mao
     * @version 1.0
     * @date    2016-03-14
     * @param   {[string]}   downVideo [下载地址]
     * @param   {[object]}   req       [请求数据]
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

    mod.OSDTime = function(g_iWndIndex){
    	var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szInfo = "";
	
		if (oWndInfo != null) {
			var szTime = WebVideoCtrl.I_GetOSDTime();
			if (szTime != -1) {
				szInfo = " 获取OSD时间成功！";
			} else {
				szInfo = " 获取OSD时间失败！";
			}
			console.log(oWndInfo.szIP + " " + szInfo);
		}
		return szTime;
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
        }

    };


    mod.openVoice = function(){
        var szInfo="";
        setTimeout(function(){
            var iRet = WebVideoCtrl.I_OpenSound();

            if (0 == iRet) {
                szInfo = "打开声音成功！";
            } else {
                szInfo = "打开声音失败！";
            }
            console.log(szInfo);
        },2000);
    };
    return mod;

})(videoPlay||{});


$(function(){
    var pars = JSON.parse(($("#parameter").val()).split("'").join('"'));

    //初始化
    videoPlay.initVideo(700,300,pars.channel,"divPlugin",pars.download);
    //登录
    videoPlay.Login({ip:pars.ip,ports:pars.port,user:pars.username,passwd:pars.password});
    //实时视频
    videoPlay.StartRealPlay(pars.channel);
    //打开声音
    videoPlay.openVoice();



    ////选择标记点跳转视频
    //$(".points li").click(function(){
    //
    //    //拿到标记点初始时间  字符串转化为时间格式
    //    var point=$(this).find("span").text();
    //    dateTime = (point).replace(/-/g,"/");
		//var pointTime = new Date(dateTime);
    //    var pointTime = Date.parse(dateTime);
    //    //传入计算出的进度条长度
    //    i = (pointTime/1000-start)*step;    //计数器重置
    //    $(".progress-bar").css("width",i+"%");
    //    //清除进度条
    //    clearInterval(timer);
    //    timer = setInterval(function(){
    //		progressMove();
    //	},1000);
    //    //启动回放
    //    videoPlay.StartPlayback(0,pars.ip,point,pars.endtime,pars.channel);
    //    videoPlay.openVoice();
    //
    //    //检测是否为暂停
    //    testPause(timer);
    //
    //
    //});
});