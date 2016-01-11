@extends('msc::wechat.layouts.admin')
@section('only_head_css')
    <link href="{{asset('msc/wechat/user/css/commons.css')}}"  rel="stylesheet"/>
    <style>
        .left{
            width: 20%;
        }
        .right{
            width: 80%;
        }
    </style>
@stop
@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        忘记密码
        <a class="right header_btn" href="{{}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="text-box">
        <div>
            <div class="pull-left left">
                <span>手机号</span>
            </div>
            <div class="pull-left right">
                <input type="text" class="form-control">
            </div>
        </div>
        <div>
            <div class="pull-left left">
                <input type="text" class="form-control">
            </div>
            <div class="pull-left right">
                <button class="btn btn-default">发送手机验证码</button>
            </div>
        </div>
    </div>
@stop