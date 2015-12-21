@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link rel="stylesheet" href="{{asset('msc/common/css/bootstrapValidator.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/personalcenter/css/phone_change.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')
    <script type="text/javascript" src="{{asset('msc/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('msc/wechat/personalcenter/js/personalinfo.js')}}"></script>
    <script src="{{asset('msc/wechat/user/js/commons.js')}}"></script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
     更换关联手机
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>

</div>
<div class="w_94 phone_icon mart_15">
    <img src="{{asset('msc/wechat/personalcenter/img/phone.png')}}"/>
    <p>您现在的手机号：<span class="this_phone">{{ $user['mobile'] }}</span></p>
</div>
<input type="hidden" id="parameter" value="{'pagename':'phone_change'}" />
<form id="info_list" name="info_list" method="post" action="{{ url('msc/wechat/personal-center/save-phone') }}" class="mart_5" >
    <div class="add_main">
        <div class="form-group">
            <input type="number" id="mobile"  class="form-control" name="mobile" placeholder="请输入新的手机号码"/>
        </div>
        <div class="submit_box">
            <input type="button" class="form-control ipt_huoqu" id="getVerificationButtonOne"   value="获取验证码"/>
            <input type="hidden" name="yz_num" value="0">
        </div>
        <div class="form-group">
            <input type="text" class="form-control ipt_code" id="VerificationText" placeholder="请输入验证码"/>
        </div>

    </div>
    <div class=" btn-submit">
        <input type="hidden" name="id" value="{{ $user['id'] }}">
        <input  id="change_submit" class="btn"  type="submit" value="确认修改密码" />
    </div>
</form>

@stop