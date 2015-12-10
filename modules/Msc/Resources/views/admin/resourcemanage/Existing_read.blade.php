@extends('msc::admin.layouts.admin')

@section('only_js')
<!-- Sweet alert -->
<script src="{{asset('msc/admin/js/fileinput.min.js')}}"></script>
<script src="{{asset('msc/admin/js/fileinput_locale_zh.js')}}"></script>
<script src="{{asset('msc/admin/plugins/js/plugins/sweetalert/sweetalert.min.js')}}"></script>
<script src="{{asset('msc/wechat/common/js/swiper.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $("#sourceForm input").attr("disabled","disabled");
            $("#sourceForm select").attr("disabled","disabled");
            swiper();//自动滚屏图片切换

        });
        
        function  swiper(){
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                centeredSlides: true,
                autoplay: 2500,
                // 如果需要前进后退按钮
			    nextButton: '.swiper-button-next',
			    prevButton: '.swiper-button-prev',
                spaceBetween:30,
            });
        }
    </script>
@stop
@section('only_css')
    <link rel="stylesheet" href="{{asset('msc/admin/plugins/css/plugins/sweetalert/sweetalert.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/resourcemanage/css/managedetail.css')}}">
    <link rel="stylesheet" href="{{asset('msc/admin/css/fileinput.min.css')}}">
    <link href="{{asset('msc/common/css/swiper.min.css')}}" rel="stylesheet" type="text/css" />
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
                        <div class="scroll_image">
				         <div class="swiper-container">
				         	
				                <div class="swiper-wrapper">
				                    @if ($resource['image'])
			                                @foreach ($resource['image'] as $image)			                                    
			                                    <div class="swiper-slide"><img src="{{ asset($image['url']) }}" /></div>
			                                @endforeach
                           			 @endif
				                </div>
				            </div>
				            <div class="swiper-pagination"></div>
			                <div class="swiper-button-prev"></div>
							<div class="swiper-button-next"></div>
				        </div>
                    </div>
                    <div class="col-md-7 ">
                        <form method="post" class="form-horizontal" id="sourceForm">
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="name" value="{{ $resource['name'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">类别</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="name" value="{{ $resource['categoryName'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">负责人</label>
                                <div class="col-sm-10">
                                    <input type="text" id="manager_name" class="form-control" value="{{ $resource['manager_name'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >负责人电话</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="manager_mobile" class="form-control" value="{{ $resource['manager_mobile'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">功能描述</label>
                                <div class="col-sm-10">
                                    <input type="text" id="description" class="form-control" value="{{ $resource['detail'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>
                                <div class="col-sm-10">
                                    <input type="text" id="location" class="form-control" value="{{ $resource['locationName'] }}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}