@extends('msc::wechat.layouts.base')
@section('meta')
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
@stop
@section('head_css')
    <link href="{{asset('msc/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
    <link href="{{asset('msc/wechat/common/css/font-awesome/css/font-awesome.css')}}" rel="stylesheet"/>
	<link href="{{asset('msc/wechat/common/css/commons.css')}}"  rel="stylesheet"/>
	<link href="{{asset('msc/wechat/user/css/commons.css')}}"  rel="stylesheet"/>
@stop

@section('head_js')
	<!-- 全局js -->
    <script src="{{asset('msc/admin/plugins/js/jquery-2.1.1.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/bootstrap.min.js?v=3.4.0')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
    <script src="{{asset('msc/wechat/validate/jquery.validate.min.js')}}"></script>

@stop
@section('head_style')
    <style type="text/css">
        .table-head-style1{ background: #434a54;margin: 0; border-radius: 6px 6px 0 0; padding: 6px 0; }
        .selected-all{ color: #fff; font-size: 14px;;padding: 0 20px;line-height: 30px }
        .input-group-btn{ height: 30px;}
        .test1{ background: #fff;color: #1b7a8b;}
        .table thead th{ font-size: 14px;}
        .btn_pl{height:30px;min-width:80px;line-height: 16px;}
        .opera  span{ padding: 0 5px; cursor: pointer;}

        .table .state1{ color:#408aff;}
        .table .state2{ color:#ed5565;}
        .table .state3{ color:#21b9bb;}

        .modal .modal-dialog{ margin-top:10%;}
        .modal-body textarea{ margin-top: 10px; height: 200px; }

        #time_set input{ float: left; width:238px;}
        #time_set  .time_set{ display: table;  }
        #time_set  .time_set span{padding:0  10px 0 0; }
        #time_set {line-height: 36px; padding-left: 13px; margin-bottom: 0;}

        #time_set p{ margin-bottom: 0;}

        .hr-line-dashed{margin: 0 0 20px 0;}
    </style>
@show
@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop

@section('body')

    @section('content')
    @show{{-- 内容主体区域 --}}

@show

@section('footer_js')


<!-- 新增 -->
    <!-- 自定义js -->
    <script src="{{asset('msc/admin/plugins/js/hplus.min.js?v=3.0.0')}}"></script>
    <script type="text/javascript" src="{{asset('msc/admin/plugins/js/contabs.min.js')}}"></script>
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