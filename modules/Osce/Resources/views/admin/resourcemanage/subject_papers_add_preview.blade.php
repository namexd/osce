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
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">试卷预览</h5>
            </div>
        </div>
        <div class="ibox-content">
                        <h2>2016年第一期OSCE考试理论试卷</h2>
            <p>考试时长：20分钟　　　总分：40分</p>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

