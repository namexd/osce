@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/admin/subjectManage/check_tag.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_check_tag','addUrl':'{{ route('osce.admin.ExamLabelController.addExamQuestionLabel') }}'}" />
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
                        <li class="active"><a href="{{route('osce.admin.ExamLabelController.getExamLabel')}}">考核标签</a></li>
                        <li><a href="">题库管理</a></li>
                        <li><a href="{{route('osce.admin.ExamPaperController.getExamList')}}">试卷管理</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content" style="border: none;">
                <div class="input-group" style="width: 100%;margin:20px 0;">
                    <form action="" method="get" class="left">
                        <label for="" class="pull-left exam-name">标签名称：</label>
                        <input type="text" placeholder="请输入标签名称" name="tagName" class="input-md form-control" style="width: 250px;">
                        <label for="" class="pull-left exam-name" style="margin-left: 20px;">标签类型：</label>
                        <select name="tagType" id="tagType" class="input-sm form-control subject_select" style="width: 250px;height: 34px">
                            @if(!empty(@$ExamQuestionLabelTypeList))
                                @foreach(@$ExamQuestionLabelTypeList as $val)
                                    <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary marl_10" id="search">查询</button>
                    </form>
                    <button class="btn btn-sm btn-primary marl_10 right" id="add">新增</button>
                </div>
                <div class="list_all">
                    <table class="table table-striped" id="table-striped" style="background:#fff">
                        <thead>
                            <tr>
                                <th>序号</th>
                                <th>标签</th>
                                <th>标签类型</th>
                                <th>描述</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody class="tagBody">
                            @if(!empty(@$datalist))
                                @foreach(@$datalist as $k=>$val)
                                    <tr>
                                        <td>{{ $k+1 }}</td>
                                        <td>{{ $val['name'] }}</td>
                                        <td>{{ $val['LabelType'] }}</td>
                                        <td>{{ $val['describe'] }}</td>
                                        <td>
                                            <a href="javascript:void(0)" class="edit" dataId="{{ $val['id'] }}">
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