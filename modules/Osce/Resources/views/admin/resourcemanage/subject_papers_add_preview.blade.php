@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style>
        body{background-color: #fff!important;}
    </style>
@stop

@section('only_js')

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
                                <div class="picBox" style="width: 200px">
                                    @if(!empty($val2["image"]))
                                        @foreach(unserialize($val2["image"]) as $item)
                                            <img src="{{$item}}" alt="">
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

