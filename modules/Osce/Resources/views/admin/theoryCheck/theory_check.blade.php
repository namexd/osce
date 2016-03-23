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
            float:left;display:block;
            width:30px;height:29px;
            line-height:29px;font-size:20px;
            font-weight:bold;text-align:center;
            color:#ff0101; margin-right:5px;}
    </style>
    <link href="{{asset('osce/admin/plugins/css/plugins/iCheck/custom.css')}}" rel="stylesheet">
    <link href="{{asset('osce/admin/plugins/css/plugins/steps/jquery.stepschange.css')}}" rel="stylesheet">

@stop

@section('only_js')
    <!--[if IE]>
    <script src="{{asset('osce/admin/js/html5shiv.min.js')}}"></script>
    <![endif]-->
    <script type="text/javascript" src="{{ asset('osce/admin/js/countdown/js/jquery.classyled.js') }}"></script>
    <script type="text/javascript" src="{{ asset('osce/admin/js/countdown/js/raphael.js') }}"></script>
    <script src="{{ asset('osce/admin/plugins/js/plugins/staps/jquery.stepschange.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(".wizard").steps();
            $(".check_label").change(function(){
                var examCategoryFormalId= $(this).parent().attr("examCategoryFormalId");//判断题型
                var exam_question_id= $(this).parent().parent().find(".subjectBox").attr("exam_question_id");//获取题号ID
                var answer="";//答案
                if($(this).children("input").checked=="true"){
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

            $(".radio_label").change(function(){//单选按钮
                var examCategoryFormalId= $(this).parent().attr("examCategoryFormalId");//判断题型
                var exam_question_id= $(this).parent().parent().find(".subjectBox").attr("exam_question_id");//获取题号ID
                var answer = $(this).children("input").val();
                Set_answer(examCategoryFormalId,exam_question_id,answer);//保存成绩
                if($(this).children("input").checked=="true"){
                    $(this).children(".radio_icon").removeClass("check");
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
                //var postnew=localStorage.getItem("Storage_answer")+"{{$examPaperFormalData["id"]}}";
                var examPaperFormalId=$('#examPaperFormalId').val();
                var examQuestionFormalInfo=JSON.parse(localStorage.getItem("Storage_answer"));
                var stationId = $(".allData").attr("stationId");
                var userId = $(".allData").attr("userId");
                var studentId = $(".allData").attr("studentId");
                $.post("{{route('osce.admin.AnswerController.postSaveAnswer')}}",{examQuestionFormalInfo:examQuestionFormalInfo,examPaperFormalId:examPaperFormalId},function(obj){
                    if(obj.status=='2'){
                        $.ajax({
                            url:"/osce/pad/change-status?student_id="+studentId+"&station_id="+stationId+"&user_id="+userId,
                            cache:false,
                            dataType:"json",
                            type:"get",
                            success:function(res){
                                if(res.code == 1){
                                    location.href="{{route("osce.admin.AnswerController.selectGrade")}}?examPaperFormalId="+examPaperFormalId;
                                }
                            }
                        });
                    }
                    if(obj.status=='3'){
                        console.log('保存失败');
                    }
                })
            })

        });
        $(function(){
            countDown("{{$systemTimeEnd}}","#colockbox1");
        });
        function countDown(time,id){
            var day_elem = $(id).find('.day');
            var hour_elem = $(id).find('.hour');
            var minute_elem = $(id).find('.minute');
            var second_elem = $(id).find('.second');
            var end_time = new Date(time).getTime(),//月份是实际月份-1
                    sys_second = (end_time-new Date().getTime())/1000;
            var timer = setInterval(function(){
                if (sys_second > 1) {
                    sys_second -= 1;
                    var day = Math.floor((sys_second / 3600) / 24);
                    var hour = Math.floor((sys_second / 3600) % 24);
                    var minute = Math.floor((sys_second / 60) % 60);
                    var second = Math.floor(sys_second % 60);
                    day_elem && $(day_elem).text(day);//计算天
                    $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
                    $(minute_elem).text(minute<10?"0"+minute:minute);//计算分钟
                    $(second_elem).text(second<10?"0"+second:second);//计算秒杀
                } else {
                    //var postnew=localStorage.getItem("Storage_answer")+"{{$examPaperFormalData["id"]}}";
                    var examPaperFormalId=$('#examPaperFormalId').val();
                    var examQuestionFormalInfo=JSON.parse(localStorage.getItem("Storage_answer"));
                    var stationId = $(".allData").attr("stationId");
                    var userId = $(".allData").attr("userId");
                    var studentId = $(".allData").attr("studentId");
                    $.post("{{route('osce.admin.AnswerController.postSaveAnswer')}}",{examQuestionFormalInfo:examQuestionFormalInfo,examPaperFormalId:examPaperFormalId},function(obj){
                        if(obj.status=='2'){
                            $.ajax({
                                url:"/osce/pad/change-status?student_id="+studentId+"&station_id="+stationId+"&user_id="+userId,
                                cache:false,
                                dataType:"json",
                                type:"get",
                                success:function(res){
                                    if(res.code == 1){
                                        location.href="{{route("osce.admin.AnswerController.selectGrade")}}?examPaperFormalId="+examPaperFormalId;
                                    }
                                }
                            });
                        }
                        if(obj.status=='3'){
                            console.log('保存失败');
                        }
                    })
                }
            }, 1000);
        }
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'theory_check'}" />
    <input type="hidden" class="allData" stationId="{{ $stationId }}" userId="{{ $userId }}" studentId="{{ $studentId }}">
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
                                            <span class="font20">{{@$val["examCategoryFormalName"]}}</span>
                                            <span style="margin-left: 1em;">共<span class="subjectNum">{{@$val["examCategoryFormalNumber"]}}</span>题，</span>
                                            <span>每题<span class="subjectScore">{{@$val["examCategoryFormalScore"]}}</span>分</span>
                                            <div class="allSubject">
                                                <div class="subjectBox   mart_10 " exam_question_id="{{$val["id"]}}">
                                                    <span class="font16 subjectContent">{{ $val["name"]}}(　　　)</span>
                                                </div>
                                                @if($val["exam_question_type_id"]===1)
                                                    @foreach($val["content"] as $k=> $val2 )
                                                        <div class="answerBox" examCategoryFormalId="{{$val["exam_question_type_id"]}}">
                                                            <label class="radio_label mart_20 check_top">
                                                                <div class="radio_icon left" ></div>
                                                                <input type="radio" name="{{$val["serialNumber"]}}" value="{{$k}}">
                                                                <span class="marl_10 answer">{{@$val2}}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if($val["exam_question_type_id"]===2||$val["exam_question_type_id"]===3)
                                                    @foreach($val["content"] as $k=> $val2 )
                                                        <div class="answerBox" examCategoryFormalId="{{$val["exam_question_type_id"]}}">
                                                            <label class="check_label checkbox_input mart_20 check_top" style="">
                                                                <div class="check_icon check_other"></div>
                                                                <input type="checkbox" name="{{$val["serialNumber"]}}" value="{{$k}}">
                                                                <span class="check_name">{{@$val2}}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                                @if($val["exam_question_type_id"]===4)
                                                    @foreach($val["content"] as $k=> $val2 )
                                                        <div class="answerBox" examCategoryFormalId="{{$val["exam_question_type_id"]}}">
                                                            <label class="radio_label mart_20 check_top">
                                                                <div class="radio_icon left" ></div>
                                                                <input type="radio" name="{{$val["serialNumber"]}}" value="{{$k}}">
                                                                <span class="marl_10 answer">
                                                                    @if($val2==0)
                                                                        错误
                                                                    @elseif($val2==1)
                                                                        正确
                                                                     @endif
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
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
                        <div class="btnBox" style="margin:0 auto; padding:70px 0; text-align: center; width: 400px;">
                            <span class="marl_10 left" style="height: 29px; line-height: 29px;">剩余时间：</span>
                            <div class="colockbox" id="colockbox1"><span class="hour">00</span><span class="left">:</span> <span class="minute">00</span> <span class="left">:</span> <span class="second">00</span> </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight"  style="display: none">
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
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')

@stop