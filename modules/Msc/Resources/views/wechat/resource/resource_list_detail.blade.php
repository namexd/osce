@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/common/css/swiper.min.css')}}" rel="stylesheet" type="text/css" />

@stop

@section('only_head_js')
<script src="{{asset('msc/wechat/common/js/swiper.min.js')}}"></script>
@stop

@section('content')
<div class="user_header">
    <a class="left header_btn" href="{{action('\Modules\Msc\Http\Controllers\WeChat\ResourceController@getResourceList')}}">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
    新增资源
    <a class="right header_btn" href="#">
        <i class="icon_code"></i>
    </a>
</div>
<form action="#">

    <div class="swiper-container">
        <div class="swiper-wrapper">
            @forelse($images as $image)
            <div class="swiper-slide"><img src="{{asset($image->url)}}" alt="" /></div>
            @empty
            <div class="swiper-slide"><img src="{{asset('msc/wechat/information/img/jiaoshi.png')}}" alt="" /></div>
            @endforelse
        </div>
    </div>
    <div class="swiper-pagination"></div>

    <script>
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationClickable: true,
            spaceBetween: 30,
        });
    </script>

    <div class="add_main">
        <div class="form-group">
            <label for="">类别</label>
            <input type="button" class="form-control xuan_type"  name="" value="{{ $info->categroy or '未设置' }}" required="">
        </div>
        <div class="form-group">
            <label for="">名称</label>
            <input type="button" class="form-control xuan_type"  name="" value="{{ $info->name or '未设置' }}" required="">
        </div>
        <div class="form-group">
            <label for="">功能描述</label>
            <div class="gn_txt">
                {{ $info->detail or '-' }}
            </div>
        </div>
        <div class="form-group">
            <label for="">负责人</label>
            <input type="button" class="form-control xuan_type"  name="" value="{{ $info->getManagerName() }}" required="">
        </div>
        <div class="form-group">
            <label for="">负责电话</label>
            <input type="button" class="form-control xuan_type"  name="" value="{{ $info->getManagerMoblie() }}" required="">
        </div>
        <div class="form-group">
            <label for="">地址</label>
            <input type="button" class="form-control xuan_type"  name="" value="{{ is_null($info->address)? '-':$info->address->name}}" required="">
        </div>
    </div>
    <!-- <div class="w_94 submit_box">
        <input type="submit" class="btn" value="保存"/>
    </div> -->
</form>

<style type="text/css">
    /*顶部轮播图片切换*/
    .swiper-container{width:100%;height:100%;}
    .swiper-slide{text-align:center;font-size:18px;background:#fff;
        display:-webkit-box;
        display:-ms-flexbox;
        display:-webkit-flex;display:flex;
        -webkit-box-pack:center;
        -ms-flex-pack:center;
        -webkit-justify-content:center;
        justify-content:center;
        -webkit-box-align:center;
        -ms-flex-align:center;
        -webkit-align-items:center;
        align-items:center;}
    .swiper-slide img{width: 100%;}
    .wrapper img{width:100%;}
    .swiper-pagination{ position:relative;}
    .swiper-pagination span{margin:0 0.2em 0 0 }

</style>
<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        centeredSlides: true,
        autoplay: 2500,
        spaceBetween:30,
    });
    
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
    })
</script>
@stop
