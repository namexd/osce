@extends('layouts.base')
@section('meta')
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
    <link href="{{asset('msc/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/plugins/css/font-awesome.min.css?v=4.3.0')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/plugins/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('app/index/style.min.css')}}" rel="stylesheet">
@stop

@section('head_js')

@stop

@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop

@section('body')
    <div id="wrapper">

        @include('layouts/left')

        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">

            <div class="row content-tabs">
                <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
                </button>
                <nav class="page-tabs J_menuTabs">
                    <div class="page-tabs-content">
                        <a href="javascript:;" class="active J_menuTab" data-id="index_v1.html">首页</a>
                    </div>
                </nav>
                <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
                </button>

                <button class="roll-nav roll-right J_tabClose">
                    <i class="fa fa-envelope"> 通知</i>
                    <i class="fa fa-bell"> 消息</i>
                    <i class="fa fa-gear"> 设置</i>
                </button>

                <a href="login.html" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
            </div>
            <div class="row J_mainContent" id="content-main">

                    <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="{{route('msc.verify.student')}}" frameborder="0" data-id="index_v1.html" seamless></iframe>

            </div>
            <div class="footer">
                <div class="pull-right">&copy; 2015-2018 <a href="/" target="_blank">misrobot.com</a>
                </div>
            </div>
        </div>
        <!--右侧部分结束-->


    </div>
@show

@section('footer_js')
        <!-- 全局js -->

    <script src="{{asset('msc/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/bootstrap.min.js?v=3.4.0')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>

    <!-- 自定义js -->
    <script src="{{asset('msc/admin/plugins/js/hplus.min.js?v=3.0.0')}}"></script>
    <script type="text/javascript" src="{{asset('msc/admin/plugins/js/contabs.min.js')}}"></script>
    <!-- 第三方插件 -->
    <script src="{{asset('msc/admin/plugins/js/plugins/pace/pace.min.js')}}"></script>
@show{{-- footer区域javscript脚本 --}}


@section('extraSection')
@show{{-- 补充额外的一些东东，不一定是JS，可能是HTML --}}
@stop