@extends('msc::wechat.layouts.user')
@section('only_head_css')
    <link href="{{asset('msc/wechat/user/css/commons.css')}}"  rel="stylesheet"/>

@stop


@section('content')
<div class="user_header">用户登录</div>
<div class="login_box" >
    <form name="form" method="post" id="loginForm" action="{{ url('/msc/wechat/user/user-login-op') }}">
        <div class="form-group">
            <input type="text" class="form-control ipt ipt_id" id="username" name="username"  placeholder="请输入你的学号/工号"/>
        </div>
        <div class="form-group">
            <input type="password" class="form-control ipt ipt_pwd" id="password" name="password" placeholder="请输入你的初始密码"/>
        </div>
        <input type="hidden" name="grant_type" id="grant_type" value="password">
        <input type="hidden" name="client_id"  id="client_id" value="ios">
        <input type="hidden" name="client_secret" id="client_secret" value="111">
        <input class="btn submit_btn" type="submit" id="LoginButton"  value="登录绑定" />
        <a class="btn nou zhuce_btn" href="{{ url('/msc/wechat/user/user-register') }}">注册</a>
    </form>
</div>
@stop