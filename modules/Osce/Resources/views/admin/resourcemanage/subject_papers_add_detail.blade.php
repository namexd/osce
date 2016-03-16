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
            //自动组卷总计
            function randomCount(){
                var randomSubject = 0;
                var randomScore = 0;
                parent.$('#paper #list-body').find("tr").each(function(){
                    randomSubject += parseInt($(this).children().eq(3).text());
                    randomScore += parseInt($(this).children().eq(5).text());
                });
                parent.$(".randomSubject").text(randomSubject);
                parent.$(".randomScore").text(randomScore);
            }
            var flag = false;
            $('.form-horizontal').submit(function(){
                var questionNumber = $(".questionNumber").val();
                var questionScore = $(".questionScore").val();
                if(questionNumber >= 1 && questionNumber <= 100 && questionScore >= 1 && questionScore <= 20){
                    flag = true;
                }else{
                    flag = false;
                }
                if(flag){
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
                        var ordinal = parseInt($("#ordinal").val());
                        if(ordinal > 0 ){
                            parent.$('#paper #list-body').find("tr").each(function(){
                                if($(this).attr("ordinal") == ordinal){
                                    $(this).html('<td>'+ordinal+'<input name="question[]" type="hidden" value="'+obj+'"/>'+'</td>'+
                                            '<td>'+tpye+'</td>'+
                                            '<td>'+ objvar[0]+'</td>'+
                                            '<td>'+questionnum+'</td>'+
                                            '<td>'+questionscore+'</td>'+
                                            '<td>'+questionnum*questionscore+'</td>'+
                                            '<td>'+
                                            '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>'+
                                            '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                                            '</td>');
                                }
                            });
                        }else{
                            var html = '<tr ordinal="'+now+'">'+
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
                            parent.$('#list-body').attr('index',now);
                        }
                        randomCount();
                        parent.layer.close(index);
                    });
                }
                return  false;
            });
            //关闭iframe
            $('#closeIframe').click(function(){
                parent.layer.close(index);
            });
            autoValidate();
            //表单验证
            function autoValidate(){
                $(".form-horizontal").bootstrapValidator({
                    message: 'This value is not valid',
                    feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {/*验证*/
                        'tag[]': {
                            validators: {
//                                notEmpty: {/*非空提示*/
//                                    message: '标签不能为空'
//                                },
                                callback: {
                                    message: '请至少选择一个标签',
                                    callback:function(){
                                        if($(".tag option:selected").length > 0){
                                            return true;
                                        }else{
                                            return false;
                                        }

                                    }
                                }
                            }
                        },
                        questionNumber: {/*键名username和input name值对应*/
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {/*非空提示*/
                                    message: '题目数量不能为空'
                                },
                                regexp: {
                                    regexp : /^([1-9]\d?|100)$/,
                                    message: '题目数量只能是1-100的正整数'
                                }
                            }
                        },
                        questionScore: {/*键名username和input name值对应*/
                            message: 'The username is not valid',
                            validators: {
                                notEmpty: {/*非空提示*/
                                    message: '每题分数不能为空'
                                },
                                callback: {
                                    message: '每题分数只能是1-20的正整数',
                                    callback:function(){
                                        if($(".questionScore").val() >= 1 && $(".questionScore").val() <= 20){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    }
                                }
                            }
                        }
                    }
                })
            }
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <input type="hidden" id="ordinal" value="{{ @$ordinal }}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <form class="form-horizontal" method="post" action="{{ route('osce.admin.ApiController.PostEditorExamPaperItem') }}">
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">题目类型：</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="question-type">
                            @if(!empty($examQuestionTypeList))
                                @foreach($examQuestionTypeList as $key => $val)
                                    <option value="{{ @$val['id'] }}" @if(@$questionInfo['type'] == @$val['id']) selected @endif>{{@$val['name']}}</option>
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
                                    <option value="1"
                                            @if(!empty($sub['examQuestionLabelSelectedList']))
                                                @foreach($sub['examQuestionLabelSelectedList'] as $k => $v)
                                                    @if('1' == $v['relation'])
                                                        selected
                                                    @endif
                                                @endforeach
                                            @endif
                                            >包含</option>
                                    <option value="2"
                                            @if(!empty($sub['examQuestionLabelSelectedList']))
                                                @foreach($sub['examQuestionLabelSelectedList'] as $k => $v)
                                                    @if('2' == $v['relation'])
                                                        selected
                                                    @endif
                                                @endforeach
                                            @endif
                                            >等于</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select class="form-control tag" name="tag[]" multiple="multiple" style="width: 100%">
                                    @if(!empty($sub['examQuestionLabel']))
                                        @foreach($sub['examQuestionLabel'] as $key => $val)
                                            <option value="{{ @$val['id'] }}"
                                            @if(!empty($sub['examQuestionLabelSelectedList']))
                                                @foreach($sub['examQuestionLabelSelectedList'] as $k => $v)
                                                    @if($val['id'] == $v['exam_question_label_id'])
                                                            selected
                                                    @endif
                                                @endforeach
                                            @endif
                                                >
                                                {{@$val->name}}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot" style="color: #ed5565">*</span>题目数量：</label>
                    <div class="col-sm-9">
                        <input name="questionNumber" type="number" class="form-control questionNumber" value="{{@$questionInfo['num']}}" placeholder="仅支持大于0的正整数" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><span class="dot" style="color: #ed5565">*</span>每题分数：</label>
                    <div class="col-sm-9">
                        <input name="questionScore"  type="number"  class="form-control questionScore" value="{{@$questionInfo['score']}}" placeholder="仅支持大于0的正整数" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" id='sure' disabled>确定</button>
                <button type="button" class="btn btn-white" id="closeIframe">取消</button>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}



