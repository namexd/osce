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
        var url = "{{route('osce.admin.getTrainList')}}";
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
                    //                  totalpages = res.total;
//                  var html = '';
//                  var index = (current - 1)*10;
//                  data = res.data.rows;
//                  for(var i in data){
//                      //准备dom
//                      //计数
//                      var key = (index+1+parseInt(i))
//                      html += '<li>'+
//                                  '<div class="content-header">'+
//                                      '<div class="content-l">'+
//                                          '<span>'+key+'F</span>.'+
//                                          '<span class="student">'+data[i].name.name+'</span>.'+
//                                          '<span class="time">'+data[i].time+'</span>'+
//                                      '</div>'+
//                                      '<div class="clearfix"></div>'+
//                                  '</div>'+
//                                  '<p>'+data[i].content+'</p>'+
//                              '</li>';
//
//                  }
//                  //插入
//                  $('.history-list').append(html);
					console.log("请求成功");
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
       	<a class="right header_btn nou clof header_a" href="#"></a>
    </div>
    <ul id="discussion_ul">
		@foreach($data as $data)

    	<li>
        	<a class="nou" href="{{ route('osce.wechat.getTrainDetail',array('id'=>$data['id']))  }}">
        		<p class="font14 fontb clo3 p_title">{{  $data['name'] }}</p>
        		<p class="font12 clo9 main_txt">{{  $data['address'] }}</p>
        		<p class="font12 clo9 main_txt">{{  $data['begin_dt'] }} ~ {{  $data['end_dt'] }}</p>
        		<p class="font12 p_bottom">
        			<span class="student_name">{{ $data['author']->name }}</span>
        			<span class="clo9">{{  $data['time'] }}</span>
        			<span class="right comment">已读&nbsp;100</span>
        		</p>
        	</a>
        </li>
		@endforeach
    </ul>
	<div class="">
		<div class="pull-left">
			共{{$pagination->total()}}条
		</div>
		<div class="pull-right">
			<nav>
				<ul class="pagination">
					{!! $pagination->render() !!}
					<li>1</li>
					<li>2</li>
					<li>3</li>
				</ul>
			</nav>
		</div>
	</div>
@stop