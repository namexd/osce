@extends('osce::theory.base')

@section('title')
	在线批卷
@stop
@section('head_css')
	<style>
		*,p{ margin: 0; padding: 0;}
		body { background-color:#f3f7f8 ;}
		.cBorder{border: 1px solid #e7eaec;}
		
 .body
{
    float: left;
    /*position: absolute;*/
    width: 95%;
    height: 95%;
    padding: 2.5%;
    text-align: left;
}	
.question_type { margin-bottom: 20px;}
.allSubject { padding: 0 2.5%;}
	
.subjectBox img { display: block; height: auto; max-width: 20%; width: auto;}	
.countdown {text-align: center; width: 800px;padding-left:400px; position: fixed; bottom: 5%; right: 0;}
.type_1 li ,.type_2 li,.type_3 li {cursor: pointer;}
.question_type li { padding: 5px 0;}
.allSubject div { padding: 10px 0 5px;}
#jiaojuan {}

        .colockbox{width:250px;height:30px;overflow: hidden; color:#000;}
        .colockbox span{
            float:left;display:inline-block;
            width:30px;height:29px;
            line-height:29px;font-size:20px;
            font-weight:bold;text-align:center;
            color:#ff0101; margin-right:5px;}
	
	.allSubject label { font-weight: normal;}
	
	.stu_a label,.stu_a span {color: #e86f64; font-weight: bolder;}
	.stu_ra label,.stu_ra span {color: #2d8f7b; font-weight: bolder;}
	.stu_cuo { border: 1px solid #e86f64;}
	.form-control { height: auto;}
	.defen { display: none;}
	.dafen { float: right; width: 200px; padding: 0!important;}
	.dafen strong { float: left; width: 50px; line-height: 34px;}
	.dafen input { width: 150px;}
	.font16 { line-height: 34px;}
	.type_1 .dafen,.type_2 .dafen,.type_3 .dafen { display: none;}
	
	</style>
@stop

<?php $question =collect($data)->groupBy('type');?>


	
@section('head_js')
	<script src="{{ asset('osce/theory/js/exam_online.js') }}"></script>

   <script>
		$(function () {
			var _json = {!! $question->toJson() !!};

			console.log(_json)
				for (var name in _json) {
					var arr = _json[name];
					var str = 
							'<p>'
								+'<label class="font20">'+title[name]+'</label>'
								+'<span style="margin-left: 1em;">共<span>'+arr.length+'</span>题</span>'
							+'</p>';
					
					if (name=='1'||name=='2') {
						str = setdanxuanstr(arr,str);
					}
					if (name=='3') {
						str = setpanduanstr(arr,str);	
					}
					if (name=='4') {
						str = setzhuguan(arr,str);		
					}
					if (name=='5'||name=='6'||name=='7') {
						str = setzhuguan(arr,str);	
					}				
					$('#logid').val(arr[0].logid);
					$('#stuid').val(arr[0].stuid);
					
					$('.step-content').append('<div class="question_type type_'+name+'">'+str+'</div>');	
				}			
				
				$('.type_1 .allSubject').each(function () {
					var _this = $(this);
					$(_this).find('li span').each(function () {
						if ($(this).html()==$(_this).attr('_a')) {
							$(this).parent().parent().find('.radio_icon').addClass('check');
							$(this).parent().parent().addClass('stu_a');
						}
						if ($(this).html()==$(_this).attr('_ra')) {
							$(this).parent().parent().addClass('stu_ra');
						}
					});
					if ($(_this).attr('_ra')!=$(_this).attr('_a')) {
						$(this).addClass('stu_cuo');
					}
				});				
				
				$('.type_2 .allSubject').each(function () {
					var _this = $(this);
					var _a = $(_this).attr('_a');
					var _ra = $(_this).attr('_ra');
					$(_this).find('li span').each(function () {
						for (var i = 0 ; i < _a.length; i++) {
							if ($(this).html()==_a[i]) {
								$(this).parent().parent().find('.radio_icon').addClass('check');
								$(this).parent().parent().addClass('stu_a');
								break;
							}							
						}
						for (var i = 0 ; i < _ra.length; i++) {
							if ($(this).html()==_ra[i]) {
								$(this).parent().parent().addClass('stu_ra');
								break;
							}							
						}
					});
					if ($(_this).attr('_ra')!=$(_this).attr('_a')) {
						$(this).addClass('stu_cuo');
					}
				});	
				
				
				$('.type_3 .allSubject').each(function () {
					var _this = $(this);
					$(_this).find('li span').each(function () {
						if ($(this).html()==$(_this).attr('_a')) {
							$(this).parent().find('.radio_icon').addClass('check');
							$(this).parent().addClass('stu_a');
						}
						if ($(this).html()==$(_this).attr('_ra')) {
							$(this).parent().addClass('stu_ra');
						}
						
					});
					if ($(_this).attr('_ra')!=$(_this).attr('_a')) {
						$(this).addClass('stu_cuo');
					}
				});	
				
				
				$('.type_1 input,.type_2 input,.type_3 input').remove();				
			    $("input.form-control").keyup(function () {
			        this.value = this.value.replace(/[^\d]/g, '');
			    });					
				
				$('#pijuan').click(function () {
					var bOk = true;
					$('input.form-control').each(function () {
						if ($.trim($(this).val())=='') {
							uselayer(3,'还有尚未评分的题目！！');
							$(this).focus();
							bOk = false;
							return false;
						}
						
					});
					if (bOk) {
						uselayer(2,'确定要提交批卷吗？',function () {
							$('.form-shijuan').submit();
						});
					}
					return false;
				});	
				
				
				
			
		});
		
				
		
		
   </script>
@stop

@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">{{request()->get('exam')}} 的理论考试</h5>
	        </div>
	    </div>
		<div class="row">
			<div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$data[0]->examname}}</h2>
                        <span>考生姓名：</span>
                        <span class="checkTime">{{$data[0]->stuname}}</span>
                        <span style="margin-left: 1em;">考试时长：</span>
                        <span class="score">{{$data[0]->times}}分钟</span>
                        <span style="margin-left: 1em;">试卷总分：</span>
                        <span class="score">{{$data[0]->examscore}}分</span>
                    </div>
                </div>
            </div>
			<div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center p-md">
                    	<form method="post" class="form-horizontal form-shijuan p-md cBorder mart_10 clearfix" action="{{route('osce.cexam.modifyresult')}}">
							<div class="step-content body current">
				
								
							</div>
							<input type="hidden" name="logid" id="logid" value="" />
							<input type="hidden" name="stuid" id="stuid" value="" />
							<button class="btn btn-primary" id="pijuan" type="submit">提交批卷</button>
							<button class="btn btn-default" onclick="javascript:history.go(-1);">返回</button>
						</form>
					</div>

                           
                </div>
            </div>            
            
            
			          
            
            			
		</div>
		
		

	</div>

	
@stop{{-- 内容主体区域 --}}











