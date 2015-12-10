@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/common/css/swiper.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/resourcemanage/css/returnmanagement.css')}}" rel="stylesheet" type="text/css" />

@stop

@section('only_head_js')
    <script src="{{asset('msc/wechat/common/js/swiper.min.js')}}"></script>
    <Script type="text/javascript">
        $(function(){
            $(".more_txt").click(function(){
                var h=parseInt($(".gn_txt").height());
                var height=parseInt($(".gn_txt span").height()+6);
                if(h==18){
                    $(".more_txt").css({transform:"rotate(90deg)"});
                    $(".gn_txt").animate({height:height},300);
                }else{
                    $(".more_txt").css({transform:"rotate(0deg)"});
                    $(".gn_txt").animate({height:"30px"},300);
                }
            })
            swiper();//自动滚屏图片切换
        })


        function  swiper(){
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                centeredSlides: true,
                autoplay: 2500,
                spaceBetween:30,
            });
        }
    </Script>
@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        外借历史
    <a class="right header_btn" href="{{ url('/msc/wechat/personal-center/info-manage') }}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>

<div class="time_select" style="display:none;">
    <span>起时间：</span><input id="star_time" type="date" placeholder="起时间">
    <span style="margin-top:10px;">终时间：</span><input id="end_time" type="date" placeholder="终时间">
</div>

<div id="info_list">
    <form action="#">
        <div class="scroll_image">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide"><img src="{{asset('msc/wechat/common/img/waiting.png')}}" alt="" /></div>
                    <div class="swiper-slide"><img src="{{asset('msc/wechat/common/img/waiting.png')}}" alt="" /></div>
                    <div class="swiper-slide"><img src="{{asset('msc/wechat/common/img/waiting.png')}}" alt="" /></div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>


        <div class="add_main mart_5">
            <div class="form-group">
                <label for="">名称</label>
                <div class="txt">
                    {{ @$historyDetail['user']['name'] }}
                </div>
            </div>
            <div class="form-group">
                <label for="">类别</label>
                <div class="txt">
                    {{ @$historyDetail['resourcesTool']['name'] }}
                </div>
            </div>
            <div class="form-group">
                <label for="">编号</label>
                <div class="txt">
                    {{ @$historyDetail['code'] }}
                </div>
            </div>

            <div class="form-group">
                <label for="">外借时段</label>
                <div class="txt">
                    {{ @$historyDetail['real_begindate'] }}-{{ @$historyDetail['real_enddate'] }}
                </div>
            </div>

            <div class="form-group">
                <label for="">设备状态</label>
                <div class="txt">
                    {{ @$historyDetail['resourcesToolItem']['status'] }}
                </div>
            </div>
            <div class="form-group">
                <label for="">功能描述</label>
                <div class="gn_txt">
                    <span class="font16">{{ @$historyDetail ['resourcesTool']['detail'] }}</span>
                    <div class="font18 clo9 more_txt">
                        <i class="fa fa-angle-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>


@stop