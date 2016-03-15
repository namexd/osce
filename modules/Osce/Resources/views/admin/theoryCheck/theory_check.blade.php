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
    </style>
    <link href="osce/admin/plugins/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="osce/admin/plugins/css/plugins/steps/jquery.steps.css" rel="stylesheet">
@stop

@section('only_js')
    <script src="{{asset('osce/admin/js/all_checkbox.js')}}"> </script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
    <script>
        $(document).ready(function(){$("#wizard").steps();$("#form").steps({bodyTag:"fieldset",onStepChanging:function(d,a,b){if(a>b){return true}if(b===3&&Number($("#age").val())<18){return false}var c=$(this);if(a<b){$(".body:eq("+b+") label.error",c).remove();$(".body:eq("+b+") .error",c).removeClass("error")}c.validate().settings.ignore=":disabled,:hidden";return c.valid()},onStepChanged:function(b,a,c){if(a===2&&Number($("#age").val())>=18){$(this).steps("next")}if(a===2&&c===3){$(this).steps("previous")}},onFinishing:function(c,a){var b=$(this);b.validate().settings.ignore=":disabled";return b.valid()},onFinished:function(c,a){var b=$(this);b.submit()}}).validate({errorPlacement:function(a,b){b.before(a)},rules:{confirm:{equalTo:"#password"}}})});
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
                        <h2>2016年第一期OSCE考试理论考试</h2>
                        <span>考试时间：</span>
                        <span class="checkTime">20分钟</span>
                        <span style="margin-left: 1em;">总分：</span>
                        <span class="score">100分</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-left p-md">
                        <div class="bigTitle">
                            <span class="font20">单选题</span>
                            <span style="margin-left: 1em;">共<span class="subjectNum">5</span>题，</span>
                            <span>每题<span class="subjectScore">5</span>分</span>
                        </div>
                        <div class="p-md cBorder mart_10">
                            <div class="allSubject">
                                <div class="subjectBox">
                                    <span class="font20 subjectNo">1.1</span>
                                    <span class="font20 marl_10 subjectContent">下列感染中，不具有传染性的是？</span>
                                </div>
                                <div class="answerBox">
                                    <label class="check_label checkbox_input mart_20 check_top" style="">
                                        <div class="check_icon check_other"></div>
                                        <input type="checkbox" name="nosureAnswer"  value="A">
                                        <span class="check_name">A</span>
                                        <span class="marl_10 answer">隐形感染</span>
                                    </label>
                                    <label class="radio_label mart_20 check_top">
                                        <div class="radio_icon left" ></div>
                                        <input type="radio" name="oneAnswer" value="B">
                                        <span class="radio_name">B</span>
                                        <span class="marl_10 answer">显性感染</span>
                                    </label>
                                </div>
                            </div>
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

                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>基础表单向导</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="form_wizard.html#">
                                            <i class="fa fa-wrench"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-user">
                                            <li><a href="form_wizard.html#">选项1</a>
                                            </li>
                                            <li><a href="form_wizard.html#">选项2</a>
                                            </li>
                                        </ul>
                                        <a class="close-link">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                    <p>
                                        这是一个简单的表单向导示例
                                    </p>
                                    <div id="wizard">
                                        <h1>第一步</h1>
                                        <div class="step-content">
                                            <div class="text-center m-t-md">
                                                <h2>第一步</h2>
                                                <p>
                                                    这是第一步的内容
                                                </p>
                                            </div>
                                        </div>

                                        <h1>第二步</h1>
                                        <div class="step-content">
                                            <div class="text-center m-t-md">
                                                <h2>第二步</h2>
                                                <p>
                                                    这是第二步的内容
                                                </p>
                                            </div>
                                        </div>

                                        <h1>第三步</h1>
                                        <div class="step-content">
                                            <div class="text-center m-t-md">
                                                <h2>第三步</h2>
                                                <p>
                                                    这是第三步的内容
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')

@stop