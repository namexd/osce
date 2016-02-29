@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script src="{{asset('osce/admin/statistics_query/js/statistics_all.js')}}"></script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_statistics','standardStr':'{{ $StrList["standardStr"] }}','scoreAvgStr':'{{ $StrList["scoreAvgStr"] }}','ajaxUrl':'{{ route('osce.admin.SubjectStatisticsController.SubjectGradeList') }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">科目成绩分析</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li><a href="#">科目成绩分析</a></li>
                        <li class="active"><a href="#">科目难度分析</a></li>
                        <li><a href="#">考站成绩分析</a></li>
                        <li><a href="#">考核点分析</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <label for="" class="pull-left exam-name">科目名称：</label>
                <div class="input-group" style="margin:20px 0;">
                    <select name="name" class="input-sm form-control subject_select" style="width: 210px;height: 34px">
                        @foreach(@$examlist as $exam)
                        <option value="{{ $exam['id'] }}">{{ $exam['name'] }}</option>
                        @endforeach
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
                            <th>#</th>
                            <th>考试</th>
                            <th>考试时间</th>
                            <th>平均耗时</th>
                            <th>平均成绩</th>
                            <th>考试人数</th>
                            <th>合格率</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="subjectBody">
                            {{--<tr>--}}
                                {{--<td>--}}
                                    {{--<a href="{{route('osce.admin.course.getStudentDetails',['student_id'=>$item->student_id])}}">--}}
                                        {{--<span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>--}}
                                    {{--</a>--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                        </tbody>
                    </table>
                </div>

            </div>


        </div>


    </div>
@stop{{-- 内容主体区域 --}}