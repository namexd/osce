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
        $.(function(){
            $('.check').click(function(){
                alert(1111);
            })


        })
      
    </script>

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       考试邀请
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="history-box ">
        <ul class="history-list">
            @forelse($list as $data)
            <li>
                <a class="title check" href="{{route('osce.wechat.invitation.getMsg',['id'=>$data->id])}}"> {{$data->name}}</a>
                {{--<p class="title check">{{$data->name}}</p>--}}
                <p class="clearfix time"><span class="year">{{date('Y-m-d',strtotime($data->created_at))}}</span><span>{{date('H-i-s',strtotime($data->created_at))}}</span>
                    <span class="right clo6">@if($status->status == 0)还未处理 @elseif($status->status==1)已同意@elseif($status->status==2)已拒绝@endif</span></p>
            </li>
            @empty
            <li>
                <p class="title">你暂时没有考试邀请</p>
            </li>
            @endforelse
        </ul>
    </div>
@stop