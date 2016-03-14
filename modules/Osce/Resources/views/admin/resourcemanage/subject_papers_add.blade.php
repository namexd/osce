@extends('osce::admin.layouts.admin_index')

@section('only_css')
@stop

@section('only_js')
    <script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
    <script>
        function categories(){
            $('#submit-btn').click(function(){
                var flag = null;
                $('tbody').find('.col-sm-10').each(function(key,elem){
                    flag = true;
                    if($(elem).find('input').val()==''){
                        flag = false;
                    }
                });
                if(flag==false){
                    layer.alert('题型不能为空！');
                    return false;
                }
                if(flag==null){
                    layer.alert('请新增题型！');
                    return false;
                }
            });
            /**
             * 手工、自动组卷划分
             */
            $("#status").change(function(){
                if($(this).val()=="1"){
                    $("#paper").show();
                    $("#paper2").hide();
                    $("#status2").removeAttr("disabled");
                }else{
                    $("#paper2").show();
                    $("#paper").hide();
                    $("#status2 option[text='统一试卷']").attr("selected", true);
                    $("#status2").attr("disabled","disabled");
                }
            })
            /**
             * 自动组卷页面操作
             */
            $("#add-new").click(function(){
                layer.open({
                    type: 2,
                    title: '新增试题组成',
                    area: ['90%', '530px'],
                    fix: false, //不固定
                    maxmin: true,
                    content: '{{route('osce.admin.ApiController.GetEditorExamPaperItem')}}',
                })

            })
            $('tbody').on('click','.fa-pencil-square-o',function(){
                $("#addForm").hide();
                $("#editForm").show();
                $(this).parent().parent().parent().parent().attr("sequence");
            });
            $('#addForm').submit(function(){
                var now = $('#paper2').find('tbody').attr('index');
                now = parseInt(now) + 1;//计数
                var tpye= $('select[name="question-type"] option:selected').text();//题目类型ID
                var score=$('input[name="question-score"]').val(); //每题分数
                var html = '<tr sequence="'+parseInt(now)+'">'+
                        '<td>'+parseInt(now)+'<input name="question-type[]" type="hidden" value="'+$(this).serialize()+'"/>'+'</td>'+
                        '<td>'+tpye+'</td>'+
                        '<td></td>'+
                        '<td>'+score+'</td>'+
                        '<td></td>'+
                        '<td>'+
                        '<a href="javascript:void(0)"><span class="read  state1 detail"><i data-toggle="modal" data-target="#myModal" class="fa fa-pencil-square-o fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa  fa-cog fa-2x"></i></span></a>'+
                        '<a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>'+
                        '</td>'+
                        '</tr>';
                //记录计数
                $('#paper2').find('tbody').append(html);
                $('#paper2').find('tbody').attr('index',now);
                $('.close').trigger('click');
                return  false;
            })

            /**
             * 删除
             */
            $('tbody').on('click','.fa-trash-o',function(){
                $(this).parent().parent().parent().parent().remove();
            });
            /**
             * 手动组卷情况下现则试题
             */
            $('tbody').on('click','.fa-cog',function(){
                layer.open({
                    type: 2,
                    title: '新增试题组成',
                    area: ['90%', '530px'],
                    fix: false, //不固定
                    maxmin: true,
                    content: '{{route('osce.admin.ExamPaperController.getExampQuestions')}}?'+$(".form-horizontal").serialize(),
                })
            });
            // 添加新题型
            $("#add-new2").click(function(){
                $("#addForm").show();
                $("#editForm").hide();
            })
            // 编辑题型
            $('tbody').on('click','.fa-pencil-square-o',function(){
                $("#addForm").hide();
                $("#editForm").show();
                $(this).parent().parent().parent().parent().attr("sequence");
            });

            /**
             * 预览整套试卷
             */
            $('#preview').click(function(){
                layer.open({
                    type: 2,
                    title: '新增试题组成',
                    area: ['90%', '530px'],
                    fix: false, //不固定
                    maxmin: true,
                    content: '{{route('osce.admin.ApiController.ExamPaperPreview')}}?'+$(".form-horizontal").serialize(),
                })
                return  false;

            })
}
        $(function(){
            categories();
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">新增试卷</h5>
            </div>
        </div>
        <div class="ibox-content">
            <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.ExamPaperController.getAddExams')}}">
                <div class="form-group">
                    <label class="col-sm-2 control-label">试卷名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">考试时长</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="code" name="time">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">组卷方式</label>
                    <div class="col-sm-10">
                        <select id="status"   class="form-control m-b" name="status">
                            <option value="1">自动组卷</option>
                            <option value="2">手工组卷</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">试卷类型</label>
                    <div class="col-sm-10">
                        <select id="status2"   class="form-control m-b" name="status2">
                            <option value="1">随机试卷</option>
                            <option value="2">统一试卷</option>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">评分标准</label>
                    <div class="col-sm-10">
                        <div class="ibox float-e-margins" id="paper">
                            <div class="ibox-title" style="border-top:0;">
                                <h5></h5>
                                <div class="ibox-tools">
                                    <button type="button" class="btn btn-outline btn-default" id="add-new" >新增题型</button>
                                </div>
                            </div>
                            <div class="ibox-content" style="border-top:0;" >
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="10%">序号</th>
                                        <th width="20%">题目类型</th>
                                        <th>考核范围</th>
                                        <th width="10%">题目总量</th>
                                        <th width="10%">每题分数</th>
                                        <th width="10%">总分</th>
                                        <th width="10%">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody index="0" id="list-body">
                                        <tr>
                                            <th>总计</th>
                                            <th></th>
                                            <th></th>
                                            <th>40</th>
                                            <th>-</th>
                                            <th>20</th>
                                            <th></th>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="ibox float-e-margins" id="paper2"  style="display: none;">
                            <div class="ibox-title" style="border-top:0;">
                                <h5></h5>
                                <div class="ibox-tools">
                                    <button type="button" class="btn btn-outline btn-default" id="add-new2" data-toggle="modal" data-target="#myModal" >新增题型</button>
                                </div>
                            </div>
                            <div class="ibox-content" style="border-top:0;" id="paper2" >
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="10%">序号</th>
                                        <th width="20%">题目类型</th>
                                        <th width="10%">题目总量</th>
                                        <th width="10%">每题分数</th>
                                        <th width="10%">总分</th>
                                        <th width="10%">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody index="0" id="list-body">
                                    <tr>
                                        <th>总计</th>

                                        <th></th>
                                        <th>40</th>
                                        <th>-</th>
                                        <th>20</th>
                                        <th></th>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>


                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <button class="btn btn-primary" id="preview" type="button">预览</button>
                        <a class="btn btn-white" href="{{route("osce.admin.machine.getMachineList",["cate_id"=>2])}}">取消</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')
    {{--手工组卷状态下新增试题类型--}}
    <form class="form-horizontal" id="addForm" novalidate="novalidate" method="post" action="{{ route('osce.admin.ExamLabelController.postAddExamQuestionLabel') }}">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">新增试题组成</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">标签类型：</label>
                <div class="col-sm-9">
                    <select name="question-type" id="typeSelect" class="form-control">
                        @if(!empty(@$ExamQuestionLabelTypeList))
                            @foreach(@$ExamQuestionLabelTypeList as $val)
                                <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                        @endif
                            <option value="1">单选题</option>
                            <option value="2">多选题</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">每题分数：</label>
                <div class="col-sm-9">
                    <input type="text" name="question-score" class="form-control" placeholder="仅支持大于0的正整数">
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
            <h4 class="modal-title" id="myModalLabel">编辑试题组成</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">标签类型：</label>
                <div class="col-sm-9">
                    <select name="question-type" id="typeSelect" class="form-control">
                        @if(!empty(@$ExamQuestionLabelTypeList))
                            @foreach(@$ExamQuestionLabelTypeList as $val)
                                <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                        @endif
                            <option value="1">单选题</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">每题分数：</label>
                <div class="col-sm-9">
                    <input type="text" name="question-score" class="form-control" placeholder="仅支持大于0的正整数">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id='editSure'>确定</button>
            <button type="button" class="btn btn-white" data-dismiss="modal" aria-hidden="true">取消</button>
        </div>
    </form>
@stop

