@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/js/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">
    <style>
        body{background-color: #fff!important;}
        .pic{padding: 1em;}
        .pic:hover{cursor: pointer;}
    </style>
@stop

@section('only_js')
    <script src="{{ asset('osce/admin/plugins/js/plugins/fancybox/jquery.fancybox.js') }}"></script>
    <script>
        $(document).ready(function () {
//            图片点击显示大图
            $('.fancybox').fancybox({
                openEffect: 'none',
                closeEffect: 'none'
            });
        });
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="center">
            <h2>{{$PaperPreviewArr["name"]}}</h2>
            <p>考试时长：{{$PaperPreviewArr["time"]}}分钟　　　总分：{{@$PaperPreviewArr['total_score']}}分</p>
        </div>
        @if(!empty($PaperPreviewArr["item"]))
            @foreach(@$PaperPreviewArr["item"] as $k =>$val )
                <div class="form-group marb_25">
                    <h4>{{@$val["name"]}}</h4>
                    @if(!empty($val["child"]))
                        @foreach($val["child"] as $k => $val2 )
                            <div class="form-group">
                                <p>{{$k+1}}、{{@$val2["name"]}}（　　）</p>
                                <div class="picBox">
                                    @if(!empty($val2["image"])&&count(unserialize($val2["image"]))>0)
                                        @foreach(unserialize($val2["image"]) as $item)
                                            <a href="{{$item}}" class="fancybox">
                                                <img src="{{$item}}" alt="image" class="pic" style="height: 150px;width: 150px;">
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                                @if(!empty($val2->examQuestionItem))
                                    @foreach($val2->examQuestionItem as $val3 )
                                        <span class="marr_15">{{@$val3["name"]}}、{{@$val3["content"]}}</span>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        @endif
    </div>
@stop{{-- 内容主体区域 --}}

