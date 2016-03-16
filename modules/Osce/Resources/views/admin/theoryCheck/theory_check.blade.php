@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        /*题目区域外边框*/
        .cBorder{border: 1px solid #e7eaec;}
        /*选择框样式*/
        .check_other {display: inline-block;vertical-align: middle;}
        .check_top {top: 8px;display: block;}
        /*按钮框下面线*/
        .cBorder_b{border-bottom: 1px solid #e7eaec;}
        /*选项样式*/
        .padb{padding-bottom: 56px;}
        .chooseOne{padding: 10px;margin-right: 5px;border-radius: 2px;cursor: pointer;}
        .haveChoose{border: 1px solid #aeddd9;background-color: #aeddd9;}
        .nowChoose{border: 1px solid #16beb0;background-color: #16beb0;color: #fff;}
        .waitChoose{border: 1px solid #e7eaec;}
        /*覆盖页面样式*/
        .wizard > .steps > ul > li{;margin-right: 5px;border-radius: 2px;cursor: pointer;
            width: auto!important;
        }
    </style>
    <link href="{{asset('osce/admin/plugins/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/plugins/steps/jquery.steps.css')}}" rel="stylesheet">

@stop

@section('only_js')
    <script src="{{asset('osce/admin/js/all_checkbox.js')}}"> </script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/staps/jquery.stepschange.js')}}"></script>
    <script>
        $(document).ready(function() {
            $(".wizard").steps();
        });
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'theory_check'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">理论考试</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$examPaperFormalData["name"]}}</h2>
                        <span>考试时间：</span>
                        <span class="checkTime">{{$examPaperFormalData["length"]}}分钟</span>
                        <span style="margin-left: 1em;">总分：</span>
                        <span class="score">{{$examPaperFormalData["totalScore"]}}分</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-left p-md">
                        @if(!empty($examCategoryFormalData))
                            @foreach(@$examCategoryFormalData as $val )
                                <div class="bigTitle">
                                    <span class="font20">{{@$val["name"]}}</span>
                                    <span style="margin-left: 1em;">共<span class="subjectNum">{{@$val["number"]}}</span>题，</span>
                                    <span>每题<span class="subjectScore">{{@$val["score"]}}</span>分</span>
                                </div>
                                <div class="wizard">
                                @if(!empty($val["exam_question_formal"]))
                                    @foreach($val["exam_question_formal"] as $k => $val2 )
                                        <h1>{{$val2["serialNumber"]}} </h1>
                                        <div class="step-content">
                                            <div class="allSubject">
                                                <div class="subjectBox">
                                                    <span class="font20 subjectNo"></span>
                                                    <span class="font20 marl_10 subjectContent">{{ $val2["name"]}}</span>
                                                </div>
                                                <div class="answerBox">
                                                    @if(!empty($val2["content"]))
                                                        @foreach($val2["content"] as $k=> $val3 )
                                                            <label class="check_label checkbox_input mart_20 check_top" style="">
                                                                <div class="check_icon check_other"></div>
                                                                <input type="checkbox" name="nosureAnswer"  value={{$k}}>
                                                                <span class="marl_10 answer">{{@$val3}}</span>
                                                            </label>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                </div>
                            @endforeach
                        @endif

                        <div class="bigTitle">
                            <span class="font20">单选题</span>
                            <span style="margin-left: 1em;">共<span class="subjectNum">5</span>题，</span>
                            <span>每题<span class="subjectScore">5</span>分</span>
                        </div>

                        <div class="p-md cBorder mart_10" style="display:none">
                            <div class="btnBox" style="margin: 70px 0 50px 0;">
                                <button class="btn btn-primary" id="nextBtn">下一题</button>
                                <button class="btn btn-primary" id="beforeBtn">上一题</button>
                                <button class="btn btn-warning" id="goBtn">提交试卷</button>
                                <span class="marl_10">剩余时间</span>
                                <span class="font24" style="color: #ff0101;font-weight: 700;">10:10</span>
                            </div>
                            <div class="cBorder_b"></div>
                            <div class="chooseBox">
                                <div class="font16" style="padding: 20px 0;">本试卷包含以下试题</div>
                                <div class="padb choose">
                                    <span class="haveChoose left chooseOne">1.1</span>
                                    <span class="nowChoose left chooseOne">1.2</span>
                                    <span class="waitChoose left chooseOne">1.3</span>
                                </div>
                            </div>
                        </div>
                        <div class="btnBox" style="margin: 70px 0 50px 0;">
                            <span class="marl_10">剩余时间</span>
                            <span class="font24" style="color: #ff0101;font-weight: 700;">10:10</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')

@stop