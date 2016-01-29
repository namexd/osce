@extends('osce::wechat.layouts.base')
@section('meta')
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
    <link href="{{asset('osce/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('osce/wechat/common/css/font-awesome/css/font-awesome.css')}}" rel="stylesheet"/>
    <link href="{{asset('osce/admin/plugins/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/style.min.css?v=3.0.0')}}" rel="stylesheet"> <!--H+模板通用CSS未使用-->
    <link href="{{asset('osce/wechat/html5-boilerplate/dist/css/normalize.css')}}" rel="stylesheet"/>
	<link href="{{asset('osce/wechat/html5-boilerplate/dist/css/main.css')}}" rel="stylesheet"/>
	<link href="{{asset('osce/wechat/jquery-confirm/jquery-confirm.css')}}"  rel="stylesheet"/>
	<link href="{{asset('osce/wechat/common/css/commons.css')}}"  rel="stylesheet"/>
    <link href="{{asset('osce/wechat/common/css/table_commons.css')}}"  rel="stylesheet"/>
    <style>
        i.form-control-feedback.glyphicon.glyphicon-ok, i.form-control-feedback.glyphicon.glyphicon-remove{display: none!important;}
    </style>
@stop

@section('head_js')
	<!-- 全局js -->
    <script src="{{asset('osce/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/bootstrap.min.js?v=3.4.0')}}"></script>

    <script src="{{asset('osce/admin/plugins/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>   <!--菜单插件未使用-->
    
    <script src="{{asset('osce/admin/plugins/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script><!--虚拟滚动条插件未使用-->
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script><!--弹框插件未使用-->
	<script src="{{asset('osce/wechat/common/js/jquery.cookie.js')}}"></script><!--会话添加插件插件未使用-->

	<script src="{{asset('osce/wechat/html5-boilerplate/dist/js/vendor/modernizr-2.8.3.min.js')}}"></script>
	<script src="{{asset('osce/wechat/jquery-confirm/jquery-confirm.js')}}"></script>
	<script src="{{asset('osce/wechat/common/js/iscroll.js')}}"></script>
  	<script src="{{asset('osce/wechat/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('osce/wechat/validate/jquery.metadata.js')}}"></script>
    <script src="{{asset('osce/wechat/validate/messages_zh.min.js')}}"></script>
    <script type="text/javascript">
        var totalpages="0";
        var　page_height = "0";
        var away_top= "0";
        var window_height= "0";
        var today;
        $(document).ready(function(){
            today = getFormattedDate(); //获取当前日期
        });
        function getFormattedDate(date) { //获取当前日期
            var date=new Date();
            var year = date.getFullYear();
            var month = (1 + date.getMonth()).toString();
            month = month.length > 1 ? month : '0' + month;
            var day = date.getDate().toString();
            day = day.length > 1 ? day : '0' + day;
            return year + '-' + month + '-' + day;
        }
        $(window).scroll(function(e){
            page_height = $(document).height();
            away_top = $(document).scrollTop();//当前顶部到窗口顶部的距离
            window_height = $(window).height()
            if(away_top > 300){  /*回到顶部*/
                $("#go_top").show(400);
                $('#go_top').click(function(){
                    $(document).scrollTop(0);
                })
            }else{
                $("#go_top").hide(200);
            }
            /*判定到达底部*/
        })
        //ajax 请求时loading效果
        $(document).ajaxStart(function() {
            $("#layer_loading").show();//加载中显示
        });
        $(document).ajaxSuccess(function(event, request, settings) {
            $("#layer_loading").hide();//加载完成隱藏
        });
    </script>

@stop
@section('head_style')

@show
@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop

@section('body')

    @section('content')
    @show{{-- 内容主体区域 --}}

    @section('layer_loading')
    <div id="layer_loading" style="display: none;">
        <div class="more_show_txt">
            <div class="sk-spinner sk-spinner-fading-circle">
                <div class="sk-circle1 sk-circle"></div>
                <div class="sk-circle2 sk-circle"></div>
                <div class="sk-circle3 sk-circle"></div>
                <div class="sk-circle4 sk-circle"></div>
                <div class="sk-circle5 sk-circle"></div>
                <div class="sk-circle6 sk-circle"></div>
                <div class="sk-circle7 sk-circle"></div>
                <div class="sk-circle8 sk-circle"></div>
                <div class="sk-circle9 sk-circle"></div>
                <div class="sk-circle10 sk-circle"></div>
                <div class="sk-circle11 sk-circle"></div>
                <div class="sk-circle12 sk-circle"></div>
            </div>
            <p>加载中请稍后</p>
        </div>
    </div>
    <div id="go_top" style="display: none;"><img src="{{asset('osce/wechat/common/img/go_top.png')}}"/> </div>
    {{--作用于设置翻页的全局变量--}}
    @show
@show
@section('footer_js')


<!-- 新增 -->
    

    <!-- 自定义js -->
    <script src="{{asset('osce/admin/plugins/js/hplus.min.js?v=3.0.0')}}"></script>
    <script type="text/javascript" src="{{asset('osce/admin/plugins/js/contabs.min.js')}}"></script>
    <!-- 第三方插件 -->
    <script src="{{asset('osce/admin/plugins/js/plugins/pace/pace.min.js')}}"></script>
@show{{-- footer区域javscript脚本 --}}

{{-- 引入额外依赖JS插件 --}}
<script type="text/javascript">
    $(document).ready(function(){
        <!--highlight main-sidebar-->
        @section('filledScript')
        @show{{-- 在document ready 里面填充一些JS代码 --}}
    });

</script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('jk/js/jk.js') }}" type="text/javascript"></script>
@section('extraSection')
@show{{-- 补充额外的一些东东，不一定是JS，可能是HTML --}}
@stop