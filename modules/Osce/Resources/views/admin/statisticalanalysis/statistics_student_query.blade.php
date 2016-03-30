@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .check_name{margin-left:5px}
        .check_label{margin-left:48px;}
        .group_border{border-bottom:1px solid #e7eaec}
        .check_icon{margin-top: 2px;}
    </style>
@stop

@section('only_js')

@stop

@section('content')
<!--理论考试展示查询页面-->
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考生成绩统计</h5>
            </div>
            <div class="col-xs-6 col-md-2 right">
                <a href="javascript:history.go(-1)" class="btn btn-outline btn-default right">返回</a>
            </div>
        </div>
        <div class="ibox-content">
            <div class="center" style="margin-bottom: 40px;">
                @if(!empty(@$examItems))
                    <h2>{{ @$examItems['exam_name'] }}</h2>
                    <div>
                        <span>考试姓名：</span><span>{{$examItems['student_name']}}</span>
                        <span style="margin-left: 1em;">考试用时：</span><span>{{ @$examItems['actual_length'] }}</span>
                        <span style="margin-left: 1em;">最后得分：</span><span>{{ @$examItems['stuScore'] }}</span>分
                    </div>
                @endif
            </div>
            @if(!empty(@$data))
                @foreach(@$data as $val)
                    <div class="marb_25">
                        <h3>{{ @$val['Title'] }}</h3>
                        @if(!empty(@$val['child']))
                            @foreach(@$val['child'] as $val1)
                                <div class="group_border" style="padding: 1em 0;">
                                    <h4>{{ @$val1['exam_question_name'] }}</h4>
                                    <div class="picBox" style="width: 200px">
                                        @if(!empty($val1['exam_question_image']))
                                            @foreach($val1['exam_question_image'] as $item)
                                                <img src="{{$item}}" alt="">
                                            @endforeach
                                        @endif
                                    </div>
                                    @if(@$val['questionType'] == 4)
                                        <span class="marr_15">
                                            <label class="check_label" style="margin:10px">
                                                <div class="check_icon @if($val1['student_answer'] == '正确') check @endif left"></div>
                                                <span class="check_name left">正确</span>
                                            </label>
                                            <label class="check_label" style="margin:10px">
                                                <div class="check_icon @if($val1['student_answer'] == '错误') check @endif left"></div>
                                                <span class="check_name left">错误</span>
                                            </label>
                                        </span>
                                        <div class="text">
                                            @if($val1['answer'] == $val1['student_answer'])
                                                <p>考生答案：<span style="color:#56b04b">{{ @$val1['student_answer'] }}</span>（{{ @$val1['answer'] }}）</p>
                                            @else
                                                <p>考生答案：<span style="color:#ed5565">{{ @$val1['student_answer'] }}</span>（{{ @$val1['answer'] }}）</p>
                                            @endif
                                            <p>{{ @$val1['parsing'] }}</p>
                                        </div>
                                    @else
                                        @if(!empty(@$val1['contentItem']))
                                            @foreach(@$val1['contentItem'] as $val2)
                                                <span class="marr_15">
                                                    <label class="check_label" style="margin:10px">
                                                        <?php $Answer = explode(':',$val2)?>
                                                        <div class="check_icon @if(in_array(@$Answer[0],$val1['studentAnswerAarry'])) check @endif left"></div>
                                                        <span class="check_name left">{{ $val2 }}</span>
                                                    </label>
                                                </span>
                                            @endforeach
                                        @endif
                                        <div class="text">
                                            <?php $Result=explode('、',$val1['answer'])?>
                                            <?php $c = array_diff($Result, $val1['studentAnswerAarry']);?>
                                            @if(!empty($c))
                                                <p>考生答案：<span style="color:#ed5565">{{ @$val1['student_answer'] }}</span>（{{ @$val1['answer'] }}）</p>
                                            @else
                                                <p>考生答案：<span style="color:#56b04b">{{ @$val1['student_answer'] }}</span>（{{ @$val1['answer'] }}）</p>
                                            @endif
                                            <p>{{ @$val1['parsing'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

