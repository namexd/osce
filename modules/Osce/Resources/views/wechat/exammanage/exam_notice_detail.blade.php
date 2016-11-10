@extends('osce::wechat.layouts.admin')

@section('only_head_css')
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
        .notice-box{
            word-wrap:break-word;
        }
        img{
            width: 100%;
        }
        .notice-file a{
            display: inherit;
            overflow: hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }
    </style>


@stop
@section('only_head_js')

@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.notice.getSystemList')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        资讯&通知
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="history-box">
        <ul class="history-list">
            <li>
                <p class="title">{{  $notice->name }}</p>
                <p class="time"><span class="year"> {{ $notice->created_at }}</span></p>
                <div>
                    <div>
                        <img src="" alt="">
                    </div>
                    <div class="notice-box">
                      {!! $notice->content !!}
                    </div>

                    <br />附件<br />
                    <div class="notice-file">
                        @if($notice->attachments)
                            @foreach($notice->attachments as $key=>$list)
                                <a href="{{ route('osce.wechat.notice.getDownloadDocument',['id'=>$notice->id,'attch_index'=>$key])}}">
                                    <?php $pathInfo = explode('/',$list) ?>
                                        {{array_pop($pathInfo)}}
                                </a><br />
                            @endforeach
                        @endif
                    </div>
                </div>
            </li>
        </ul>
    </div>
@stop