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
                    $("#status2").empty().append('<option value="1">随机试卷</option><option value="2">统一试卷</option>')
                }else{
                    $("#paper2").show();
                    $("#paper").hide();
                    $("#status2").empty().append('<option value="2">统一试卷</option>')
                }
            });
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
                    content: '{{route('osce.admin.ApiController.GetEditorExamPaperItem')}}'
                })

            });
            $('#paper tbody').on('click','.fa-pencil-square-o',function(){
                var question_detail=$(this).parent().parent().parent().parent().find("input[name='question[]']").val();
                var ordinal = $(this).parent().parent().parent().parent().attr("ordinal");
                layer.open({
                    type: 2,
                    title: '编辑试题组成',
                    area: ['90%', '600px'],
                    fix: false, //不固定
                    maxmin: true,
                    content: '{{route('osce.admin.ApiController.GetEditorExamPaperItem')}}?question_detail='+question_detail+"&ordinal="+ordinal
                })
            });
            /**
             * 手动组卷情况下现则试题
             */
//            统一试卷总计封装
            function editOneCount(){
                var oneSubject = 0;
                var oneScore = 0;
                $('#paper2 tbody').find("tr").each(function(){
                    oneScore += parseInt($(this).children().eq(4).text());
                    oneSubject += parseInt($(this).children().eq(2).text());
                });
                $(".oneScore").text(oneScore);
                $(".oneSubject").text(oneSubject);
            }
            //自动组卷封装
            function randomCount(){
                var randomSubject = 0;
                var randomScore = 0;
                $('#paper #list-body').find("tr").each(function(){
                    randomSubject += parseInt($(this).children().eq(3).text());
                    randomScore += parseInt($(this).children().eq(5).text());
                });
                $(".randomSubject").text(randomSubject);
                $(".randomScore").text(randomScore);
            }
            $('#addForm').submit(function(){//添加题型
                var now = 0;
                var length = $('#list-body tr').length;//修改时获取tr数量
                if(length){
                    now = length;
                }else{
                    now = $('#paper2').find('tbody').attr('index');
                }

                now = parseInt(now) + 1;//计数
                var tpye2= $('select[name="question-type"] option:selected').text();//题目类型名字
                var tpyeid= $('select[name="question-type"] option:selected').val();//题目类型ID
                var score=$('input[name="question-score"]').val(); //每题分数
                var html = '<tr sequence="'+parseInt(now)+'" id="handwork_'+parseInt(now)+'">'+
                        '<td>'+parseInt(now)+'<input name="question-type[]" type="hidden" value="'+tpyeid+"@"+score+'"/>'+'</td>'+
                        '<td>'+tpye2+'</td>'+
                        '<td>0</td>'+
                        '<td>'+score+'</td>'+
                        '<td>0</td>'+
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
                editOneCount();
                return  false;
            });

            $('#paper2 tbody').on('click','.fa-cog',function(){//添加题目
                var  sequence=  $(this).parent().parent().parent().parent().attr("sequence");
                var question_detail=$(this).parent().parent().parent().parent().find("input[name='question-type[]']").val();
                var geturl='{{route('osce.admin.ExamPaperController.getExampQuestions')}}?question_detail='+question_detail+"&sequence="+sequence;
                layer.open({
                    type: 2,
                    title: '新增试题组成',
                    area: ['90%', '600px'],
                    fix: false, //不固定
                    maxmin: true,
                    content:geturl
                })
            });
            // 添加新题型
            $("#add-new2").click(function(){
                $("#addForm").show();
                $("#editForm").hide();
                $("#addForm")[0].reset();
            });
            // 编辑题型
            $('#paper2 tbody').on('click','.fa-pencil-square-o',function(){
                $("#addForm").hide();
                $("#editForm").show();
               var nowid= $(this).parent().parent().parent().parent().attr("id");
                var question_detail=$(this).parent().parent().parent().parent().find("input[name='question-type[]']").val();
                question_detail=question_detail.split("@");
                $('#typeSelect2').find('option').each(function(){
                    if($(this).val()==question_detail[0]){
                        $(this).attr("selected", true);
                    }
                });
                $('input[name="question-score2"]').val(question_detail[1]);
                $('#editForm').submit(function(){//编辑题型
                    var new_question_detail="";
                    for(var i=0; i<question_detail.length; i++){
                        if(i==1){
                            question_detail[1]=$('input[name="question-score2"]').val(); //修改每题分数重置
                        }
                        if(i==question_detail.length-1){
                            new_question_detail=new_question_detail+question_detail[i];
                        }else{
                            new_question_detail=new_question_detail+question_detail[i]+"@";
                        }
                    }
                    $("#"+nowid).children().find("input[name='question-type[]']").val(new_question_detail);
                    $("#"+nowid).children().eq(3).text(question_detail[1]);
                    $("#"+nowid).children().eq(4).text(question_detail[1]*$("#"+nowid).children().eq(2).text());
                    $('.close').trigger('click');
                    editOneCount();
                    return  false;
                })

            });

            /**
             * 手工组卷的删除
             */
            $('#paper2 tbody').on('click','.fa-trash-o',function(){
                $(this).parent().parent().parent().parent().remove();
                editOneCount();
            });
            /**
             * 自动组卷的删除
             */
            $('#paper tbody').on('click','.fa-trash-o',function(){
                $(this).parent().parent().parent().parent().remove();
                randomCount();
            });

            /**
             * 预览整套试卷
             */
            $('#preview').click(function(){
                layer.open({
                    type: 2,
                    title: '新增试题组成',
                    area: ['90%', '600px'],
                    fix: false, //不固定
                    maxmin: true,
                    content: '{{route('osce.admin.ApiController.ExamPaperPreview')}}?'+$(".form-horizontal").serialize()
                });
                return  false;

            })
}
        $(function(){
            categories();
            if($("#status").val()=="1"){
                $("#paper").show();
                $("#paper2").hide();
                var randomSubject = 0;
                var randomScore = 0;
                $('#paper #list-body').find("tr").each(function(){
                    randomSubject += parseInt($(this).children().eq(3).text());
                    randomScore += parseInt($(this).children().eq(5).text());
                });
                $(".randomSubject").text(randomSubject);
                $(".randomScore").text(randomScore);
            }else{
                $("#paper2").show();
                $("#paper").hide();
                var oneSubject = 0;
                var oneScore = 0;
                $('#paper2 tbody').find("tr").each(function(){
                    oneScore += parseInt($(this).children().eq(4).text());
                    oneSubject += parseInt($(this).children().eq(2).text());
                });
                $(".oneScore").text(oneScore);
                $(".oneSubject").text(oneSubject);

            }

            //当页面加载完，获取第一个input的值，判断是否是修改
            var inputVal = $('#name').val();

            var editUrl = '{{route('osce.admin.ExamPaperController.getEditExamPaper')}}';
            if(inputVal){
                $('#sourceForm').attr('action',editUrl);
                $('.status').attr('disabled',true);
                $('.status2').attr('disabled',true);
            }
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
                    <label class="col-sm-2 control-label"><span class="dot" style="color: #ed5565;">*</span>试卷名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name" value="{{@$paperDetail['name']}}">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="dot" style="color: #ed5565;">*</span>考试时长</label>
                    <div class="col-sm-10">
                        <input type="text"  class="form-control" id="code" name="time" value="{{@$paperDetail['length']}}">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">组卷方式</label>
                    <div class="col-sm-10">
                        <select id="status"   class="form-control m-b" name="status">
                            <option value="1" @if(@$paperDetail['mode'] == 1)selected="selected" @endif >自动组卷</option>
                            <option value="2" @if(@$paperDetail['mode'] == 2)selected="selected" @endif >手工组卷</option>
                        </select>
                    </div>
                    <input type="hidden" name="status" value="{{@$paperDetail['mode']}}">
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">试卷类型</label>
                    <div class="col-sm-10">
                        <select id="status2" class="form-control m-b" name="status2">
                            <option value="1" @if(@$paperDetail['type'] == 1)selected="selected" @endif >随机试卷</option>
                            <option value="2" @if(@$paperDetail['type'] == 2)selected="selected" @endif >统一试卷</option>
                        </select>
                    </div>
                    <input type="hidden" name="status2" value="{{@$paperDetail['type']}}">
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

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>总计</td>
                                            <td></td>
                                            <td></td>
                                            <td class="randomSubject">0</td>
                                            <td></td>
                                            <td class="randomScore">0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
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
                                    @if(!empty(@$paperDetail['item']))
                                        @foreach(@$paperDetail['item'] as $k=>$detail)
                                            <tr sequence="{{@$k+1}}" id="handwork_{{@$k+1}}" data="{{@$detail['id']}}">
                                                <td>{{@$k+1}}<input name="question-type[]" type="hidden" value="{{@$detail['type'].'@'.@$detail['score'].'@'.@$detail['child'].'@'.@$detail['id']}}"/></td>
                                                <td>{{@$detail['typename']}}</td>
                                                <td>{{@$detail['num']}}</td>
                                                <td>{{@$detail['score']}}</td>
                                                <td>{{@$detail['total_score']}}</td>
                                                <td>
                                                    <a href="javascript:void(0)"><span class="read  state1 detail"><i data-toggle="modal" data-target="#myModal" class="fa fa-pencil-square-o fa-2x"></i></span></a>
                                                    <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa  fa-cog fa-2x"></i></span></a>
                                                    <a href="javascript:void(0)"><span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>总计</td>
                                            <td></td>
                                            <td class="oneSubject">0</td>
                                            <td></td>
                                            <td class="oneScore">0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>


                    </div>
                </div>
                {{--修改时，存试卷paperID--}}
                <input type="hidden" name="paperid" value="{{@$paperDetail['id']}}">
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <button class="btn btn-primary" id="preview" type="button">预览</button>
                        <a class="btn btn-white" href="{{route('osce.admin.ExamPaperController.getExamList')}}">取消</a>
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
                <label class="col-sm-3 control-label">题目类型：</label>
                <div class="col-sm-9">
                    <select name="question-type" id="typeSelect" class="form-control">
                        @if(!empty($ExamQuestionLabelTypeList))
                            @foreach($ExamQuestionLabelTypeList as $key => $val)
                                <option value="{{ @$val['id'] }}">{{@$val['name']}}</option>
                            @endforeach
                        @endif
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
                <label class="col-sm-3 control-label">题目类型：</label>
                <div class="col-sm-9">
                    <select name="question-type2" id="typeSelect2" class="form-control" disabled>
                        @if(!empty($ExamQuestionLabelTypeList))
                            @foreach($ExamQuestionLabelTypeList as $key => $val)
                                <option value="{{ @$val['id'] }}">{{@$val['name']}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">每题分数：</label>
                <div class="col-sm-9">
                    <input type="text" name="question-score2"  class="form-control" placeholder="仅支持大于0的正整数">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-success" id='editSure'>确定</button>
            <button type="button" class="btn btn-white" data-dismiss="modal" aria-hidden="true">取消</button>
        </div>
    </form>
@stop

