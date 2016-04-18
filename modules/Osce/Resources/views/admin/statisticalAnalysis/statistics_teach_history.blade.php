@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
    <script src="{{asset('osce/admin/statisticalanalysis/statistics_teach.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
<script>


</script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'teach_detail','classData':'{{@$data['classData']}}','allData':'{{@$data['allData']}}','timeData':'{{@$data['timeData']}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">教学成绩分析</h5>
            </div>
        </div>
        <div class="ibox-content">
            <span class="student_name">{{@$classId}}</span>
            <span class="marl_10 student_subject">{{@$subname}}</span>
            <span>历史成绩分析</span>
            <button class="btn btn-sm btn-primary marl_10 right" id="back">返回</button>
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
                            <th>班级平均成绩</th>
                            <th>考试平均成绩</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="subjectBody">
                        @if(!empty(@$data['datalist']))
                            @foreach(@$data['datalist'] as $k=>$v)
                                <tr>
                                    <td>{{@$k+1}}</td>
                                    <td>{{@$v['name']}}</td>
                                    <td>{{@$v['avgScore']}}</td>
                                    <td>{{@$v['AllavgScore']}}</td>
                                    <td>
                                        <span class="read state1 detail cursor"><i class="fa fa-search fa-2x" examid="{{@$v['id']}}" resultid="{{@$classId}}" subid="{{@$v['sid']}}"></i></span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}