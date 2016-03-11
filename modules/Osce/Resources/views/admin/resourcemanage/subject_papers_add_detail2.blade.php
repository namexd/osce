@extends('osce::admin.layouts.admin_index')

@section('only_css')

    <style>
        body{background-color: #fff!important;}
    </style>
@stop

@section('only_js')
    <script type="text/javascript" src="js/all_checkbox.js"> </script>
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
        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <form class="form-horizontal" method="post" action="{{ route('osce.admin.ApiController.PostEditorExamPaperItem') }}">

        </form>

        <div class="container-fluid ibox-content" style="border: none;">
            <div class="input-group" style="width: 100%;margin:20px 0;">
                <label for="" class="pull-left exam-name">科目标签：</label>
                <select class="form-control" name="label-{{ @$sub['id'] }}">
                    <option value="0">全部</option>
                    <option value="1">基础医学</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary marl_10" id="search">查询</button>
                <button type="submit" class="btn btn-sm btn-primary marl_10 pull-right" id="add"><a href="{{route('osce.admin.ExamPaperController.getAddExamPage')}}"> 新增</a></button>
            </div>
            <div class="list_all">
                <table class="table table-striped" id="table-striped" style="background:#fff">
                    <thead>
                    <tr>
                        <th></th>
                        <th>序号</th>
                        <th>试题</th>
                        <th>考核范围</th>
                        <th>题目类型</th>
                        <th>试卷类型</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody class="subjectBody">
                    @if(!empty(@$data))
                        @foreach(@$data as $k=>$val)
                            <tr>
                                <td>{{@$k+1}}</td>
                                <td>{{@$val['name']}}</td>
                                <td>{{@$val['num']}}</td>
                                <td>{{@$val['total_score']}}</td>
                                @if($val['type']===1)
                                    <td>随机试卷</td>
                                @else
                                    <td>统一试卷</td>
                                @endif
                                <td>
                                    <a href="javascript:void(0)">
                                        <span class="read state1 detail">
                                            <i class="fa fa-cog fa-2x"></i>
                                        </span>
                                    </a>
                                    <a href="">
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

