@extends('osce::theory.base')

@section('title')
	试卷预览
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
	.dafen { display: none;}
	.radio_icon { display: none;}
	
	
	</style>
@stop
<?php $question =collect($data->questionHas)->groupBy('type');?>


	
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
					
					$('.step-content').append('<div class="question_type type_'+name+'">'+str+'</div>');	
				}			
				
				$('.type_1 .allSubject').each(function () {
					var _this = $(this);
					$(_this).find('li span').each(function () {
						if ($(this).html()==$(_this).attr('_a')) {
							$(this).parent().parent().addClass('stu_ra');
						}
					});
				});								
				$('.type_2 .allSubject').each(function () {
					var _this = $(this);
					var _a = $(_this).attr('_a');
					$(_this).find('li span').each(function () {
						for (var i = 0 ; i < _a.length; i++) {
							if ($(this).html()==_a[i]) {
								$(this).parent().parent().addClass('stu_ra');
								break;
							}							
						}
					});
				});	
				$('.type_3 .allSubject').each(function () {
					var _this = $(this);
					$(_this).find('li span').each(function () {
						if ($(this).html()==$(_this).attr('_a')) {
							$(this).parent().addClass('stu_ra');
						}
					});
				});	
				
				$('.type_4 .allSubject').each(function () {
					$(this).find('li').eq(0).css('display','none');
					$(this).find('li').eq(1).find('p').html($(this).find('li').eq(1).find('p').html().replace('学生答案：','参考答案：'));
				});	
				$('.type_5 .allSubject,.type_6 .allSubject,.type_7 .allSubject').each(function () {
					$(this).find('li').eq(0).css('display','none');
					$(this).find('li').eq(1).find('p').html($(this).find('li').eq(1).find('p').html().replace('学生答案：','参考评分点：'));
				});	
				
								
			$('.btn-primary').click(function () {
				uselayer(2,'确定要重新生成吗？',function () {
					Api.ajax({
						type:'post',
						url:"{{route('osce.theory.onceautoexam')}}",
						json:{id:findurl('id')},
						fn:function (res) {
							window.location.reload();
						}
					});
				
				});
				return false;
			});			
				
				
			
		});

		function setzhuguan(arr,str) {
			console.log(arr)
			for (var i = 0 ; i < arr.length; i++) {
				str+=
						'<div class="allSubject" _a="'+arr[i].answer+'" _ra="'+arr[i].answer+'">'
						+setnamestr(arr[i],i)
						+'<ul>'
						+'<li class=""><p>'+(arr[i].type==4?'参考答案':'参考评分点')+'：</p><span class="form-control">'+arr[i].answer.replace(/\n/g,"<br/>")+'</span></li>'
						+'<li class=""><p>学生答案：</p><span class="form-control">'+arr[i].answer.replace(/\n/g,"<br/>")+'</span></li>'
						+'</ul>'
						+'<input type="hidden" name="id[]" value="'+arr[i].cid+'" />'
						+'<input type="hidden" name="isright[]" value="3" />'
						+'</div>';
			}
			return str;
		};
		
		
   </script>
@stop

@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">试卷预览</h5>
	        </div>
	    </div>
		<div class="row">
			<div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$data->name}}</h2>
                        <span style="margin-left: 1em;">创建时间：</span>
                        <span class="score">{{$data->ctime}}</span>
                        <span style="margin-left: 1em;">试卷总分：</span>
                        <span class="score">{{$data->score}}分</span>
                    </div>
                </div>
            </div>
			<div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center p-md">
                    	<div class="p-md cBorder mart_10 clearfix">
							<div class="step-content body current">
				
								
							</div>
							@if (request()->get('type')=='add') 
	                        	<button class="btn btn-primary">重新生成</button>
	                       		<a class="btn btn-white" href="{{route('osce.theory.examquestion')}}">返回列表</a>
							@else
								<a class="btn btn-white" onclick="javascript:history.go(-1);">返回</a>
							@endif
						</div>
					</div>

                           
                </div>
            </div>            
            
            
			          
            
            			
		</div>
		
		

	</div>

	
@stop{{-- 内容主体区域 --}}











