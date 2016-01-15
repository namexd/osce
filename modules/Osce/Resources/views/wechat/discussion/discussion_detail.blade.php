@extends('osce::wechat.layouts.admin')

@section('only_head_css')
    <style type="text/css">
        .title{
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
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
        /*header*/
        .item-l,.item-c,.item-r{
            float: left;
            color: #cccccc;
            margin: 2px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .item-l{width: 20%;color:#42b2b1;}
        .item-c{width: 60%}
        .item-r{width: 20%}
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
        }
        .option li{
            margin: 0;
            height: 30px;
            width: 60px;
            line-height: 30px;
            text-align: center;
        }
        .jconfirm.white .jconfirm-box .buttons{
            float: none;
            margin-left: 28%;
        }
        .jconfirm.white .jconfirm-box .buttons .btn:last-child{
            background: #fff;
            border: 1px solid #ccc;
            color: #333!important;
        }
        .btn.btn-default{
            background: #1ab394;
            color: #fff!important;
        }
    </style>
@stop
@section('only_head_js')
    <script>
      $(function(){
        $('.right').click(function(){
            $('.option').show();
        });

        $('.content-box').click(function(){
            $('.option').fadeOut();
        });

        $('.history-list').click(function(){
            $('.option').fadeOut();
        });

        /**
         * 删除操作
         * @author mao
         * @version 1.0
         * @date    2016-01-14
         */
        $('#del').click(function(){
            $this = $(this);
            $.confirm({
                title: '提示!',
                content: '是否删除？',
                confirmButton: '确定',
                cancelButton: '取消',
                confirm: function(){
                    var id = (location.href).split('=')[1];
                    location.href = $this.attr('url')+'?id='+id;
                },
                cancel: function(){
                    $('.option').fadeOut();
                }
            });
        });


      })
    </script>

@stop


@section('content')
    <ul class="option">
        <li><a href="{{route('osce.wechat.postAddReply',array('id'=>$row['question']['id']))}}">回复</a></li>
        <li><a href="{{route('osce.wechat.postEditQuestion',array('id'=>$row['question']['id']))}}">编辑</a></li>
        <li><a href="javascript:void(0)" url="{{route('osce.wechat.getDelQuestion') }}" id="del">删除</a></li>
    </ul>
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       查看
        <a class="right header_btn" href="javascript:void(0)">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="content-box">
        <div>
            <h2 class="title">{{  $row['question']['title'] }}</h2>
            <div class="title-footer">
                <div class="item-l">{{  $row['question']['name']->name }}</div>
                <div class="item-c">{{  $row['question']['create_at'] }}</div>
                <div class="item-r">&nbsp;</div>
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
                    <span>1F</span>.
                    <span class="student">{{  $data['name']->name }}</span>.
                    <span class="time">{{  $data['time'] }}</span>
                </div>
                <div class="clearfix"></div>
            </div>
            <p>{{  $data['content'] }}</p>
        </li>
           @endforeach
    </ul>
    <div class="row">
        <div class="pull-left">
            共{{$pagination->total()}}条
        </div>
        <div class="pull-right">
            <nav>
                <ul class="pagination">
                    {!! $pagination->render() !!}
                </ul>
            </nav>
        </div>
    </div>
@stop