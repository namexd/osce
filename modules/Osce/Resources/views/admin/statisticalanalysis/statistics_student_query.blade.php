@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style>
        body{background-color: #fff!important;}
        .check_name{margin-left:5px}
        .check_label{margin-left:48px;}
        .group_border{border-bottom:1px solid #e7eaec}
    </style>
@stop

@section('only_js')
<script type="text/javascript">
    $(function(){
        //radio实现单选效果
        $("input[type=radio]").click(function () {
             $("input[type=radio]").removeAttr('checked');
             $(this).prop("checked",true);
        });
    })


</script>
@stop

@section('content')
<!--理论考试展示页面-->
    <div class="row table-head-style1" style="border-bottom:1px solid  #e7eaec">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考生成绩统计统计</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a href="javascript:history.go(-1)" class="btn btn-outline btn-default" style="float: right;">返回</a>
        </div>
    </div>
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="center">
            @if(!empty($examItems))
            <h2>{{ @$examItems['exam_name'] }}</h2>
            <p>考试姓名：<span>张三</span>　　　考试用时：<span>{{ @$examItems['length'] }}</span>分<span>{{ @$examItems['total_score'] }}</span>秒 　　　最后得分：<span>{{ @$examItems['stuScore'] }}</span>分</p>
            @endif
        </div>


                <div class="form-group marb_25">
                     @if(!empty($data))
                         @foreach(@$data as $val)
                            @if(@$val['questionType']=='1')
                            <h3>{{@$val['Title']}}</h3>

                            <div class="form-group group_border">
                                @foreach(@$val['child'] as $val1)
                                    <h4>{{ @$val1['exam_question_name'] }}</h4>
                                    @foreach(@$val1['contentItem'] as $k=>$val2)

                                    <span class="marr_15">

                                        <label class="check_label all_checked " style="margin:10px">
                                            @if($val1['answer']==$k)
                                                <div class="check_icon check " style="float:left"></div>
                                            @else
                                                <div class="check_icon  " style="float:left"></div>
                                            @endif
                                            <input type="checkbox"  value="">
                                            <span class="check_name" style="float:left">{{ @$val2}}</span>

                                        </label>

                                    </span>

                                    @endforeach

                                 <div class="text">
                                    @if(!empty($val1['parsing']))
                                    <p>考生答案：<span style="color:#ed5565">D</span>（A）</p>
                                    <p>{{$val1['parsing']}}</p>
                                    @endif
                                 </div>
                                    　
                            @endforeach
                            </div>


                            @endif
                @endforeach
                @endif
                </div>

    </div>
@stop{{-- 内容主体区域 --}}

