@extends('osce::theory.base')

@section('title')
	题库管理
@stop
@section('head_css')
	<style>
		.mar0 { margin: 0;}
		.form-horizontal {float: right; position: relative; margin-right: 20px; overflow: hidden; }
		.import { opacity: 0; filter: alpha(opacity=0); position: absolute; left: -100%; top: 0; width: 200%; height: 100%; outline: none; cursor: pointer; }
		
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
				$('.form-horizontal').submit();
			} else {
				uselayer(1,'请上传正确的excel文件！')
			}
		};
		
		
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6 col-md-2">
	            <h5 class="title-label">题库管理</h5>
	        </div>
	        <div class="col-xs-6" style="float: right;">
	            <a  href="{{route('osce.theory.export')}}" class="btn btn-primary" style="float: right;">&nbsp;模版下载&nbsp;</a>
	        	<form method="post" enctype="multipart/form-data" class="form-horizontal" action="{{route('osce.theory.import')}}" >
	        		<input type="file" name="file" onchange="upload()" class="import" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
	        		<a  href="javascript:;" class="btn btn-primary mar0">&nbsp;导入试题&nbsp;</a>
	        	</form>
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
            		
                </tbody>
            </table>
            <form method="get" class="form-horizontal" action="{{route('osce.theory.del')}}">
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