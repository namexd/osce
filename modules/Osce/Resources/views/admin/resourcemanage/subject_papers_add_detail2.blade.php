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
            //点击筛选是查找相关试题
            var subject_id = $('#status0 option:selected').val();
            var ability_id = $('#status1 option:selected').val();
            var difficult_id = $('#status2 option:selected').val();
            var page = 1;
            var array = [];//用于存放已选中的checkbook
            var question_type = $('.question_type').val();
            var sequence = $('.sequence').val();
            var question_detail = $('.question_detail').val();
            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
            var str = '';//拼接字符串返回给父页面
            var questionIDs = $('.questionIDs').val();//之前已选中题目ID，但是并未保存
            var questionArr = [];//试题ID转换

            function GetDomInfo(){
                subject_id = $('#status0 option:selected').val();
                ability_id = $('#status1 option:selected').val();
                difficult_id = $('#status2 option:selected').val();
                page = 1;
                array = [];//用于存放已选中的checkbook
                question_type = $('.question_type').val();
                sequence = $('.sequence').val();
                question_detail = $('.question_detail').val();
                index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                str = '';//拼接字符串返回给父页面
                questionIDs = $('.questionIDs').val();//之前已选中题目ID，但是并未保存
                questionArr = [];//试题ID转换
            }

            if(questionIDs){
                var a = questionIDs.split(',');
                for(var i=0;i<a.length;i++){
                    questionArr.push(Number(a[i]));
                }
            }

            //console.log(questionArr);
            $('.form-horizontal').submit(function(){
                getCheckboxVal();
                var detail = question_detail.split('@');
                str = detail[0]+'@'+detail[1]+'@'+array;
                var oneSubject = 0;
                var oneScore = 0;
                parent.$('#paper2 #list-body tr').each(function(){
                    if($(this).attr('sequence') == sequence){
                        parent.$(this).find('input').val(str+'@'+$(this).attr('data'));
                        parent.$(this).children().eq(2).text(array.length);
                        parent.$(this).children().eq(4).text(array.length*parent.$(this).children().eq(3).text());
                    }
                    oneScore += parseInt(parent.$(this).children().eq(4).text());
                    oneSubject += parseInt(parent.$(this).children().eq(2).text());
                    parent.$(".oneScore").text(oneScore);
                    parent.$(".oneSubject").text(oneSubject);
                });
                //parent.$('#list-body').find('tbody').attr('index',now);
                parent.layer.close(index);
                return  false;

            });
            //关闭iframe
            $('#closeIframe').click(function(){
                parent.layer.close(index);
            });
            function checkbox(){

            }

            //筛选事件
            $('#search').click(function(){
                GetDomInfo();
                //获取筛选条件
                getexamquestions(subject_id,ability_id,difficult_id,page,question_type,sequence,questionArr);
            });


            //点击分页事件
            $('.pull-right').delegate('a','click',function(){
                var page = $(this).parents('li').attr('page');
                GetDomInfo();
                getCheckboxVal();
                getexamquestions(subject_id,ability_id,difficult_id,page,question_type,sequence,questionArr);
            });

            //获取列表数据
            function getexamquestions(subject_id,ability_id,difficult_id,page,question_type,sequence,array,questionArr){
                //console.log(array);
                $.ajax({
                    type: "GET",
                    url: "{{route('osce.admin.ExamPaperController.getExamQuestions')}}",
                    data: {subject_id:subject_id,ability_id:ability_id,difficult_id:difficult_id,page:page,question_type:question_type,sequence:sequence,questionArr:questionArr},
                    success: function(msg){
                        if(msg.code){
                            var data = msg.data.data;
                            var pagedata = msg.data;
                            var str = '';

                            $(data).each(function(i){
                                if($.inArray(this.id,array) != -1){
                                    str +='<tr><td><label class="check_label checkbox_input"><div class="check_icon check" data="'+this.id+'">';
                                }else{
                                    if($.inArray(this.id,questionArr) != -1 ){
                                        //alert(1);
                                        str +='<tr><td><label class="check_label checkbox_input"><div class="check_icon check" data="'+this.id+'">';
                                    }else{
//                                        // alert(2);
                                        str +='<tr><td><label class="check_label checkbox_input"><div class="check_icon" data="'+this.id+'">';
                                    }
                                }

                                str +='</div><input type="checkbox" value=""></label></td>';
                                str +='<td>'+(i+1)+'</td>';
                                str +='<td>'+this.question_name+'</td>';
                                str +='<td>'+this.label+'</td>';
                                str +='<td>'+this.questtion_type+'</td>';
                                str +='</td></tr>';
                            });
                            $('.subjectBody').html(str);

                            var pager = createPageDom(pagedata.total,pagedata.per_page,page);
                            $('.pull-right .pagination').html(pager);
                            $('.pull-left').html('共'+pagedata.total+'条');
                        }else{
                            $('.subjectBody').html();
                            alert('没有数据');
                        }
                    }
                });
            }

            //默认加载
            getexamquestions(subject_id,ability_id,difficult_id,page,question_type,sequence,questionArr);

            //ajax分页
            function createPageDom(total,pagesize,page){
                var string = '';
                if(total>0){
                    var sum = Math.ceil(total/pagesize);
                    //TODO 拼凑上一页的按钮
                    if(page == 1){
                        string += '<li class="disabled"><span>«</span></li>';
                    }else{
                        string += '<li rel="prev" page="'+(page-1)+'" ><a href="javascript:void(0)">«</a></li>';
                    }

                    for(var i = 0;i<sum;i++){
                        if(page == (i+1)){
                            string += '<li class="active"><span>'+(i+1)+'</span></li>';
                        }else{
                            string += '<li page="'+(i+1)+'"><a href="javascript:void(0)">'+(i+1)+'</a></li>';
                        }
                    }
                    //TODO 拼凑下一页的按钮
                    if(page == sum){
                        string += '<li class="disabled"><span>»</span></li>';
                    }else{
                        string += '<li rel="next" page="'+(page+1)+'" ><a href="javascript:void(0)">»</a></li>';
                    }
                }
                return  string;
            }

            //获取checkbox选中的值-公用方法
            function getCheckboxVal(){
                $('.check_icon').each(function(){
                    //存储已选中元素
                 if(($(this).attr('class') == 'check_icon check') && ($(this).attr('data') != undefined)){
                     if($.inArray($(this).attr('data'), array) == -1){
                         array.push(Number($(this).attr('data')));
                     }
                 }else{
                     //取消选中时去除数组元素
                        if($.inArray(Number($(this).attr('data')),array) != -1){
                            array.splice($.inArray(Number($(this).attr('data')),array),1);
                        }

                 }

                 });
                 return array;
            }

        })
    </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <form class="form-horizontal" method="post" action="{{ route('osce.admin.ApiController.PostEditorExamPaperItem') }}">

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
                        <button type="button" class="btn btn-sm btn-primary marl_10" id="search">查询</button>
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
                        <ul class="pagination">
                            {{--{!! $data->appends(@$keyword)->render() !!}--}}
                        </ul>


                    </div>
                </div>
                <input type="hidden" class="question_type" value="{{@$question_type}}">
                <input type="hidden" class="sequence" value="{{@$sequence}}">
                <input type="hidden" class="question_detail" value="{{@$question_detail}}">
                <input type="hidden" class="questionIDs" value="{{@$questionIDs}}">
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <a class="btn btn-white" href="#" id="closeIframe">取消</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}

