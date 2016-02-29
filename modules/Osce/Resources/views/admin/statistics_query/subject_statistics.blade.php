@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script src="{{asset('osce/admin/statistics_query/js/statistics_all.js')}}"></script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_statistics','standardStr':'{{ $StrList["standardStr"] }}','scoreAvgStr':'{{ $StrList["scoreAvgStr"] }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">科目成绩分析</h5>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content">
                <div class="input-group" style="width: 290px;margin:20px 0;">
                    <input type="text" name="name" placeholder="请输入科目名称" class="input-sm form-control" value="">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-primary" id="search">搜索</button>
                </span>
                </div>
                <div class="list_all">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="ibox-content">
                                <div class="echarts" id="echarts-Subject"></div>
                            </div>
                        </div>
                    </div>
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
                        @forelse($list as $item)
                            <tr>
                                <td>{{$item->subjectId}}</td>
                                <td>{{$item->title}}</td>
                                <td>{{$item->mins}}</td>
                                <td>{{$item->mins}}</td>
                                <td>{{$item->scoreAvg}}</td>
                                <td>{{$item->studentQuantity}}</td>
                                <td>{{$item->qualifiedPass}}</td>
                                <td>
                                    <a href="{{route('osce.admin.course.getStudentDetails',['student_id'=>$item->student_id])}}">
                                        <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>
                                    </a>
                                </td>
                            </tr>
                        @empty

                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>


        </div>


    </div>
@stop{{-- 内容主体区域 --}}