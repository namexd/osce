@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<script>
    $(function() {
        var h = echarts.init(document.getElementById("echarts-Subject")),
                d = {

                    tooltip: {
                        trigger: "axis"
                    },
                    legend: {
                        orient: "vertical",
                        x: "right",
                        y: "bottom",
                        data: ["考生成绩", "平均分"]
                    },
                    polar: [{
                        indicator: [{
                            text: "冠心病问病史",
                            max: 100
                        },
                            {
                                text: "肠胃炎问病史",
                                max: 100
                            },
                            {
                                text: "发热咳嗽问病史",
                                max: 100
                            },
                            {
                                text: "体格检查",
                                max: 100
                            },
                            {
                                text: "无菌操作",
                                max: 100
                            },
                            {
                                text: "心血管疾病",
                                max: 100
                            }]
                    }],
                    calculable: !0,
                    series: [{
                        name: "预算 vs 开销",
                        type: "radar",
                        data: [{
                            value: [50, 60, 70, 80, 90, 85],
                            name: "考生成绩"
                        },
                        {
                            value: [65, 75, 36, 85, 36, 86],
                            name: "平均分"
                        }]
                    }]
                };
        h.setOption(d);
    })


</script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'statistics_student_subject','ajaxUrl':'{{ route('osce.admin.SubjectStatisticsController.stationGradeList') }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考生成绩分析</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="/osce/admin/testscores/test-score-list">考生成绩分析</a></li>
                        <li><a href="/osce/admin/testscores/student-subject-list">考生科目分析</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <div class="input-group" style="margin:20px 0;">
                    <label for="" class="pull-left exam-name">考生名称：</label>
                    <select name="name" class="input-sm form-control student_select" style="width: 210px;height: 34px">

                        <option value=""></option>

                    </select>
                    <label for="" class="pull-left exam-name" style="margin-left: 20px;">考试名称：</label>
                    <select name="name" class="input-sm form-control exam_select" style="width: 210px;height: 34px">

                        <option value=""></option>

                    </select>
                    <button type="submit" class="btn btn-sm btn-primary marl_10" id="search">搜索</button>
                </div>
                <div class="list_all">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content" style="border: none;">
                                <div class="echarts" id="echarts-Subject"></div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped" id="table-striped" style="background:#fff">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>科目</th>
                            <th>考试限时</th>
                            <th>平均耗时</th>
                            <th>平均成绩</th>
                            <th>用时</th>
                            <th>成绩</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="subjectBody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}