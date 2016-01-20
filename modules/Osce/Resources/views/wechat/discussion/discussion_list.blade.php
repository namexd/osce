@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('osce/wechat/css/discussion.css')}}" type="text/css" />
@stop
@section('only_head_js')
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
        var url = "{{route('osce.wechat.getQuestionList')}}";
        //内容初始化
        $('.history-list').empty();
        getItem(now_page,url);

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
                  	console.log(data);
                    for(var i in data){
                        //准备dom
                        //计数
                        var key = (index+1+parseInt(i))
                        if(data[i].user==null)
                        {
                            var ThisName    ='-';
                        }
                        else
                        {
                            var ThisName    =   data[i].user.name;
                        }
                        html += '<li>'+
						        	'<a class="nou" href="{{route('osce.wechat.getCheckQuestion')}}?id='+data[i].id+'">'+
						        		'<p class="font14 fontb clo3 p_title">'+data[i].title+'</p>'+
						        		'<p class="font12 clo9 main_txt">'+data[i].content+'</p>'+
						        		'<p class="font12 p_bottom">'+
						        			'<span class="student_name">'+ThisName+'</span>'+
						        			'<span class="clo0">&nbsp;·&nbsp;</span>'+
						        			'<span class="clo9">'+data[i].time+'</span>'+
						        			'<span class="right comment"><img src="{{asset('osce/wechat/common/img/pinglun.png')}}" height="16"/>&nbsp;'+data[i].count+'&nbsp;</span>'+
						        		'</p>'+
						        	'</a>'+
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
       	讨论区
        <a class="right header_btn nou clof header_a" href="{{ route('osce.wechat.getAddQuestion') }}">提问</a>
    </div>
    <ul id="discussion_ul">
		
    </ul>
@stop