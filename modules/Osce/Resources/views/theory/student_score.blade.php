@extends('osce::theory.base')

@section('title')
	成绩查询
@stop


@section('head_js')
   <script>
		
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">{{$data[0]->logdata->exam->name or ''}} 的理论考试</h5>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>学生姓名</th>
                        <th>考试时长</th>
                        <th>试卷总分</th>
                        <th>客观题得分</th>
                        <th>主观题得分</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            		@foreach($data->items() as $k=>$val)
                    <tr>
                        <td >{{$k+1}}</td>
                        <td >{{$val->student->name}}</td>
                        <td >{{$val->logdata->times}}</td>
                        <td >{{$val->logdata->test->score}}</td>  
                        <td >{{$val->objective}}</td>
                        <td >{{$val->subjective}}</td> 
                        
                        <td>
                            <a class="state1 modal-control" href="{{route('osce.cexam.searchexamdetail',['logid'=>$val->logid,'userid'=>$val->stuid,'exam'=>$val->logdata->exam->name])}}" onclick="deletelist({{$val->id}})">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i>查看详细</span>
                            </a>
	                    </td>
                    </tr>
               		@endforeach
                </tbody>
            </table>
		</div>
		<div class="pull-left">
			共{{$data->total()}}条
		</div>
		<div class="btn-group pull-right">
			{!! $data->appends($_GET)->render() !!}
		</div>
	</div>
@stop{{-- 内容主体区域 --}}