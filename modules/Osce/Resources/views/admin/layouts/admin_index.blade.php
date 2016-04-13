@extends('osce::admin.layouts.base')
@section('meta')
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
    <link href="{{asset('osce/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/font-awesome.min.css?v=4.3.0')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/style.min.css?v=3.0.0')}}" rel="stylesheet">
    <link href="{{asset('osce/wechat/jquery-confirm/jquery-confirm.css')}}" rel="stylesheet">
    <link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
@stop

@section('head_js')


{{--D:\wamp\www\hx.mis_api.local\mis-msc\public\osce\common\js\language\zh_CN.js--}}
    <script src="{{asset('osce/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/jquery-ui-1.10.4.min.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/bootstrap.min.js?v=3.4.0')}}"></script>
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('osce/common/js/language/zh_CN.js')}}"></script>
    <script type="text/javascript" src="{{asset('osce/common/js/layer/layer.js')}}"> </script>
    <!-- 自定义js -->
    <script src="{{asset('osce/wechat/jquery-confirm/jquery-confirm.js')}}"></script>
@stop
@section('head_style')
    <style type="text/css">
        body{
            font-family: 微软雅黑;
            font-size: 14px;
        }
        .gray-bg {
            background-color: #f3f7f8;
        }
        .nav-tabs>li.active>a{border-bottom: 1px solid #fff!important}
    </style>
@show
@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop

@section('body')
    @include('osce::admin.layouts.admin_errors_notice')
@section('content')
@show{{-- 内容主体区域 --}}

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            @section('layer_content')
            @show{{-- 内容主体区域 --}}
        </div>
    </div>
</div>

@show

@section('footer_js')
        <!--全局Css自定义部分-->
<link href="{{asset('osce/admin/css/common.css')}}" rel="stylesheet">
<!-- 全局js -->

<script src="{{asset('osce/admin/plugins/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script><!--虚拟滚动条插件未使用-->
<script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>

<!-- 自定义js -->
<script src="{{asset('osce/admin/plugins/js/hplus.min.js?v=3.0.0')}}"></script>
<script type="text/javascript" src="{{asset('osce/admin/plugins/js/contabs.min.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/content.min.js')}}"></script>
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