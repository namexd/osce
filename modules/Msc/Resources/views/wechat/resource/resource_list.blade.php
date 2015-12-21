@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/resourcemanage/reourcemanage.css')}}" rel="stylesheet" type="text/css" />
<style>
    .detail_list .name img{width: 55%;float: left;}
    .detail_list .name span{width: 45%;float: left;text-align: left;}
</style>
@stop

@section('only_head_js')

    <script src="{{asset('msc/wechat/resourcemanage/js/resource_list.js')}}"></script>
    <script>
        /*顶部滑动*/
        var myScroll;
        function loaded() {
            myScroll = new iScroll('wrapper', {
                momentum: true,
                hScrollbar: false,
                vScroll:false,
                vScrollbar:false,
                fadeScrollbar:false,
            });
        }
        $(document).ready(function() {
            loaded();//初始化
            //document.addEventListener('DOMContentLoaded', loaded, false);
            //初始化列表
            var url="{{ url('/msc/wechat/resource/resource-paginate') }}"
            var now_page="1";
            ajaxRequest({type:$('#thelist').find('.check').attr('value'),keyword:'',page:1},url)
            $(window).scroll(function(e){

                if(away_top >= (page_height - window_height)&&now_page<totalpages){
                    now_page++;
                    ajaxRequest({type:$('#thelist').find('.check').attr('value'),keyword:'',page:now_page},url)
                    /*加载显示*/
                }

            })
            /*类目点击*/
            $('#thelist').on('click','li',function(){

                //重置当前页
                now_page= 1;

                var thisElement = $(this);
                $('#thelist').find('li').removeClass('check');
                thisElement.addClass('check');
                $('#search').val('');
                //分类标志
                var selectType = thisElement.attr('value');
                var req = {type:selectType,keyword:'',page:1};
                //数据请求结果
                ajaxReset(req,url);
            });

            /*搜索*/
            $('#search').on('keydown',function(){
                var thisElement = $(this);
                var selectType = $('#thelist').find('.check').attr('value');
                var req = {type:selectType,keyword:$('#search').val(),page:1};
                //数据请求结果
                ajaxRequest(req,url);
            })

            /*跳转到详情页*/
            $('.detail_list').find('ul').on('click','li',function(){
                location.href = 'http://www.mis.hx/msc/wechat/resource/resource?id='+$(this).attr('id')+'&type='+$(this).attr('type');
                //location.href = "{{ url('/msc/wechat/resource/resource-paginate') }}?id="+$(this).attr('id')+"&type="+$(this).attr('type');
            });
        });


    </script>

@stop

@section('content')

<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
   现有资源
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>

</div>
<div id="wrapper" style="height: 35px;">
    <div id="scroller">
        <ul id="thelist">
            @foreach($options as $k => $list)
              @if($k == "TOOLS")
                <li class="check" value="{{ $k }}">{{$list}}</li>
              @else
                <li value="{{ $k }}">{{$list}}</li>
              @endif
            @endforeach
        </ul>
    </div>
</div>

<div>


    <div class="container">
        <div class="row clearfix">
            <div class="col-md-12 column">
                <div class="input-group search_button">
                    <input type="text" placeholder="搜索" class="form-control"  id="search">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-white2"><i class="fa fa-search"></i></button>
                </span>
                </div>
            </div>
        </div>
    </div>

<div class="main_list">
    <div class="title_nav">
        <div class="name title">名称</div>
        <div class="number title">编号</div>
    </div>
    <div class="detail_list">
        <div class="search_none" style="display:none">
            <span class="btn_search"><i class="fa fa-search"></i>
            </span>
        </div>
        <ul>
            <!-- <li id="2" type="TOOLS">
                 <div class="name">
                     <img src="public/information/img/test_images.gif">
                     <span>开放实验室</span>
                 </div>
            </li>
            <li>
                <div class="name">
                    <img src="public/information/img/test_images.gif">
                    <span>开放实验室</span>
                </div>
            </li> -->

        </ul>
    </div>
</div>

</div>

@stop