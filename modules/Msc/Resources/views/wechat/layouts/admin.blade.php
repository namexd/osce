@extends('msc::wechat.layouts.base')
@section('meta')
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
    <link href="{{asset('msc/admin/plugins/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('msc/wechat/common/css/font-awesome/css/font-awesome.css')}}" rel="stylesheet"/>
	<link href="{{asset('msc/wechat/common/css/commons.css')}}"  rel="stylesheet"/>
    <link href="{{asset('msc/wechat/common/css/table_commons.css')}}"  rel="stylesheet"/>
    <link href="{{asset('msc/common/css/bootstrapValidator.css')}}" rel="stylesheet">
    <link href="{{asset('msc/wechat/jquery-confirm/jquery-confirm.css')}}" rel="stylesheet">
@stop

@section('head_js')
	<!-- 全局js -->
    <script src="{{asset('msc/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('msc/wechat/common/js/commons.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script><!--虚拟滚动条插件-->
    <script src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script><!--表单验证插件-->

    <script src="{{asset('msc/wechat/jquery-confirm/jquery-confirm.js')}}"></script>
    <script type="text/javascript">
        var totalpages="0";
        var　page_height = "0";
        var away_top= "0";
        var window_height= "0";
        var nextday;
        $(document).ready(function(){
            nextday = getFormattedDate(); //获取当前日期
        });
        function getFormattedDate(date) { //获取当前日期
            var date=new Date();
            var year = date.getFullYear();
            var month = (1 + date.getMonth()).toString();
            month = month.length > 1 ? month : '0' + month;
            var day = (date.getDate()+1).toString();
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
            <img src="{{asset('msc/wechat/common/img/loading.gif')}}"/>
            <p>加载中请稍后</p>
        </div>
    </div>
    <div id="go_top" style="display: none;"><img src="{{asset('msc/wechat/common/img/go_top.png')}}"/> </div>
    {{--作用于设置翻页的全局变量--}}
    @show
@show
@section('footer_js')


<!-- 新增 -->


@show{{-- footer区域javscript脚本 --}}

{{-- 引入额外依赖JS插件 --}}
<script type="text/javascript">
    $(document).ready(function(){
        <!--highlight main-sidebar-->
        @section('filledScript')
        @show{{-- 在document ready 里面填充一些JS代码 --}}
    });

</script>

@section('extraSection')
@show{{-- 补充额外的一些东东，不一定是JS，可能是HTML --}}
@stop