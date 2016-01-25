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
        $(function(){
            $(window).scroll(function(e){
                if(away_top >= (page_height - window_height)&&now_page<totalpages){
                    now_page++;
                    //qj.page=now_page;//设置页码

                    getItem(now_page,url)
                    /*加载显示*/
                }
            });
            //初始化
            var now_page = 1;
            var url = "{{route('osce.wechat.notice.getSystemView')}}";
            //内容初始化
            $('.history-list').empty();
            getItem(now_page,url);

            function getItem(current,url){

                $.ajax({

                    type:'get',
                    url:url,
                    aysnc:true,
                    data:{id:current,page:current},
                    success:function(res){

                        console.log(res);
                        totalpages = Math.ceil(res.data.total/res.data.pagesize);

                        var html = '';
                        var index = (current - 1)*10;
                        data = res.data.rows;

                        for(var i in data){
                            //准备dom
                            //计数
                            var key = (index+1+parseInt(i))

                            html +='<li>'+
                                        '<p class="title">'+data[i].name+'</p>'+
                                        '<p class="time"><span class="year">'+data[i].created_at+'</span>'+
                                            '<a style="color:#1ab394;" class="right" href="{{route('osce.wechat.notice.getView')}}?id='+data[i].id+'">查看详情&nbsp;&gt;</a>'+
                                        '</p>'+
                                    '</li>';
                        }
                        //插入
                        $('#discussion_ul').append(html);
                    }
                });

            }
        })
    </script>


@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
        资讯&通知
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="history-box">
        <ul id="discussion_ul" class="history-list">

        </ul>
    </div>

@stop