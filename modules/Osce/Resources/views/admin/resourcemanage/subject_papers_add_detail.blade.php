@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .select2-container--open{ z-index: 10000;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.min.js')}}"></script>
    <script>
        $(function(){
            $(".tag").select2({});
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">新增试题组成</h5>
            </div>
        </div>
        <div class="ibox-content">
            <form class="form-horizontal" method="post" action="{{ route('osce.admin.ApiController.PostEditorExamPaperItem') }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">题目类型：</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="question-type">
                                @if(!empty($examQuestionTypeList))
                                    @foreach($examQuestionTypeList as $key => $val)
                                        <option value="{{ @$val['id'] }}">{{@$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                   @if($examQuestionLabelTypeList)
                    @foreach($examQuestionLabelTypeList as $k =>$sub)
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{@$sub['name']}}：</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="label-{{ @$sub['id'] }}">
                                    <option value="0">包含</option>
                                    <option value="1">等于</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select class="form-control tag" name="tag[]" multiple="multiple" style="width: 100%">
                                    @if(!empty($sub['examQuestionLabel']))
                                        @foreach($sub['examQuestionLabel'] as $key => $val)
                                            <option value="{{ @$val['id'] }}">{{@$val->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    @endforeach
                    @endif
                    <div class="form-group">
                        <label class="col-sm-3 control-label">题目数量：</label>
                        <div class="col-sm-9">
                            <input name="question-number" type="number" class="form-control" placeholder="仅支持大于0的正整数" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">每题分数：</label>
                        <div class="col-sm-9">
                            <input name="question-score"  type="number"  class="form-control" placeholder="仅支持大于0的正整数" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success" id='sure'>确定</button>
                    <button type="button" class="btn btn-white" data-dismiss="modal" aria-hidden="true">取消</button>
                </div>
            </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

