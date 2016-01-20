@extends('msc::admin.layouts.base')
@section('meta')
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
    <link href="{{asset('msc/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/plugins/css/font-awesome.min.css?v=4.3.0')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/plugins/css/animate.min.css')}}" rel="stylesheet">
    <link href="{{asset('msc/admin/plugins/css/style.min.css?v=3.0.0')}}" rel="stylesheet">
	<link href="{{asset('msc/common/css/bootstrapValidator.css')}}" rel="stylesheet">

@stop

@section('head_js')
    <script src="{{asset('msc/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/jquery-ui-1.10.4.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/bootstrap.min.js?v=3.4.0')}}"></script>
    <script src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script>


@stop
@section('head_style')
    <style type="text/css">
  
    </style>
@show
@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop

@section('body')
    @include('msc::admin.layouts.admin_errors_notice')
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
    <link href="{{asset('msc/admin/css/common.css')}}" rel="stylesheet">
        <!-- 全局js -->

    <script src="{{asset('msc/admin/plugins/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>

    <!-- 自定义js -->
    <script src="{{asset('msc/admin/plugins/js/hplus.min.js?v=3.0.0')}}"></script>
    <script type="text/javascript" src="{{asset('msc/admin/plugins/js/contabs.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/content.min.js')}}"></script>
    <!-- 第三方插件 -->
    <script src="{{asset('msc/admin/plugins/js/plugins/pace/pace.min.js')}}"></script>

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