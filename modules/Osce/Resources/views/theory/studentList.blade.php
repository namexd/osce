@extends('osce::theory.base')

@section('title')
	考生管理
@stop
@section('head_css')
	<style>
		.mar0 { margin: 0;}
		.form-horizontal {float: right; position: relative; margin-right: 20px; overflow: hidden; }
		.import { opacity: 0; filter: alpha(opacity=0); position: absolute; left: -100%; top: 0; width: 200%; height: 100%; outline: none; cursor: pointer; }
		/*table tbody tr td:last-child { width: 220px;}*/
		
		.top-sel .form-control {width: 300px; float: left;}
		.top-sel button { float: left;}
		.delete{ cursor: pointer;}
	</style>
	
		

@stop

@section('head_js')
   <script>
		function upload() {
			var e = e||event;
			var str = e.target.value.substring(e.target.value.lastIndexOf('.')+1,e.target.value.length);
			if (str=='xls'||str=='xlsx') {			
				uploadFile({
					url:'{{route("osce.theory.importStudents")}}',
					json:{
						test_id:"{{request()->get('test_id')}}",
						student:$('.import')[0].files[0]
					},
					fn:function (res) {
						res = JSON.parse(res);
						console.log(res);
						uselayer2(1,res.message,toReload);
					},
					error:function () {
						uselayer2(1,'导入失败！请重试',toReload);
					}
				});
			} else {
				uselayer2(1,'请上传正确的excel文件！')
			}
		};
		$(function () {
			$('.delete').click(function () {
				var _id = $(this).attr('_id');
				uselayer2(2,'确定要删除吗？',function () {
					Api.ajax({
						type:'get',
						url:'{{route("osce.theory.getDelStudent")}}',
						json:{id:_id},
						fn:function (res) {
							uselayer2(3,'删除成功！',toReload);
						}
					});
					
					
				});
			});
		});	
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">考生管理</h5>
	        </div>
	        <div class="col-xs-6" style="float: right;">
	            <a  href="{{asset('download/student.xlsx')}}" class="btn btn-primary" style="float: right;">&nbsp;下载模板&nbsp;</a>
	        	<div class="form-horizontal form-import" >
	        		<input type="file" name="file" onchange="upload()" class="import" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
	        		<a  href="javascript:;" class="btn btn-primary mar0">&nbsp;导入考生&nbsp;</a>
	        	</div>
	        	<a  href="{{route('osce.theory.addStudent',['test_id'=>request()->get('test_id')])}}" class="btn btn-primary" style=" margin-right: 20px; float: right;">&nbsp;新增考生&nbsp;</a>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
    		<form action="" method="get" class="marb_15 top-sel clearfix">
				<input type="text" placeholder="姓名、学号、身份证、电话" class="form-control" name="keyword" value="{{@$keyword}}">
				<button type="submit" class="btn btn-sm btn-primary marl_10">搜索</button>
			</form>
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>考生姓名</th>
                        <th>性别</th>
                        <th>学号</th>
                        <th>身份证号</th>
                        <th>准考证号</th>
                        <th>班级</th>
                        <th>班主任姓名</th>
                        <th>联系电话</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>{{$item->userInfo->gender}}</td>
                            <td>{{$item->code}}</td>
                            <td>{{$item->idcard}}</td>
                            <td>{{$item->exam_sequence}}</td>
                            <td>{{$item->grade_class}}</td>
                            <td>{{$item->teacher_name}}</td>
                            <td>{{$item->mobile}}</td>
                            <td>
                                <a href="{{route('osce.admin.exam.postEditExaminee',['id'=>$item->id])}}" ><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                                <span class="read  state2 delete" _id="{{$item->id}}"><i class="fa fa-trash-o fa-2x"></i></span>
                            </td>
                        </tr>
                    @empty
                    @endforelse
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