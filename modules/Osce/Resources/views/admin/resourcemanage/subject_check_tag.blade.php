@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/admin/subjectManage/check_tag.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_check_tag'}" />
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
                    <label for="" class="pull-left exam-name">标签名称：</label>
                    <input type="text" placeholder="请输入标签名称" name="tagName" class="input-md form-control" style="width: 250px;">
                    <label for="" class="pull-left exam-name" style="margin-left: 20px;">标签类型：</label>
                    <select name="name" id="subject-id" class="input-sm form-control subject_select" style="width: 250px;height: 34px">
                        <option value="">难度标签</option>
                        <option value="">能力标签</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary marl_10" id="search">查询</button>
                    <button type="submit" class="btn btn-sm btn-primary marl_10 pull-right" id="add">新增</button>
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
                        <tbody class="subjectBody">
                            <tr>
                                <td>1</td>
                                <td>基础医学</td>
                                <td>科目标签</td>
                                <td>哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</td>
                                <td>
                                    <a href="javascript:void(0)">
                                        <span class="read state1 detail">
                                            <i class="fa fa-cog fa-2x"></i>
                                        </span>
                                    </a>
                                    <a href="javascript:void(0)">
                                        <span class="read state2">
                                            <i class="fa fa-trash-o fa-2x"></i>
                                        </span>
                                    </a>
                                </td>
                            </tr>
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