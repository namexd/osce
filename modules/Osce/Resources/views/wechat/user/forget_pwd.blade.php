@extends('msc::wechat.layouts.admin')
@section('only_head_css')
    <link href="{{asset('msc/wechat/user/css/commons.css')}}"  rel="stylesheet"/>
    <style>
        .btn{
            margin-top: 0px;
            margin-bottom: 10px;
        }
        .mobile-box{
            margin-top: 10px;
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
        <div class="form-group mobile-box">
            <label for="mobile">手机号码<span>*</span></label>
            <input type="number" class="form-control" id="mobile" name="mobile" />
        </div>
        <div>
            <div class="pull-left left">
                <input type="text" class="form-control">
            </div>
            <div class="pull-left right">
                <button class="btn btn-default" id="btn">发送手机验证码</button>
            </div>
        </div>
        <div class="form-group">
            <input type="text" name="password" class="form-control ipt_txt" placeholder="请输入验证码"/>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control ipt_txt" placeholder="请输入原密码"/>
        </div>
        <div class="form-group">
            <input type="password" name="repassword" class="form-control ipt_txt" placeholder="请输入新密码"/>
        </div>
        <input class="btn" type="submit"  value="提交审核" />
    </div>
@stop