@extends('osce::admin.layouts.admin_index')

@section('only_css')

    <style>
        body{background-color: #fff!important;}
    </style>
@stop

@section('only_js')
    <script type="text/javascript" src="{{asset('osce/admin/js/all_checkbox.js')}}"> </script>
    <script>
        $(function(){
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
            function checkbox(){

            }
            //点击筛选是查找相关试题
            $('#search').click(function(){
                //获取筛选条件
                var subject_id = $('#status0 option:selected').val();
                var ability_id = $('#status1 option:selected').val();
                var difficult_id = $('#status2 option:selected').val();
                $.ajax({
                    type: "GET",
                    url: "{{route('osce.admin.ExamPaperController.getExamQuestions')}}",
                    data: {subject_id:subject_id,ability_id:ability_id,difficult_id:difficult_id},
                    success: function(msg){
                        if(msg.code){
                            var data = msg.data;
                            var str = '';
                            $(data).each(function(i){
                                str +='<tr><td><label class="check_label checkbox_input"><div class="check_icon">';
                                str +='</div><input type="checkbox" value=""></label></td>';
                                str +='<td>'+this.question_name+'</td>';
                                str +='<td>'+this.label+'</td>';
                                str +='<td>'+this.questtion_type+'</td>';
                                str +='</td></tr>';
                            });
                            console.log(str);
                            $('#subjectBody').html(str);
                        }else{
                            alert('没有数据');
                        }
                    }
                });
            });
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <form class="form-horizontal" method="post" action="{{ route('osce.admin.ApiController.PostEditorExamPaperItem') }}">

        </form>

        <div class="container-fluid ibox-content" style="border: none;">
            <div class="input-group row" style="width: 100%;margin:20px 0;">
                @if(@$labelList)
                    @foreach($labelList as $k=>$label)
                        <div class="form-group col-sm-4">
                            <label class="col-sm-4 control-label">{{@$label['name']}}：</label>
                            <div class="col-sm-8">
                                <select id="status{{$k}}"   class="form-control m-b" name="status2">
                                    <option value="0">全部</option>
                                    @foreach(@$label['label_type_and_label'] as $list)
                                        <option value="{{@$list['id']}}">{{@$list['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="col-sm-3">
                    <button type="submit" class="btn btn-sm btn-primary marl_10" id="search">查询</button>
                </div>
            </div>
            <div class="list_all">
                <table class="table table-striped" id="table-striped" style="background:#fff">
                    <thead>
                    <tr>
                        <th>
                            <label class="check_label all_checked">
                                <div class="check_icon"></div>
                                <input type="checkbox" value="">
                            </label>
                        </th>
                        <th>序号</th>
                        <th>试题</th>
                        <th>考核范围</th>
                        <th>题目类型</th>
                    </tr>
                    </thead>
                    <tbody class="subjectBody">

                    </tbody>
                </table>
                <div class="pull-left">
                    {{--共{{@$data->total()}}条--}}
                </div>
                <div class="pull-right">

                    {{--{!! $data->appends(@$keyword)->render() !!}--}}

                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

