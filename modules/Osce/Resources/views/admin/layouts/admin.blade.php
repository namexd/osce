@extends('osce::admin.layouts.base')
@section('meta')
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
    <link href="{{asset('osce/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/font-awesome.min.css?v=4.3.0')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('app/index/style.min.css')}}" rel="stylesheet">

    <style>
        body{
            font-family: 微软雅黑;
            font-size: 14px;
        }
        .row.content-tabs,.page-tabs-content,.page-tabs.J_menuTabs,.content-tabs .roll-nav, .page-tabs-list{height: 50px!important;}
        .roll-nav.roll-left.J_tabLeft,.roll-nav.roll-right.J_tabRight,.roll-nav.roll-right.J_tabClose,.roll-nav.roll-right.J_tabExit{
            margin: 0;
            border: 0;
            border-bottom: 2px solid #2f4050;
        }
        .page-tabs a {
            height: 48px;
            line-height: 50px;
        }
        .roll-right.J_tabClose { 
            width: 170px;
            padding: 0;
            border: 0;
        }
        .roll-nav.roll-right.J_tabClose i{
            margin: 0;
            line-height: 50px;
            width: 53.6px;
        }
        .roll-nav.roll-right.J_tabExit{line-height: 50px;}
        .nav > li.active {border-left: 4px solid #16beb0!important;}
        .content-tabs .roll-nav:hover, .page-tabs a:hover {
            color: #999;
            background: #fff;
            cursor: pointer;
        }
        .roll-nav.roll-right.J_tabClose i:hover{
            color: #777;
            background: #f2f2f2;
            cursor: pointer;
            height: 48px;
        }
    </style>
@stop

@section('head_js')

@stop

@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop

@section('body')
    <div id="wrapper">

        @include('osce::admin.layouts.left')

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
                <button class="roll-nav roll-right J_tabRight" style="right:61px;"><i class="fa fa-forward"></i>
                </button>
                

                <button class="roll-nav roll-right J_tabClose" style="display:none;">
                    <i class="fa fa-envelope"></i>
                    <i class="fa fa-bell"></i>
                    <i class="fa fa-gear"></i>
                </button>
                @if(Auth::user())
                    <a href="{{route('osce.admin.user.getLogout')}}" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
                @else
                    <a href="{{route('osce.admin.getIndex')}}" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 登录</a>
                @endif
            </div>
            <div class="row J_mainContent" id="content-main">

                <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="{{route('osce.admin.index.dashboard')}}" frameborder="0" data-id="index_v1.html" seamless></iframe>

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

    <script src="{{asset('osce/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/bootstrap.min.js?v=3.4.0')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script><!--虚拟滚动条插件未使用-->
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
    <script>
        $(function(){
            $("#side-menu li").click(function(){
                $("body").removeClass("mini-navbar");
            })
        })
    </script>
    <!-- 自定义js -->
    <script src="{{asset('osce/admin/plugins/js/hplus.min.js?v=3.0.0')}}"></script>
    <script type="text/javascript" src="{{asset('osce/admin/plugins/js/contabs.min.js')}}"></script>
    <!-- 第三方插件 -->
    <script src="{{asset('osce/admin/plugins/js/plugins/pace/pace.min.js')}}"></script>
@show{{-- footer区域javscript脚本 --}}


@section('extraSection')
@show{{-- 补充额外的一些东东，不一定是JS，可能是HTML --}}
@stop