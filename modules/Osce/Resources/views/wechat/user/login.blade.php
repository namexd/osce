@extends('osce::wechat.layouts.user')
@section('only_head_css')
    <link href="{{asset('msc/wechat/user/css/commons.css')}}"  rel="stylesheet"/>
    <style>
        .forget-psw{
            margin-top: 20px;

        }
        .user_header,.submit_btn,.register{background: #1ab394!important;}
    </style>
@stop


@section('content')
    <div class="user_header">用户登录</div>
    <div class="login_box" >
        <form name="form" method="post" id="loginForm" action="{{route('osce.wechat.user.postLogin')}}">
            <div class="form-group">
                <input type="text" class="form-control ipt ipt_id" id="username" name="username"  placeholder=""/>
            </div>
            <div class="form-group">
                <input type="password" class="form-control ipt ipt_pwd" id="password" name="password" placeholder=""/>
            </div>
           {{-- <input type="hidden" name="grant_type" id="grant_type" value="password">
            <input type="hidden" name="client_id"  id="client_id" value="ios">
            <input type="hidden" name="client_secret" id="client_secret" value="111">--}}
            <input class="btn submit_btn" type="submit" id="LoginButton"  value="登录" />
            <a class="btn right register" style="margin-right:1%;" href="{{route('osce.wechat.user.postRegister')}}">注册</a>

        </form>
        <div  class="forget-psw">
            <a href="{{route('osce.wechat.user.getForgetPassword')}}">忘记密码?</a>
        </div>
    </div>
    <script>
@stop
