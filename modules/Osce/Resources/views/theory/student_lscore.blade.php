@extends('osce::theory.base')

@section('title')
	理论考试成绩查询
@stop

@section('head_css')
    <style>
        table tbody tr td:last-child{
            width:auto;
        }
    </style>
@stop
@section('head_js')
   <script>
		
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">理论考试成绩查询</h5>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
			<div class="panel-options">
                <ul class="nav nav-tabs" style="margin-left: 0">
					<li><a href="{{route('osce.admin.geExamResultList')}}">技能考试成绩</a></li>
                    <li class="active"><a href="{{route('osce.theory.studentlscore')}}">理论考试成绩</a></li>
                </ul>
            </div>
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>考试名称</th>
                        <th>试卷名称</th>
                        <th>考生姓名</th>
                        <th>客观题得分</th>
                        <th>主观题得分</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            		@foreach($data as $k=>$val)
                    <tr>
                        <td >{{$k+1}}</td>
                        <td >{{$val->name}}的理论考试</td>
                        <td >{{$val->name}}</td>
                        <td >{{$val->stuname}}</td>
                        <td >{{$val->objective}}</td>
                        <td >{{$val->subjective}}</td>                
                        
                        <td>
                            <a class="state1 modal-control" href="{{route('osce.cexam.searchexamdetail',['logid'=>$val->logid,'userid'=>$val->user_id])}}">
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