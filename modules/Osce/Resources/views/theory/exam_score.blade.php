@extends('osce::theory.base')

@section('title')
	成绩管理
@stop


@section('head_js')
   <script>
		
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">考试列表</h5>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>考试名称</th>
                        <th>试卷</th>
                        <th>监考老师</th>
                        <th>开始时间</th>
                        <th>结束时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            		@foreach($data->items() as $k=>$val)
                    <tr>
                        <td >{{$k+1}}</td>
                        <td >{{$val->exam->name}}的理论考试</td>
                        <td >{{$val->test->name}}</td>
                        <td >{{$val->teacherdata->name}}</td>
                        <td >{{$val->start}}</td>  
                        <td >{{$val->end}}</td>                  
                        
                        <td>
                            <a class="state1 modal-control" href="{{route('osce.theory.studentscore',['id'=>$val->id])}}" onclick="deletelist({{$val->id}})">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i>查看成绩</span>
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