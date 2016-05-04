<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" ng-app="myApp" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" ng-app="myApp" class="no-js"> <!--<![endif]-->
<head>
 <meta charset="utf-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <title>OSCE考试智能管理系统</title>
 <meta name="description" content="">
 <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="{{asset('osce/admin/plugins/css/bootstrap.min.css?v=3.4.0')}}" rel="stylesheet">
	<link href="{{asset('osce/admin/css/animate.css')}}" rel="stylesheet">
	<link href="{{asset('osce/admin/css/style.css')}}" rel="stylesheet">
 	<link rel="stylesheet" type="text/css" href="{{asset('osce/admin/css/login.css')}}"/>
	<link href="{{asset('osce/admin/css/style.css')}}" rel="stylesheet">
	<script type="text/javascript" src="{{asset('osce/admin/plugins/js/jquery-2.1.1.min.js')}}" ></script>
	<link rel="stylesheet" type="text/txt" src="{{asset('osce/common/css/bootstrapValidator.css')}}"/>
	<script type="text/javascript" src="{{asset('osce/common/js/bootstrapValidator.js')}}"> </script>
	@section('only_css')
	<style>
		.hide{display: none}

	</style>
	@stop
	<?php
		$errorsInfo =(array)$errors->getMessages();
		if(!empty($errorsInfo)){
			$errorsInfo = array_shift($errorsInfo);
		}
	?>


</head>
<body>
		<div class="middle-box loginscreen animated fadeInDown">
			<div style="background:#fff;margin-top:60px;padding:20px;border-top:3px solid #1dc5a3;">
				<div class="logo">
					<img src="{{asset('osce/images/logo.png')}}" width="100%"/>
				</div>
				<form class="m-t" role="form"  onsubmit="return jufe()" method="get" action="{{ route('osce.doorplate.getdoorplatemsg') }}">

					<select name="examId" id="examId"  class="input-sm form-control subject_select" style="height: 34px">

						@if(!empty(@$examList))

							@foreach(@$examList as $val)
								<option value="{{ $val['exam_id'] }}" selected="selected" >
									{{ $val['name'] }}
								</option>
							@endforeach
						@else
							<option value="">当前没有考试</option>
						@endif
					</select>
					<p style="display: block;margin-top: 5px;margin-bottom: 10px;color: red" class="hide exam">　请选择考试</p>
					<h3 class="tt">

					</h3>
					<select name="roomId" id="roomId" class="input-sm form-control subject_select" style="height: 34px">
						<option value=''>请选择考场</option>
						@if(!empty(@$roomList))
							@foreach(@$roomList as $val)
								<option value="{{ $val[0] }}" >
									{{ $val[1] }}
								</option>
							@endforeach
						@endif
					</select>
					<p style="display: block;margin-top: 5px;margin-bottom: 10px;color: red" class="hide room" >　请选择考场</p>
					@forelse($errorsInfo as $errorItem)
						<div class="pnotice" style="display: block;color: red">{{$errorItem}}</div>
					@empty
					@endforelse
					<button type="submit"  class="btn btn-primary block full-width m-b">
						启动
					</button>
				</form>
			</div>
		</div>

		<script>

			var m='';
			$('#examId').change(function(){
				var examId= $.trim($(this).val())
				var opstr='<option value="1">请选择考场</option>';
				if(examId!=''){

					$.ajax({
						type: "GET",
						url: "{{route('osce.api.LoginPullDown.getRoomList')}}",
						data: {'exam_id':examId},
						success: function(msg){
							if(msg){
								$(msg.data).each(function(i,k){
									m=eval(k)
									opstr += '<option value="'+ m[0]+'">'+m[1]+'</option>　';
								});
								$('#roomId').html(opstr);
							}
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