@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/demo/webuploader-demo.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/css/demo.css')}}">
    <style type="text/css">
        #left-content .form-control{border: 0;}
        .title .col-sm-2 label span{margin-left: 24%;}
        .title h4 label{font-weight: bolder;}
        .tools-bar{
            height: 30px;
            background: #F5F5F5;
        }
        .nav-bar li{
            list-style: none;
            float: left;
            margin: 2px 5px;
            height: 20px;
            line-height: 28px;
        }
        .nav-bar li a{color: #676a6c!important;}
        .active{font-weight: bolder;}
        .tools-tips{
            float: right;
            margin-top: 5px;
            margin-right: 15px;
        }
        .tools-tips span:first-child{margin-right: 8px;}
        .video{
            height: 700px;
            width: 1080px;
        }
        .fa{cursor: pointer;}
    </style>
@stop


@section('content')
<div class="row">
    <!-- 左侧 -->
    <div class="col-sm-4">
        <div class="row">
            <div class="form-horizontal ibox-content" id="left-content">
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">课程内容</label>
                    <div class="col-sm-10">
                        <div class="form-control">xxxxxxx</div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">授课老师</label>
                    <div class="col-sm-10 select_code">
                        <div class="form-control">李老师</div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">考勤率</label>
                    <div class="col-sm-10" style="height:98px">
                        <div class="form-control">
                            <span>97.8%</span>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td>应到</td>
                                        <td>实到</td>
                                        <td>缺勤</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>30</td>
                                        <td>29</td>
                                        <td>1</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" >练习情况</label>
                    <div class="col-sm-10">
                        <div class="form-control">
                            <button class="btn btn-white cancel" type="button"><i class="fa fa-file-text"></i>&nbsp;查看预习题</button>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group" style="height:270px;">
                    <label class="col-sm-2 control-label">错题统计</label>
                    <div class="col-sm-10">
                        <div class="form-control">
                            <div id="main" style="height:260px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 右侧 -->
    <div class="col-sm-8">
        <div class="container ibox-content">
            <div class="title">
                <h4>
                    <div class="row">
                        <div class="col-sm-8">
                            <label class="font-noraml">临床教学楼1楼10001室</label>
                        </div>
                        <div class="col-sm-2">
                            <label class="font-noraml">2014-10-28 <span>8:00</span></label>
                        </div>
                        <div class="col-sm-2">
                            <label class="pull-right font-noraml">信号强度123</label>
                        </div>
                    </div>
                </h4>
            </div>
            <div class="tools-bar">
                <ul class="nav-bar">
                    <li><a class="active" href="javascript:void(0)">1号摄像机</a></li>
                    <li><a href="javascript:void(0)">2号摄像机</a></li>
                    <li><a href="javascript:void(0)">3号摄像机</a></li>
                    <li><a href="javascript:void(0)">4号摄像机</a></li>
                    <div class="tools-tips">
                        <span class="fa fa-arrows-alt"></span>
                        <span class="fa fa-times"></span>
                    </div>
                </ul>
            </div>
            <div class="content" style="height:600px;">
                <div id="divPlugin" class="video"></div>
            </div>
        </div>
    </div>
</div>   
@stop{{-- 内容主体区域 --}}

@section('only_js')
<script src="{{asset('msc/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<script src="{{asset('msc/admin/js/webVideoCtrl.js')}}"></script>
<script>
$(function(){
    /**
     *统计图
     */
    function chart(res){
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
    }
    //测试
    //chart({xAxis:[],yAxis:[]})
    chart({xAxis:["1","2","3","4","5","6"],yAxis:[5, 20, 40, 10, 10, 20]});  



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
    WebVideoCtrl.I_InitPlugin(1130, 600, {
        iWndowType: 1,
        cbSelWnd: function (xmlDoc) {
            g_iWndIndex = $(xmlDoc).find("SelectWnd").eq(0).text();
            var szInfo = "当前选择的窗口编号：" + g_iWndIndex;
            alert(szInfo);
        }
    });
    WebVideoCtrl.I_InsertOBJECTPlugin("divPlugin");

    /**
     *检查插件是否最新
     */
    if (-1 == WebVideoCtrl.I_CheckPluginVersion()) {
        alert("检测到新的插件版本，双击开发包目录里的WebComponents.exe升级！");
        return;
    }


    /**
     *登录
     */
    function clickLogin() {
        var szIP = '192.168.1.250',
            szPort = '80',
            szUsername = 'admin',
            szPassword = 'misrobot123';

        if ("" == szIP || "" == szPort) {
            return;
        }

        var iRet = WebVideoCtrl.I_Login(szIP, 1, szPort, szUsername, szPassword, {
            success: function (xmlDoc) {
                alert(szIP + " 登录成功！");

                /*$("#ip").prepend("<option value='" + szIP + "'>" + szIP + "</option>");
                setTimeout(function () {
                    $("#ip").val(szIP);
                    getChannelInfo();
                }, 10);*/
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
     */
    function StartRealPlay() {
        var oWndInfo = WebVideoCtrl.I_GetWindowStatus(g_iWndIndex),
            szIP = '192.168.1.250';//$("#ip").val(),
            iStreamType = parseInt('1', 10),  //默认主码流
            iChannelID = parseInt('1', 10),  //通道号
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

    /**
     *选择不同的摄像头
     */
    $('.nav-bar').on('click','li',function(){
        var thisElement = $(this);
        $('.nav-bar li a').removeClass('active');
        thisElement.find('a').addClass('active');
    });

    //返回列表
    $('.fa-arrows-alt').click(function(){
        //WebVideoCtrl.I_FullScreen(true);
    });

    //测试
    clickLogin();//登录
    $('.active').click(function(){
        StartRealPlay();
    });


})
</script>
@stop