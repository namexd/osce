<!DOCTYPE html>
<html>
	<head>
	    <meta charset="UTF-8">
	    <title>@section('title') OSCE管理系统 @show{{-- 页面标题 --}}</title>
	    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	    <meta name="description" content="OSCE管理系统" />
	    <meta name="keywords" content="OSCE管理系统" />
	    <meta name="author" content="OSCE管理系统" />
	    <meta name="renderer" content="webkit">{{-- 360浏览器使用webkit内核渲染页面 --}}
	    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />{{-- IE(内核)浏览器优先使用高版本内核 --}}
	    <meta name="csrf-token" content="{{ csrf_token() }}" />
	    @section('meta')
	    @show{{-- 添加一些额外的META申明 --}}
	    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">{{-- favicon --}}
	    
	    <link rel="stylesheet" href="{{ asset('osce/theory/css/bootstrap.min.css') }}" />
	    <link rel="stylesheet" href="{{ asset('osce/theory/css/animate.min.css') }}" />
	    <link rel="stylesheet" href="{{ asset('osce/theory/css/style.min.css') }}" />
	    <link href="{{asset('osce/admin/plugins/css/font-awesome.min.css?v=4.3.0')}}" rel="stylesheet">
	    	
	    <link href="{{asset('osce/admin/css/common.css')}}" rel="stylesheet">
	    <!--<link rel="stylesheet" href="{{ asset('osce/theory/css/common.css') }}" />-->
		
		<script src="{{ asset('osce/theory/js/jquery-1.12.0.min.js') }}"></script>
		<script src="{{ asset('osce/theory/js/bootstrap.min.js') }}"></script>
		<script src="{{ asset('osce/theory/js/layer/layer.min.js') }}"></script>
		<script src="{{ asset('osce/theory/js/base.js') }}"></script>
		<script src="{{asset('osce/admin/plugins/js/plugins/pace/pace.min.js')}}"></script>

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
	    @section('head_css')
	    @show{{-- head区域css样式表 --}}
	
	    @section('head_js')
	    @show{{-- head区域javscript脚本 --}}
		
		@include('osce::theory.notice')
	   
	</head>
	<body @section('body_attr')class=""@show{{-- 追加类属性 --}}>
	
	@section('body')
	@show{{-- 正文部分 --}}
	
	</body>
</html>