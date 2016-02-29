@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <!-- ECharts -->
    <script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<script>


</script>
@stop


@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">教学成绩分析</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#">班级成绩分析</a></li>
                        <li><a href="#">成绩提升分析</a></li>
                    </ul>
                </div>
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
                            <th>老师</th>
                            <th>班级</th>
                            <th>人数</th>
                            <th>平均成绩</th>
                            <th>最高分</th>
                            <th>最低分</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="">
                                    <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}