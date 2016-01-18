@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/discussion.css')}}" type="text/css" />
@stop
@section('only_head_js')
<script type="text/javascript">
	$(function(){
		/**
         * 翻页
         * @author mao
         * @version 1.0
         * @date    2016-01-18
         */
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
        var url = "{{route('osce.wechat.getCheckQuestions')}}";
        //内容初始化
        $('.history-list').empty();
        getItem(now_page,url);

        /**
         * 分页的ajax请求
         * @author mao
         * @version 1.0
         * @date    2016-01-18
         * @param   {string}   current 当前页
         * @param   {string}   url     请求地址
         */
        function getItem(current,url){
            $.ajax({
                type:'get',
                url:url,
                aysnc:true,
                data:{id:current,pagesize:current},
                success:function(res){
                    totalpages = res.total;
                    var html = '';
                    var index = (current - 1)*10;
                    data = res.data.rows;
                    for(var i in data){
                        //准备dom
                        //计数
                        var key = (index+1+parseInt(i))
                        html += '<li>'+
                                    '<div class="content-header">'+
                                        '<div class="content-l">'+
                                            '<span>'+key+'F</span>.'+
                                            '<span class="student">'+data[i].name.name+'</span>.'+
                                            '<span class="time">'+data[i].time+'</span>'+
                                        '</div>'+
                                        '<div class="clearfix"></div>'+
                                    '</div>'+
                                    '<p>'+data[i].content+'</p>'+
                                '</li>';

                    }
                    //插入
                    $('.history-list').append(html);
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
       	讨论区
        <a class="right header_btn nou clof header_a" href="{{ route('osce.wechat.getAddQuestion')  }}">提问</a>
    </div>
    <ul id="discussion_ul">
		@foreach($list as $list)
        <li>
        	<a class="nou" href="{{ route('osce.wechat.getCheckQuestion',array('id'=>$list['id']))  }}">
        		<p class="font14 fontb clo3 p_title">{{  $list['title']  }}</p>
        		<p class="font12 clo9 main_txt">{{  $list['content']  }}</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">{{ $list['name']->name  }}</span>
        			<span class="clo0">·</span>
        			<span class="clo9">{{ $list['time']  }}</span>
        			<span class="right comment"><img src="{{asset('osce/wechat/common/img/pinglun.png')}}" height="16"/>&nbsp;{{ $list['count']  }}</span>
        		</p>
        	</a>
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