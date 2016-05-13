
@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        /*题目区域外边框*/
        .cBorder{border: 1px solid #e7eaec;}
        /*选择框样式*/
        .check_other {display: inline-block;vertical-align: middle;}
        .check_top {top: 8px;display: block;}

        /*覆盖页面样式*/
        .wizard > .steps > ul > li{;margin-right: 5px;border-radius: 2px;cursor: pointer;
            width: auto!important;
        }
        .check_label{cursor: pointer}

        .colockbox{width:250px;height:30px;overflow: hidden; color:#000;}
        .colockbox span{
            float:left;display:inline-block;
            width:30px;height:29px;
            line-height:29px;font-size:20px;
            font-weight:bold;text-align:center;
            color:#ff0101; margin-right:5px;}
        /*图片样式*/
        .pic{padding: 1em;}
        .pic:hover{cursor: pointer;}
    </style>
    <link href="{{asset('osce/admin/plugins/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/plugins/steps/jquery.stepschange.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/js/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">
@stop

@section('only_js')
    <!--[if IE]>
    <script src="{{asset('osce/admin/js/html5shiv.min.js')}}"></script>
    <![endif]-->
    {{--<script type="text/javascript" src="{{ asset('osce/admin/js/countdown/js/jquery.classyled.js') }}"></script>
    <script type="text/javascript" src="{{ asset('osce/admin/js/countdown/js/raphael.js') }}"></script>--}}
    <script src="{{ asset('osce/admin/plugins/js/plugins/staps/jquery.stepschange.js') }}"></script>
    <script src="{{ asset('osce/admin/plugins/js/plugins/fancybox/jquery.fancybox.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".wizard").steps({
                enableAllSteps: true, //所有按钮都可以点击
            });
            for(var i=0;i<$(".steps li").length;i++){//给按钮增加初始类名
                if(!$($(".steps li")[i]).hasClass("current")){
                    $($(".steps li")[i]).addClass("done");
                }
            }
            //图片点击显示大图
            $('.fancybox').fancybox({
                openEffect: 'none',
                closeEffect: 'none'
            });
            $(".actions").prepend($('.btnBox'));
            $(".check_label").change(function(){
                var examCategoryFormalId= $(this).parent().attr("examCategoryFormalId");//判断题型
                var exam_question_id= $(this).parent().parent().parent().find(".subjectBox").attr("exam_question_id");//获取题号ID
                var answer="";//答案
                if($(this).children(".check_icon").hasClass("check")){
                    $(this).children(".check_icon").removeClass("check");
                    $(this).children("input").attr("checked",false);
                }else{
                    $(this).children(".check_icon").addClass("check");
                    $(this).children("input").attr("checked",true);
                }
                $(this).parent().parent().find(".check").each(function(index, element){//查找被选中的项
                    if(index==0){
                        answer=$(element).next("input").val();
                    }else{
                        answer=answer+"@"+$(element).next("input").val();
                    }
                });
                Set_answer(examCategoryFormalId,exam_question_id,answer);//保存成绩
            });

            $(".radio_label").click(function(){//单选按钮
                var examCategoryFormalId= $(this).parent().attr("examCategoryFormalId");//判断题型
                var exam_question_id= $(this).parent().parent().parent().find(".subjectBox").attr("exam_question_id");//获取题号ID
                var answer = $(this).children("input").val();
                Set_answer(examCategoryFormalId,exam_question_id,answer);//保存成绩
                if($(this).children(".radio_icon").hasClass("check")){
                }else{
                    $(this).parent().siblings(".answerBox").find(".radio_icon").removeClass("check");
                    $(this).children(".radio_icon").addClass("check");
                }
            });
            function Set_answer(examCategoryFormalId,exam_question_id,answer){
                var Storage_answer  = {};//单个题目成绩
                var Storage_answer_list=[]; //页面储存成绩
                Storage_answer.examCategoryFormalId=examCategoryFormalId;
                Storage_answer.exam_question_id = exam_question_id;
                Storage_answer.answer = answer;
                var now_answer_list=localStorage.getItem("Storage_answer");
                if(now_answer_list!=null)
                {
                    Storage_answer_list=JSON.parse(now_answer_list);
                    for(i=0;i<Storage_answer_list.length;i++){
                        if(Storage_answer_list[i].exam_question_id == exam_question_id){
                            Storage_answer_list.splice(i,1);
                        }
                    }
                }
                Storage_answer_list.push(Storage_answer);
                localStorage.setItem("Storage_answer",JSON.stringify(Storage_answer_list));//设置本地存储
            }
            $(".actions").find("a[href='#finish']").click(function(){
                layer.confirm('确认提交答题？',{btn: ['确认','取消']},
                        function(){   //var postnew=localStorage.getItem("Storage_answer")+"{{$examPaperFormalData["id"]}}";
                            clearInterval(statusTimer);
                            var examPaperFormalId=$('#examPaperFormalId').val();
                            var examQuestionFormalInfo=JSON.parse(localStorage.getItem("Storage_answer"));
                            var stationId = $(".allData").attr("stationId");
                            var userId = $(".allData").attr("userId");
                            var studentId = $(".allData").attr("studentId");
                            var examId = $(".allData").attr("examId");
                            //console.log(examQuestionFormalInfo);return false;
                            $.post("{{route('osce.admin.AnswerController.postSaveAnswer')}}",
                                    {examQuestionFormalInfo:examQuestionFormalInfo,examPaperFormalId:examPaperFormalId,studentId:studentId,stationId:stationId,teacherId:userId,examId:examId},function(obj){
                                        if(obj.code==1){
                                            $.post("{{route('osce.admin.AnswerController.postSaveStatus')}}",{examId:examId,studentId:studentId,stationId:stationId},function(res){
                                                if(res.code==1){
                                                    location.href="{{route("osce.admin.AnswerController.selectGrade")}}?examPaperFormalId="+examPaperFormalId;
                                                }else{
                                                    layer.confirm(res.message);
                                                }
                                            })

                                        }else{
                                            layer.confirm(obj.message);
                                        }
                                    })},
                        function(){
                        })
            })
        });
        countDown("{{$systemTimeEnd}}","#colockbox1","{{$systemTimeStart}}");
        //被终止考试状态改变请求
        var statusTimer = setInterval(function(){
            var examPaperFormalId=$('#examPaperFormalId').val();
            var examQuestionFormalInfo=JSON.parse(localStorage.getItem("Storage_answer"));
            var stationId = $(".allData").attr("stationId");
            var userId = $(".allData").attr("userId");
            var studentId = $(".allData").attr("studentId");
            var examId = $(".allData").attr("examId");
            $.ajax({
                url:"{{route('osce.admin.AnswerController.getControlMark')}}",
                type:"get",
                dataType:"json",
                cache:false,
                data:{examId:examId,studentId:studentId,stationId:stationId},
                success:function(res){
                    if(res == 1){
                        clearInterval(statusTimer);
                        $.post("{{route('osce.admin.AnswerController.postSaveAnswer')}}",
                                {examQuestionFormalInfo:examQuestionFormalInfo,examPaperFormalId:examPaperFormalId,studentId:studentId,stationId:stationId,teacherId:userId,examId:examId},function(obj){
                                    if(obj.code==1){
                                        $.post("{{route('osce.admin.AnswerController.postSaveStatus')}}",{examId:examId,studentId:studentId,stationId:stationId},function(res){
                                            if(res.code==1){
                                                location.href="{{route("osce.admin.AnswerController.selectGrade")}}?examPaperFormalId="+examPaperFormalId;
                                            }else{
                                                layer.confirm(res.message);
                                            }
                                        })

                                    }else{
                                        layer.confirm(obj.message);
                                    }

                                });
                    }
                }
            })
        },3000);

        function countDown(time,id,beginTime){

            var day_elem = $(id).find('.day');
            var begin_time = new Date(beginTime).getTime();
            var end_time = new Date(time).getTime();//月份是实际月份-1
            var current_time = new Date();
            current_time = current_time.getTime();
            var diff_time = current_time - begin_time;
            var sys_second = (end_time-current_time)/1000;
            sys_second += diff_time/1000;
            /*if(diff_time > 0){
                sys_second += diff_time;
            }else{
                sys_second -= diff_time;
            }*/
            //console.log(end_time);
            var timer = setInterval(function(){
                if (sys_second > 1) {
                    sys_second -= 1;
                    var day = Math.floor((sys_second / 3600) / 24);
                    var hour = Math.floor((sys_second / 3600) % 24);
                    var minute = Math.floor((sys_second / 60) % 60);
                    var second = Math.floor(sys_second % 60);
                    day_elem && $(day_elem).text(day);//计算天
                    $("#hour").text(hour<10?"0"+hour:hour);//计算小时
                    $("#minute").text(minute<10?"0"+minute:minute);//计算分钟
                    $("#second").text(second<10?"0"+second:second);//计算秒杀
                } else {
                    clearInterval(timer);
                    var examPaperFormalId=$('#examPaperFormalId').val();
                    var examQuestionFormalInfo=JSON.parse(localStorage.getItem("Storage_answer"));
                    var stationId = $(".allData").attr("stationId");
                    var userId = $(".allData").attr("userId");
                    var studentId = $(".allData").attr("studentId");
                    var examId = $(".allData").attr("examId");
                    console.log(examQuestionFormalInfo);
                    $.post("{{route('osce.admin.AnswerController.postSaveAnswer')}}",
                            {examQuestionFormalInfo:examQuestionFormalInfo,examPaperFormalId:examPaperFormalId,studentId:studentId,stationId:stationId,teacherId:userId,examId:examId},function(obj){
                                if(obj.code==1){
                                    $.post("{{route('osce.admin.AnswerController.postSaveStatus')}}",{examId:examId,studentId:studentId,stationId:stationId},function(res){
                                        if(res.code==1){
                                            location.href="{{route("osce.admin.AnswerController.selectGrade")}}?examPaperFormalId="+examPaperFormalId;
                                        }else{
                                            layer.confirm(res.message);
                                        }
                                    })
                                }else{
                                    layer.confirm(obj.message);
                                }
                            });
                }
            }, 1000);
        }
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'theory_check'}" />
    <input type="hidden" class="allData" stationId="{{ $stationId }}" userId="{{ $userId }}" studentId="{{ $studentId }}" examId="{{ $examId }}">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-6">
                <h5 class="title-label">{{$examPaperFormalData["name"]}}&nbsp;(
                    <span>考试时间：</span>
                    <span class="checkTime">{{$examPaperFormalData["length"]}}分钟</span>
                    <span style="margin-left: 1em;">总分：</span>
                    <span class="score">{{$examPaperFormalData["totalScore"]}}分</span>
                    <input type="hidden" id="examPaperFormalId" value="{{$examPaperFormalData["id"]}}">)</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12" style="display: none;">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$examPaperFormalData["name"]}}</h2>
                        <span>考试时间：</span>
                        <span class="checkTime">{{$examPaperFormalData["length"]}}分钟</span>
                        <span style="margin-left: 1em;">总分：</span>
                        <span class="score">{{$examPaperFormalData["totalScore"]}}分</span>
                        <input type="hidden" id="examPaperFormalId" value="{{$examPaperFormalData["id"]}}">
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-left">
                        <div class="p-md cBorder">
                            <div class="wizard">
                                @if(!empty($examCategoryFormalData))
                                    @foreach(@$examCategoryFormalData as $val )
                                        <h1>{{$val["serialNumber"]}} </h1>
                                        <div class="step-content">
                                            <span class="font20" style="font-weight: 700;">{{@$val["examCategoryFormalName"]}}</span>
                                            <span style="margin-left: 1em;">共<span class="subjectNum">{{@$val["examCategoryFormalNumber"]}}</span>题，</span>
                                            <span>每题<span class="subjectScore">{{@$val["examCategoryFormalScore"]}}</span>分</span>
                                            <div class="allSubject">
                                                <div class="subjectBox   mart_10 " exam_question_id="{{@$val["id"]}}">
                                                    <span class="font16 subjectContent">{{ @$val["name"]}}</span>
                                                </div>

                                                <div class="col-sm-6 col-md-6">
                                                    @if(@$val["examQuestionTypeId"]==1)
                                                        @foreach(@$val["content"] as $k=> $val2 )
                                                            <div class="answerBox" examCategoryFormalId="{{@$val["examQuestionTypeId"]}}">
                                                                <label class="radio_label mart_10 check_top">
                                                                    <div class="radio_icon left" ></div>
                                                                    <input type="radio" name="{{@$val["serialNumber"]}}" value="{{@$k}}">
                                                                    <span class="marl_10 answer">{{@$val2}}</span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @if(@$val["examQuestionTypeId"]==2||@$val["examQuestionTypeId"]==3)
                                                        @foreach(@$val["content"] as $k=> $val2 )
                                                            <div class="answerBox" examCategoryFormalId="{{@$val["examQuestionTypeId"]}}">
                                                                <label class="check_label checkbox_input mart_10 check_top" style="">
                                                                    <div class="check_icon check_other"></div>
                                                                    <input type="checkbox" name="{{@$val["serialNumber"]}}" value="{{@$k}}">
                                                                    <span class="check_name">{{@$val2}}</span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                    @if(@$val["examQuestionTypeId"]==4)
                                                        {{--@foreach($val["content"] as $k=> $val2 )--}}
                                                        <div class="answerBox" examCategoryFormalId="{{@$val["examQuestionTypeId"]}}">
                                                            <label class="radio_label mart_10 check_top">
                                                                <div class="radio_icon left" ></div>
                                                                <input type="radio" name="{{@$val["serialNumber"]}}" value="0">
                                                                <span class="marl_10 answer">
                                                                    {{--@if($val2==0)--}}
                                                                    {{--错误--}}
                                                                    {{--@elseif($val2==1)--}}
                                                                    {{--正确--}}
                                                                    {{--@endif--}}
                                                                    错误
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="answerBox" examCategoryFormalId="{{@$val["examQuestionTypeId"]}}">
                                                            <label for="" class="radio_label mart_10 check_top">
                                                                <div class="radio_icon left" ></div>
                                                                <input type="radio" name="{{@$val["serialNumber"]}}" value="1">
                                                                <span class="marl_10 answer">正确</span>
                                                            </label>
                                                        </div>
                                                        {{--@endforeach--}}
                                                    @endif
                                                </div>
                                                <div class="col-sm-6 col-md-6">
                                                    <div class="picBox">
                                                        @if(!empty($val['image']))
                                                            @foreach($val['image'] as $item)
                                                                <a href="{{$item}}" class="fancybox">
                                                                    <img src="{{$item}}" alt="image" class="pic" style="height: 150px;width: 150px;">
                                                                </a>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="btnBox left" style="margin:0 auto; text-align: center; width: 800px;padding-left:400px;">
                            <span class="marl_10 left" style="display:inline-block; height: 29px; width:100px; line-height: 29px;">剩余时间：</span>
                            <div class="colockbox" id="colockbox1">
                                <span class="hour" id="hour">00</span><span class="left">:</span>
                                <span class="minute" id="minute">00</span><span class="left">:</span>
                                <span class="second" id="second">00</span>
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