@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
        .box{
            position: relative;
            height: 100%;
        }
        i{
            font-size: 24px;
            color:#a6b0c3;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -16px;
            margin-left: -175px;
        }
    </style>

@stop
@section('only_js')
@stop
@section('content')
    <div class="middle-box text-center animated fadeInDown" style="margin-top: 86px;">
        <h1>404</h1>
        <h3 class="font-bold">页面未找到！</h3>

        <div class="error-desc">
            抱歉，页面好像去火星了~
        </div>
    </div>
@stop
