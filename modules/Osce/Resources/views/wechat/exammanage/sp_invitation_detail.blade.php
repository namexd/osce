@extends('osce::wechat.layouts.admin')

@section('only_head_css')
    <link rel="stylesheet" href="{{asset('osce/wechat/personalcenter/css/documentation.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('osce/wechat/personalcenter/css/jalendar.css')}}" type="text/css" />
    <style type="text/css">
        .detail-list{
           margin:30px;
        }
        .detail-list li{
            font-weight: 700;
            margin-bottom: 10px;
        }
        .operate button{
            width: 45%;
        }
        .operate{
            margin-top: 20px;
        }
        .rejected{
            background-color: #ed5565;
        }
        .items{
            color: #999;
            margin-left: 20px;
           font-weight: inherit!important;
        }
        .agree{
            background-color: #16beb0;
        }
    </style>


@stop
@section('only_head_js')
    <script type="text/javascript" src="{{asset('osce/wechat/personalcenter/js/jalendar.js')}}"></script>
    <script type="text/javascript">

    </script>

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        考试邀请详情
        <a class="right header_btn" href="">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="detail-list">
        <ul>
            <li>
                考试邀请:<span class="items">OSCE考试2015年第3期</span>
            </li>
            <li>
                考试时间:<span class="items">2015-11-20~11-21</span>
            </li>
            <li>
                sp病例:<span class="items">腰酸背痛</span>
            </li>
        </ul>
        <p>希望你能协助考核，如有疑问，请致电：028 - 87653489  张老师</p>
        <div class="operate">
            <button class="btn1 pull-left agree" type="button">同意</button>
            <button class="btn1 pull-right rejected" type="button">拒绝</button>
        </div>
    </div>
@stop