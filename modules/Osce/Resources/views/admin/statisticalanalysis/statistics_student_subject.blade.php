@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script src="{{asset('osce/admin/statisticalanalysis/statistics_student.js')}}"></script>
<script>


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
        <div class="ibox-content">
            <span class="student_name">李雷</span>
            <span class="marl_10 student_subject">冠心病问病史</span>
            <span>历史成绩分析</span>
            <button class="btn btn-sm btn-primary marl_10 right">返回</button>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none">
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
                            <th>考试</th>
                            <th>考试时间</th>
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