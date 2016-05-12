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
	<form class="m-t" role="form"  onsubmit="return jufe()" method="get" action="{{ route('osce.doorplate.getdoorplatemsg') }}">

		<select name="examId" id="examId"  class="input-sm form-control subject_select" style="height: 50px">

			@if(!empty(@$examList))

				@foreach(@$examList as $val)
					<option value="{{ $val['exam_id'] }}" >
						{{ $val['name'] }}
					</option>
				@endforeach
			@else
				<option value="">当前没有考试</option>
			@endif
		</select>
		<p style="margin-top: 5px;margin-bottom: 10px;color: red" class="hide exam">　请选择考试</p>
		<h3 class="tt">

		</h3>
		<select name="roomId" id="roomId" class="input-sm form-control subject_select" style="height: 50px">
			<option value=''>请选择考场</option>
			@if(!empty(@$roomList))
				@foreach(@$roomList as $val)
					<option value="{{ $val['id'] }}" >
						{{ $val['name'] }}
					</option>
				@endforeach
			@endif
		</select>
		<p style="margin-top: 5px;margin-bottom: 10px;color: red" class="hide room" >　请选择考场</p>
		@forelse($errorsInfo as $errorItem)
			<div class="pnotice" style="display: block;color: red">{{$errorItem}}</div>
		@empty
		@endforelse
		<div class="pnotice" id="error" style="display: none;color: red">数据加载中...</div>
		<div class="pnotice" id="error1" style="display: none;color: red"></div>
		<button type="submit"  class="btn btn-primary block full-width m-b sub">
			启　动
		</button>
	</form>
	<p class="bot"><i class="">敏行医学版权所有</i></p>
</div>

<script>

	var m='';
	$('#examId').change(function(){
		$('#error1').css('display','none');
		var examId= $.trim($(this).val())
		var opstr='<option value="">请选择考场</option>';
		if(examId!=''){
			$('#error').css('display','block');

			$.ajax({
				type: "GET",
				url: "{{route('osce.api.LoginPullDown.getRoomList')}}",
				data: {'exam_id':examId},
				success: function(msg){
					$('#error').css('display','none');
					if(msg){
						$(msg.data).each(function(i,k){
							opstr += '<option value="'+ k.id+'">'+ k.name+'</option>　';
						});
						$('#roomId').html(opstr);
					}
				},
				error:function(e,code,msg){
					$('#error1').css('display','block').text(code+'-'+msg);
				}
			});

		}else{
			$('#roomId').html('<option value="">没有相关房间信息</option>');
		}

	})
	$('#roomId').change(function(){
		$('.pnotice').css('display','none')
	})
	function jufe(){
		$('.exam').addClass('hide');
		$('.room').addClass('hide');
		var exam= $.trim($('#examId').val());
		var room= $.trim($('#roomId').val());

		if(exam==''){
			$('.exam').removeClass('hide')
			return false;
		}
		if(room==''){
			$('.room').removeClass('hide')
			return false;
		}
		return true;
	}
</script>
</body>
</html>