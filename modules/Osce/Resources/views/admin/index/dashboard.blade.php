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
    <div class="box">
        <i>欢迎使用OSCE考试智能管理系统</i>
    </div>
@stop
