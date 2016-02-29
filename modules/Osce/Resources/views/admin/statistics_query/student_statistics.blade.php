@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<script>

    $(function(){
                var standardStr= "{{ @$StrList["standardStr"]  }}";
                standardStr=standardStr.split(",");
                var timeAvgStr= "{{ @$StrList["timeAvgStr"]  }}";
                timeAvgStr=timeAvgStr.split(",");
                var scoreAvgStr= "{{ @$StrList["scoreAvgStr"]  }}";
                scoreAvgStr=scoreAvgStr.split(",");
        var t = echarts.init(document.getElementById("echarts-bar-chart")),
                n = {
                    title: {
                        text: "科目成绩分析"
                    },
                    tooltip: {
                        trigger: "axis"
                    },
                    legend: {
                        data: ["平均耗时", "平均成绩"]
                    },
                    calculable: !0,
                    xAxis: [{
                        type: "category",
                        data: standardStr,
                    }],
                    yAxis: [{
                        type: "value"
                    }],
                    series: [{
                        name: "平均耗时",
                        type: "bar",
                        data: timeAvgStr,
                        
                    },
                        {
                            name: "平均成绩",
                            type: "bar",
                            data: scoreAvgStr,

                        }]
                };
        t.setOption(n);
    })
</script>
@stop


@section('content')
    <div class="container-fluid ibox-content">
        <div class="panel-heading">
            <div class="panel-options">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#">考生成绩分析</a></li>
                    <li><a href="#">考生科目分析</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">科目成绩分析</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox-content">
                    <div class="echarts" id="echarts-bar-chart"></div>
                </div>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content">
                <table class="table table-striped" id="table-striped" style="background:#fff">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>科目</th>
                            <th>考试限时</th>
                            <th>平均耗时</th>
                            <th>平均成绩</th>
                            <th>考试人数</th>
                            <th>合格率</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty(@$list))
                    @foreach(@$list as $item)
                        <tr>
                            <td>{{@$item->subjectId}}</td>
                            <td>{{@$item->title}}</td>
                            <td>{{@$item->mins}}</td>
                            <td>{{@$item->mins}}</td>
                            <td>{{@$item->scoreAvg}}</td>
                            <td>{{@$item->studentQuantity}}</td>
                            <td>{{@$item->qualifiedPass}}</td>
                            <td>
                                <a href="{{route('osce.admin.course.getStudentDetails',['student_id'=>@$item->student_id])}}">
                                    <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@stop{{-- 内容主体区域 --}}