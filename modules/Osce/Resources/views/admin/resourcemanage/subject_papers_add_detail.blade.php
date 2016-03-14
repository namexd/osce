@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style>
        body{background-color: #fff!important;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.min.js')}}"></script>
    <script>
        $(function(){
            $(".tag").select2({});

            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
            $('.form-horizontal').submit(function(){
                $.post($(this).attr('action'),$(this).serialize(),function(obj){
                    /*给父页面传值*/
                    var objvar=obj.toString().split("@");
                    var typeall=objvar[1];
                    var  tpye;//题目类型
                    $('select[name="question-type"]').find('option').each(function(){
                        if($(this).val()==typeall[0]){
                            tpye=$(this).text();
                        }
                    });
                    var typeall=typeall.split(",");
                    var  questionnum=parseInt(typeall[1]);//题目数量
                    var  questionscore=parseInt(typeall[2]);//题目分数
                    var now = parent.$('#list-body').attr('index');
                    now = parseInt(now) + 1;
                    var html = '<tr>'+
                            '<td>'+parseInt(now)+'<input name="question[]" type="hidden" value="'+obj+'"/>'+'</td>'+
                            '<td>'+tpye+'</td>'+
                            '<td>'+ objvar[0]+'</td>'+
                            '<td>'+questionnum+'</td>'+
                            '<td>'+questionscore+'</td>'+
                            '<td>'+questionnum*questionscore+'</td>'+
                            '<td>'+
                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>'+
                            '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                            '</td>'+
                            '</tr>';
                    //记录计数
                    parent.$('#list-body').append(html);
                    parent.$('#list-body').find('tbody').attr('index',now);
                    parent.layer.close(index);
                })
                return  false;

            })
            //关闭iframe
            $('#closeIframe').click(function(){
                parent.layer.close(index);
            });


        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
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
                @if(@$examQuestionLabelTypeList)
                    @foreach(@$examQuestionLabelTypeList as $k =>$sub)
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{@$sub['name']}}：</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="label-{{ @$sub['id'] }}">
                                    <option value="1">包含</option>
                                    <option value="2">等于</option>
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
                <button type="button" class="btn btn-white" id="closeIframe">取消</button>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}



