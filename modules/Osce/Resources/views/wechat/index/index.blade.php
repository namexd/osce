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
<div>
  <ul>
     <li><a href="{{route('osce.wechat.notice.getSystemList')}}">资讯通知</a><li>
     <li><a href="#">成绩查询</a><li>
     <li><a href="{{route('osce.wechat.invitation.getList')}}">预约邀请</a><li>
     <li><a href="{{route('osce.wechat.getDiscussionLists')}}">讨论区</a><li>
     <li><a href="{{route('osce.wechat.getTrainlists')}}">考前培训</a><li>
     <li><a href="{{route('osce.wechat.notice.getSystemList')}}">系统消息</a><li>
  </ul>
</div>
@stop