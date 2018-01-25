<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" ng-app="myApp" class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="cache-control" content="no-cache">
	<title>OSCE考试智能管理系统</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script type="text/javascript" src="{{asset('osce/admin/plugins/js/jquery-2.1.1.min.js')}}" ></script>
	<link rel="stylesheet" type="text/txt" src="{{asset('osce/common/css/bootstrapValidator.css')}}"/>
	<script type="text/javascript" src="{{asset('osce/common/js/bootstrapValidator.js')}}"> </script>

	<style>

		*{outline:none;}
		.hide{display: none}
		body {
			background-color: #364150;
		}
		h1{text-align: center;font-family: 黑体;}
		.box{
			width: 460px;height: 300px;position: absolute;left: 50%;top: 45%;margin-top: -150px;margin-left: -230px;
		}
		select{border-radius: 5px;width: 450px;padding: 10px;font-size: 20px;font-family: 黑体;margin: 10px 0}
		.hide{display: none}
		.sub{
			border-radius: 5px;width: 450px;height: 50px;margin-top: 20px;background: #16BEB0;border: none;
			color: #fff;font-family: 黑体;
			font-size: 20px;

		}
		.red{color: #F53E3F}
		.white{color: #ffffff;}
		.bot{margin-top: 50px;text-align: center;font-family: 黑体;color:#CACACA}
	</style>

	<?php
	$errorsInfo =(array)$errors->getMessages();
	if(!empty($errorsInfo)){
		$errorsInfo = array_shift($errorsInfo);
	}
	?>


</head>
<body>
<div class="box">
	<h1><span class="red">OSCE</span><span class="white">考试智能管理系统</span></h1>
	<div class="m-t">

		<select name="examId" id="examId"  class="input-sm form-control subject_select" style="height: 50px">

			@if(!empty(@$list))

				@foreach(@$list as $val)
					<option value="{{ $val->id }}" >
						{{ $val->exam_id==0?$val->name:$val->exam->name }}的理论排考
					</option>
				@endforeach
			@else
				<option value="">当前没有考试</option>
			@endif
		</select>

		<button  class="btn btn-primary block full-width m-b sub">
			启　动
		</button>
	</div>
	<p class="bot"><i class="">{{--速立达版权所有--}}</i></p>
</div>

<script>
	$(function () {
		$('.sub').click(function () {
			if ($('#examId').val()=='') {
				alert('请选择一场考试后再启动');
				return false;
			} 
			
			
			window.location.href = "{{route('osce.theory.rankStudent')}}"+'?log_id='+$('#examId').val();
			return false;
		});
		
		
		
		
	});

</script>
</body>
</html>