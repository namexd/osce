@extends('osce::admin.layouts.admin_index')

@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/admin/subjectManage/subject_manage.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_check_tag','delUrl':'{{ route('osce.admin.ExamLabelController.getDeleteExamQuestionLabel') }}'}" />
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
                        <li><a href="{{route('osce.admin.ExamQuestionController.showExamQuestionList')}}">题库管理</a></li>
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
                        <input type="text" placeholder="请输入标签名称" name="tagName" @if(!empty($keyWords)) value="{{$keyWords}}" @endif class="input-md form-control" style="width: 250px;">
                        <label for="" class="pull-left exam-name" style="margin-left: 20px;">标签类型：</label>
                        <select name="tagType" id="tagType" class="input-sm form-control subject_select" style="width: 250px;height: 34px">
                            <option value="" @if(empty($id) ) selected="selected" @endif>全部</option>
                            @if(!empty(@$ExamQuestionLabelTypeList))
                                @foreach(@$ExamQuestionLabelTypeList as $val)
                                    <option value="{{ $val['id'] }}" @if(!empty($id)&& $val['id']==$id) selected="selected" @endif>
                                        {{ $val['name'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary marl_10" id="search">查询</button>
                    </form>
                    <button class="btn btn-sm btn-primary marl_10 right" id="add" data-toggle="modal" data-target="#myModal">新增</button>
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
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#myModal" class="edit" dataId="{{ $val['id'] }}">
                                                <span class="read state1 detail">
                                                    <i class="fa fa-pencil-square-o fa-2x"></i>
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
                    <div class="pull-left">共{{$datalist->total()}}条</div>
                    <div class="btn-group pull-right">
                        {!! $datalist->appends($_GET)->render() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')
    {{--新增表单--}}
    <form class="form-horizontal" id="addForm" novalidate="novalidate" method="post" action="{{ route('osce.admin.ExamLabelController.postAddExamQuestionLabel') }}">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增考核标签</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot" style="color: #ed5565">*</span>标签名称：</label>
                <div class="col-sm-9">
                    <input type="text" name="name" class="form-control" placeholder="最多输入10个字">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">标签类型：</label>
                <div class="col-sm-9">
                    <select name="label_type_id" id="typeSelect" class="form-control">
                        @if(!empty(@$ExamQuestionLabelTypeList))
                            @foreach(@$ExamQuestionLabelTypeList as $val)
                                <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot" style="color: #ed5565">*</span>描述：</label>
                <div class="col-sm-9">
                    <input type="text" name="describe" class="form-control add_des">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id='sure'>确定</button>
            <button type="button" class="btn btn-white" data-dismiss="modal" aria-hidden="true">取消</button>
        </div>
    </form>
    {{--编辑表单--}}
    <form class="form-horizontal" id="editForm" novalidate="novalidate" method="post" action="{{ route('osce.admin.ExamLabelController.editExamQuestionLabelInsert') }}">
        <input type="hidden" class="edit_id" name="id">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">编辑考核标签</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot" style="color: #ed5565">*</span>标签名称：</label>
                <div class="col-sm-9">
                    <input type="text" name="name" class="form-control edit_name" placeholder="最多输入10个字">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">标签类型：</label>
                <div class="col-sm-9">
                    <select name="label_type_id" id="" class="form-control edit_type">
                        @if(!empty(@$ExamQuestionLabelTypeList))

                            @foreach(@$ExamQuestionLabelTypeList as $val)
                                <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="dot" style="color: #ed5565">*</span>描述：</label>
                <div class="col-sm-9">
                    <input type="text" name="describe" class="form-control edit_des">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id='editSure'>确定</button>
            <button type="button" class="btn btn-white" data-dismiss="modal" aria-hidden="true">取消</button>
        </div>
    </form>
@stop