@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script src="{{asset('osce/admin/statisticalanalysis/statistics_subject.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>

<script>
    $(function(){
        $('#exam-id').change(function(){
            var examId = $(this).val();
            $.ajax({
                type:'get',
                url:'{{route("osce.admin.SubjectStatisticsController.getSubject")}}',
                data:{exam_id:examId},
                success:function(res){
                    if(res.code!=1){
                        layer.alert(res.message);
                    }else{
                        var data = res.data;
                        var html = '';
                        for(var i in data) {
                            var sign = true;
                            for (var k in data[+i]) {
                                if (k == 'station_id') {
                                    sign = false;   break;
                                }
                            }
                            if (sign) {
                                html += '<option value="' + data[+i].id + '" id="subject">' + data[+i].name + '</option>';
                            } else {
                                html += '<option value="' + data[+i].id + '" id="paper">' + data[+i].name + '</option>';
                            }
                        }
                        $('#subject-id').html(html);
                    }
                },
                error:function(res){
                    layer.alert('通讯失败！')
                }
            });
        });
    })
</script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'examation_statistics','ajaxUrl':'{{ route('osce.admin.SubjectStatisticsController.stationGradeList') }}','target':'{{ route('osce.admin.SubjectStatisticsController.stationDetails') }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">项目成绩分析</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('osce.admin.SubjectStatisticsController.SubjectGradeList')}}">单次考试分析</a></li>
                        <li><a href="{{route('osce.admin.SubjectStatisticsController.SubjectGradeAnalyze')}}">项目成绩趋势</a></li>
                        <li class="active"><a href="{{ route('osce.admin.SubjectStatisticsController.stationGradeList') }}">考站成绩分析</a></li>
                        <li><a href="{{ route('osce.admin.SubjectStatisticsController.standardGradeList') }}">考核点分析</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <div class="input-group" style="margin:20px 0;">
                    <label for="" class="pull-left exam-name">考试名称：</label>
                    <select name="name" id="exam-id" class="input-sm form-control exam_select" style="width: 210px;height: 34px">
                        @foreach($examInfo as $key => $exam)
                            <option value="{{ $exam['id'] }}" {{$key==0?'selected="selected"':''}}>{{ $exam['name'] }}</option>
                        @endforeach
                    </select>
                    <label for="" class="pull-left exam-name" style="margin-left: 20px">项目名称：</label>
                    <select name="name" id="subject-id" class="input-sm form-control subject_select" style="width: 210px;height: 34px">
                        @foreach(@$subjectInfo as $subject)
                            <option value="{{ $subject['id'] }}" id="{{$subject['title'] ? 'subject':'paper'}}" >{{ $subject['title'] ? :$subject['name'] }}</option>
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
                            <th>考站</th>
                            <th>评分老师</th>
                            <th>考试限时</th>
                            <th>平均耗时</th>
                            <th>平均成绩</th>
                            <th>考试人数</th>
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