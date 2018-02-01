@extends('osce::theory.base')

@section('title')
	题库管理
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
		$(function () {
			$('select[name=type]').val({{request()->get('type')}});	
			$('select[name=degree]').val({{request()->get('degree')}});	
			$('select[name=require]').val({{request()->get('require')}});	
			$('select[name=lv]').val({{request()->get('lv')}});	
		});
				
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">题库管理</h5>
	        </div>
	        <div class="col-xs-6" style="float: right;">
	            <a  href="{{route('osce.theory.export')}}" class="btn btn-primary" style="float: right;">&nbsp;模版下载&nbsp;</a>
	        	<form method="post" enctype="multipart/form-data" class="form-horizontal form-import" action="{{route('osce.theory.import')}}" >
	        		<input type="file" name="file" onchange="upload()" class="import" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
	        		<a  href="javascript:;" class="btn btn-primary mar0">&nbsp;导入题目&nbsp;</a>
	        	</form>
	        	<a  href="{{route('osce.theory.getAddQuestion')}}" class="btn btn-primary" style=" margin-right: 20px; float: right;">&nbsp;新增题目&nbsp;</a>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
   			
			<form action="" method="get" class="left" style="margin: 10px 20px;">
                <label for="" class="pull-left Fleft">题目类型：</label>
                <select name="type" class="form-control Fleft" style="width: 150px;">
                    <option value="">全部</option>
                    @foreach($model->typeValues as $k=>$type)
                    	<option value="{{$k}}">{{$type}}</option>
                    @endforeach
                </select>
                <label for="" class="pull-left Fleft" style="margin-left: 20px;">难度：</label>
                <select name="degree" class="form-control Fleft" style="width: 100px;">
                    <option value="">全部</option>
                    @foreach($model->degreeValues as $k=>$degree)
                    	<option value="{{$k}}">{{$degree}}</option>
                    @endforeach
                </select>
                <label for="" class="pull-left Fleft" style="margin-left: 20px;">要求度：</label>
                <select name="require" class="form-control Fleft" style="width: 100px;">
                    <option value="">全部</option>
                    @foreach($model->requireValues as $k=>$require)
                    	<option value="{{$k}}">{{$require}}</option>
                    @endforeach
                </select>
                <label for="lv" class="pull-left Fleft" style="margin-left: 20px;">适用层次：</label>
                <select name="lv" class="form-control Fleft" style="width: 100px;">
                    <option value="">全部</option>
                    @foreach($model->lvValues as $k=>$lv)
                    	<option value="{{$k}}">{{$lv}}</option>
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
						<!--<th>医学题型分类</th>-->
						<th>难度</th>
						<th>要求度</th>
						<th>适用层次</th>
						
                        <!--<th>创建时间</th>-->
                        <th>操作</th>
                    </tr>
                </thead>
                
                <tbody>
            		@foreach($data->items() as $k=>$val)
                    <tr>
                        <td>{{$k+1}}</td>
                        <td>{{$val->question}}</td>
						<td>{{$model->typeValues[$val->type] or ''}}</td>
                        <!--<td>{{$val->category}}</td>-->
                        <td>{{$model->degreeValues[$val->degree] or ''}}</td>
                        <td>{{$model->requireValues[$val->require] or ''}}</td>
                        <td>{{$model->lvValues[$val->lv] or ''}}</td>
                        <!--<td>{{$val->ctime}}</td>-->
                        
                        <td>
                            <a class="state1 modal-control" href="{{route('osce.theory.getEditQuestion',['id'=>$val->id,'from'=>'view'])}}">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i> 预览</span>
                            </a>
                            <a class="state1 modal-control" href="{{route('osce.theory.getEditQuestion',['id'=>$val->id,'from'=>'edit'])}}">
                                <span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i> 编辑</span>
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