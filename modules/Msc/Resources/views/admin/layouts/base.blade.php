<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>@section('title') 敏行综合管理系统 @show{{-- 页面标题 --}}</title>
    <meta name="description" content="MISROBOT" />
    <meta name="keywords" content="MISROBOT" />
    <meta name="author" content="敏行综合管理系统" />
    <meta name="renderer" content="webkit">{{-- 360浏览器使用webkit内核渲染页面 --}}
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />{{-- IE(内核)浏览器优先使用高版本内核 --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @section('meta')
    @show{{-- 添加一些额外的META申明 --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">{{-- favicon --}}

    @section('head_css')
    @show{{-- head区域css样式表 --}}

    @section('head_js')
    @show{{-- head区域javscript脚本 --}}

    @section('beforeStyle')
    @show{{-- 在内联样式之前填充一些东西 --}}

    @section('head_style')
    @show{{-- head区域内联css样式表 --}}

    @section('afterStyle')
    @show{{-- 在内联样式之后填充一些东西 --}}

    @section('only_css')
    @show{{-- head区域css样式表 --}}

    @section('only_js')
    @show{{-- head区域javscript脚本 --}}

</head>
<body @section('body_attr')class=""@show{{-- 追加类属性 --}}>

@section('beforeBody')
@show{{--在正文之后填充一些东西 --}}

@section('body')
@show{{-- 正文部分 --}}


@section('footer_css')
@show{{-- footer区域css样式--}}


@section('footer_js')
@show{{-- footer区域javscript脚本 --}}


@section('afterBody')
@show{{-- 在正文之后填充一些东西，比如统计代码之类的东东 --}}

</body>
</html>