@extends('osce::theory.base')

@section('title')
	考题管理
@stop
@section('head_css')
	<style>
		.mar0 { margin: 0;}
		.form-horizontal {float: right; position: relative; margin-right: 20px; overflow: hidden; }
		.import { opacity: 0; filter: alpha(opacity=0); position: absolute; left: -100%; top: 0; width: 200%; height: 100%; outline: none; cursor: pointer; }
		table tbody tr td:last-child { width: 220px;}
		.Fleft { float: left;}
		
		.table td,.table th { min-width: 100px;}
		
		
	</style>
	
		

@stop

@section('head_js')
   <script>

		function deletelist(id) {
			uselayer(2,'确定要删除该考题吗？',function () {
				$('.form-deletelist input').val(id);
				$('.form-deletelist').submit();
			});
		};
		$(function () {
			$('select[name=type]').val({{request()->get('type')}});			
		});
				
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">考题管理</h5>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
   			
			<form action="" method="get" class="left">
                <label for="" class="pull-left Fleft" style="margin-left: 20px;">题目类型：</label>
                <select name="type" class="form-control Fleft" style="width: 150px;">
                    <option value="">全部</option>
                    @foreach($types as $k=>$type)
                    	<option value="{{$k}}">{{$type}}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary marl_10 Fleft" id="search">查询</button>
            </form>
	        
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>题目名称</th>
						<th>类型</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            		@foreach($data->items() as $k=>$val)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$val->question}}</td>
						<td>{{isset($types[$val->type]) ? $types[$val->type]: ''}}</td>
                        <td>{{$val->ctime}}</td>
                        
                        <td>
                            <a class="state1 modal-control" href="{{route('osce.theory.getViewQuestion',['id'=>$val->id])}}">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i> 预览</span>
                            </a>
                            <a class="state1 modal-control" href="{{route('osce.theory.getEditQuestion',['id'=>$val->id])}}">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i> 编辑</span>
                            </a>
                            <a class="state1 modal-control" href="javascript:;" onclick="deletelist({{$val->id}})">
                                <span class="read  state2 detail"><i class="fa fa-trash-o fa-2x"></i> 删除</span>
                            </a>
	                    </td>
                    </tr>
               		@endforeach           		
                </tbody>
            </table>
            <form method="get" class="form-horizontal form-deletelist" action="{{route('osce.theory.getDeleteQuestion')}}">
            	<input type="hidden" name="id" value="" />
            </form>          
		</div>
		<div class="pull-left">
			共{{$data->total()}}条
		</div>
		<div class="btn-group pull-right">
			{!! $data->appends($_GET)->render() !!}
		</div>
	</div>
@stop{{-- 内容主体区域 --}}