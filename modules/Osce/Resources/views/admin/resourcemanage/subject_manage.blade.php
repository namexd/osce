@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/admin/subjectManage/subject_manage.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_manage','delUrl':'{{ route('osce.admin.ExamQuestionController.examQuestionDelete') }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">题库管理</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content" style="padding-bottom: 0;">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li><a href="{{route('osce.admin.ExamLabelController.getExamLabel')}}">考核标签</a></li>
                        <li class="active"><a href="{{route('osce.admin.ExamQuestionController.showExamQuestionList')}}">题库管理</a></li>
                        <li><a href="{{route('osce.admin.ExamPaperController.getExamList')}}">试卷管理</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <div class="input-group" style="width: 100%;margin:20px 0;">
                    <form action="" method="get" class="left">
                        <label for="" class="pull-left exam-type">试题类型：</label>
                        <select name="examType" id="examType" class="input-sm form-control exam_select" style="width: 250px;height: 34px">

                        </select>
                        <label for="" class="pull-left subject-type" style="margin-left: 20px;">题目类型：</label>
                        <select name="subjectType" id="subjectType" class="input-sm form-control subject_select" style="width: 250px;height: 34px">

                        </select>
                        <button type="submit" class="btn btn-sm btn-primary marl_10" id="search">搜索</button>
                    </form>
                    <button class="btn btn-sm btn-primary marl_10 right" id="add" data-toggle="modal" data-target="#myModal">新增</button>
                </div>
                <div class="list_all">
                    <table class="table table-striped" id="table-striped" style="background:#fff">
                        <thead>
                            <tr>
                                <th>序号</th>
                                <th>试题</th>
                                <th>考核范围</th>
                                <th>题目类型</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody class="subjectBody">
                            @if(!empty(@$list))
                                @foreach(@$list as $val)
                                    <tr>
                                        <td>{{ $val['number'] }}</td>
                                        <td>{{ $val['name'] }}</td>
                                        <td>{{ $val['examQuestionLabelName'] }}</td>
                                        <td>{{ $val['examQuestionTypeName'] }}</td>
                                        <td>
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#myModal" class="edit" dataId="{{ $val['id'] }}">
                                                <span class="read state1 detail">
                                                    <i class="fa fa-cog fa-2x"></i>
                                                </span>
                                            </a>
                                            <a href="javascript:void(0)" class="delete" dataId="{{ $val['id'] }}">
                                                <span class="read state2">
                                                    <i class="fa fa-trash-o fa-2x"></i>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <div class="pull-left">共10条</div>
                    <div class="btn-group pull-right">
                        <ul class="pagination">
                            <li class="disabled"><span>«</span></li>
                            <li class="active"><span>1</span></li>
                            <li><a href="">2</a></li>
                            <li><a href="" rel="next">»</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')

@stop