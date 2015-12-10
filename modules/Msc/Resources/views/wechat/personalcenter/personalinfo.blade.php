@extends('msc::wechat.layouts.admin')

@section('only_head_css')


<link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/personalcenter/css/personalinfo.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')
    <script>
        $(document).ready(function(){
            var  id_code=  $(".zheng_code").text();
            var  id_code_re=id_code.substring(4,14);
            id_code=id_code.replace(id_code_re,"****");
            $(".zheng_code").text(id_code);//保密处理
        });

    </script>

@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
   个人信息
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>
<div class="nav_list">
    <ul class="manage_list">
        <li><a class="ablock nou" href="#"><p>姓名 <span class="username">{{ $user['name'] }}</span></p></a></li>
        <li><a class="ablock nou" href="#"><p>性别<span class="sex">{{ $user['gender'] == 1 ? '男':'女' }}</span></p></a></li>
        <li><a class="ablock nou" href="{{ url('/msc/wechat/personal-center/save-phone') }}"><p>手机号码<i class="fa fa-angle-right i_right"></i><span class="phone_txt">{{ $user['mobile']  }}</span></p></a></li>
        <li><a class="ablock nou" href="#"><p>证件号码<span class="zheng_code">{{ $user['idcard'] }}</span></p></a></li>
    </ul>
    @if(!empty($teacherInfo))
        <ul class="manage_list">
            <li><a class="ablock nou" href="#"><p>学号/胸牌号<span class="code">{{ @$teacherInfo['code'] }}</span></p></a></li>
            <li><a class="ablock nou" href="#"><p>专业<span class="professional">{{ @$teacherInfo['dept']['name'] }}</span></p></a></li>
        </ul>
    @endif
    @if(!empty($studentInfo))
        <ul class="manage_list">
            <li><a class="ablock nou" href="#"><p>学号/胸牌号<span class="code">{{ @$studentInfo['code'] }}</span></p></a></li>
            {{--<li><a class="ablock nou" href="#"><p>年级<span class="class"></span></p></a></li>--}}
            <li><a class="ablock nou" href="#"><p>类别<span class="student_type">{{ @$studentInfo['professionalName']['name'] }}</span></p></a></li>
        </ul>
    @endif
</div>
@stop