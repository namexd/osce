@extends('msc::admin.layouts.admin')

@section('only_js')
<!-- Sweet alert -->

<script src="{{asset('msc/wechat/common/js/swiper.min.js')}}"></script>
@stop
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/resourcemanage/css/managedetail.css')}}">
    <link href="{{asset('msc/wechat/common/css/swiper.min.css')}}" rel="stylesheet" type="text/css" />

@stop



@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>资源详情</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-5">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @if ($images)
                                    @foreach ($images as $image)
                                        <div class="swiper-slide"><img src="{{ asset($image->url) }}" alt="" /></div>
                                    @endforeach
                                @endif
                                <div class="swiper-slide"><img src="{{ asset($image->url) }}" alt="" /></div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>

                    <div class="col-md-7 ">
                        <form method="post" class="form-horizontal" id="sourceForm">

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" disabled="disabled" id="name" value="{{ $resourcesTool->name }}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">类别</label>
                                <div class="col-sm-10">
                                    <select class="form-control" disabled="disabled">
                                        <option value="{{ $resourcesTool->categroy->name }}">{{ $resourcesTool->categroy->name }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">编号</label>

                                <div class="col-sm-10">
                                    <input disabled="disabled" type="text" disabled="disabled"  id="code" class="form-control" value="{{ $resourcesToolItem->code}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">负责人</label>

                                <div class="col-sm-10">
                                    <input disabled="disabled" type="text" id="manager_name" class="form-control" value="{{ $resourcesTool->getManagerName() }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label" required>负责人电话</label>

                                <div class="col-sm-10">
                                    <input type="text" disabled="disabled" id="manager_mobile" class="form-control" value="{{ $resourcesTool->getManagerMoblie() }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">功能描述</label>
                                <div class="gn_txt">
                                    {{ $resourcesTool->detail or '-' }}
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>

                                <div class="col-sm-10">
                                    <input type="text" disabled="disabled" id="location" class="form-control" value="{{ $resourcesTool->location or '-'}}">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-primary" href="{{route('msc.admin.resourcesManager.getBorrowRecordList')}}">返&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;回</a>
                                </div>
                            </div>


                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
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
@stop{{-- 内容主体区域 --}}