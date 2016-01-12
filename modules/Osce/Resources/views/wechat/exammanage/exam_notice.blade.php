@extends('osce::wechat.layouts.admin')

@section('only_head_css')
    <link rel="stylesheet" href="{{asset('osce/wechat/personalcenter/css/documentation.css')}}" type="text/css" />
    <link rel="stylesheet" href="{{asset('osce/wechat/personalcenter/css/jalendar.css')}}" type="text/css" />
    <style type="text/css">
        .title{
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .history-box{
            text-align: left;
        }
        .history-list li{
            background-color: #fff;
            padding: 15px;
            margin: 10px;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .year{
            margin-right: 20px;
        }
        .time{
            color: #999;
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
        资讯&通知
        <a class="right header_btn" href="">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="history-box">
        <ul class="history-list">
            @foreach($list as $item)
            <li>
                <p class="title">{{ $item->name  }}</p>
                <p class="time"><span class="year">{{  $item->created_at }}</span></p>
            </li>
            @endforeach
        </ul>
    </div>
@stop