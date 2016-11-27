@extends('osce::wechat.layouts.admin')

@section('only_head_css')
    <style type="text/css">
        .title{
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            word-wrap: break-word;
        }
        .content-box{
            padding: 20px;
            background: #fff;
            text-align: left;
        }
        .history-list{margin-top: 10px}
        .history-list li{
            background-color: #fff;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .title-con p{word-wrap: break-word;}
        /*header*/
        .item-l,.item-c,.item-r{
            float: left;
            color: #cccccc;
            margin: 2px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .item-l{width: 50%;color:#42b2b1;}
        .item-c{width: 45%}
        .item-r{width: 5%}
        .title-con{padding-top: 10px;}

        /*content*/
        .content-header{margin-bottom: 10px;}
        .content-l{
            font-size: 12px;
            width: 60%;
            float: left;
        }
        .content-l span{margin: 0 3px;}
        .content-l span:first-child{margin-left: 0;}
        .content-l .student{color: #42b2b1;}
        .content-l .time{color: #cccccc;}
        .content-r{
            width: 40%;
            float: left;
        }
        .option{
            z-index: 10;
            position: absolute;
            background: #e7eaed;
            top: 45px;
            right: 0;
            display: none;
            padding:10 0;
        }
        .option li{
            margin: 0;
            height: 36px;
            width: 90px;
            line-height: 36px;
            text-align: center;
        }
        .btn.btn-default{
            background: #fff;
            border: 1px solid #ccc!important;
            color: #333!important;
        }
        .btn.btn-default:first-child{
            background: #1ab394;
            color: #fff!important;
        }
    </style>
@stop
@section('only_head_js')
<script src="{{asset('osce/wechat/discussion/discussion.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'discussion_detail','del':'{{route('osce.wechat.getDelQuestion')}}','URL':'{{route('osce.wechat.getDiscussionLists')}}','toPage':'{{route('osce.wechat.getCheckQuestions')}}'}" />
    <ul class="option">
        @if($url==1)
        <li><a href="{{route('osce.wechat.postAddReply',array('id'=>$row['question']['id']))}}">回复</a></li>
        @elseif($url==2)
            <li><a href="{{route('osce.wechat.postAddReply',array('id'=>$row['question']['id']))}}">回复</a></li>
            <li><a href="{{route('osce.wechat.postEditQuestion',array('id'=>$row['question']['id']))}}">编辑</a></li>
            <li><a href="javascript:void(0)" url="{{route('osce.wechat.getDelQuestion') }}" id="del">删除</a></li>
        @endif
    </ul>
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.getDiscussionLists')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       查看
        <a class="right header_btn" href="javascript:void(0)">
            <i class="fa fa-ellipsis-h clof font26 icon_return"></i>
        </a>
    </div>
    <div class="content-box">
        <div>
            <h2 class="title">{{  $row['question']['title'] }}</h2>
            <div class="title-footer">
                <div class="item-l">{{  $row['question']['name']->name }}</div>
                <div class="item-c" style="width:50%;text-align: right;">{{  $row['question']['create_at'] }}</div>
                <div class="clearfix"></div>
            </div>
            <div class="title-con">
                <p>{{  $row['question']['content'] }}</p>
            </div>
        </div>
    </div>
    <ul class="history-list">
        @foreach($data as $data)
        <li>
            <div class="content-header">
                <div class="content-l">
                    <span>1楼</span>.
                    <span class="student">{{  $data['name']->name }}</span>.
                    <span class="time">{{  $data['time'] }}</span>
                </div>
                <div class="clearfix"></div>
            </div>
            <p>{{  $data['content'] }}</p>
        </li>
           @endforeach
    </ul>
   
@stop