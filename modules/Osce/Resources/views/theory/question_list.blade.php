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
	</style>
	
		

@stop

@section('head_js')
   <script>
		function upload() {
			var e = e||event;
			var str = e.target.value.substring(e.target.value.lastIndexOf('.')+1,e.target.value.length);
			if (str=='xls'||str=='xlsx') {
				layer.load(0, {
					shade: [0.3,'#fff'] //0.1透明度的白色背景
				});				
				$('.form-import').submit();
			} else {
				uselayer(1,'请上传正确的excel文件！')
			}
		};
		function deletelist(id) {
			uselayer(2,'确定要删除该考题吗？',function () {
				$('.form-deletelist input').val(id);
				$('.form-deletelist').submit();
			});
		};		
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