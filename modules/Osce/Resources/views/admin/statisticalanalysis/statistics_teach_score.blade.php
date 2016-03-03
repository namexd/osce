@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script src="{{asset('osce/admin/statisticalanalysis/statistics_teach.js')}}"></script>
<script>


</script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'statistics_teach_score'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">教学成绩分析</h5>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content">
                <div class="input-group" style="margin:20px 0;">
                    <label for="" class="pull-left exam-name">考试名称：</label>
                    <select name="name" class="input-sm form-control exam_select" style="width: 210px;height: 34px">
                        @if(!empty($examlist))
                            @foreach(@$examlist as $exam)
                        <option value="{{ $exam['id'] }}">{{ $exam['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                    <label for="" class="pull-left exam-name" style="margin-left: 20px;">科目名称：</label>
                    <select name="name" class="input-sm form-control student_select" style="width: 210px;height: 34px">

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
                            <th>老师</th>
                            <th>班级</th>
                            <th>人数</th>
                            <th>平均成绩</th>
                            <th>最高分</th>
                            <th>最低分</th>
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