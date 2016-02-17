@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/train.css')}}" type="text/css" />
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
        var url = "{{route('osce.wechat.getTrainList')}}";
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

                    for(var i in data){
                        //准备dom
                        //计数
                        var key = (index+1+parseInt(i))
                        var author  =   data[i].author;
                        var num=data[i].clicks;
                        if(num==""){
                            num=0;
                        }
                        if(author==null)
                        {
                            continue;
                        }
                        html += '<li>'+
						        	'<a class="nou" href="{{route('osce.wechat.getTrainDetail')}}?id='+data[i].id+'">'+
						        		'<p class="font14 fontb clo3 p_title">'+data[i].name+'</p>'+
						        		'<p class="font12 clo9 main_txt">'+data[i].address+'</p>'+
						        		'<p class="font12 clo9 main_txt">'+data[i].begin_dt+'~'+data[i].end_dt+'</p>'+
						        		'<p class="font12 p_bottom">'+
						        			'<span class="font14 student_name">'+data[i].author.name+'</span>'+
						        			'<span class="clo9">&nbsp;'+data[i].time+'</span>'+
						        			'<span class="right comment">已读&nbsp;'+num+'</span>'+
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
       	考前培训
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <ul id="discussion_ul">
    </ul>
@stop