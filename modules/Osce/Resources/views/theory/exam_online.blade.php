@extends('osce::theory.base')

@section('title')
	OSCE在线考试系统
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
.type_1 li:hover ,.type_2 li:hover,.type_3 li:hover { background: #f5f5f5;}
.question_type li { padding: 5px 0;}
.allSubject div { padding: 10px 0 5px;}
#jiaojuan {}
.allSubject label { font-weight: normal;}
        .colockbox{width:250px;height:30px;overflow: hidden; color:#000;}
        .colockbox span{
            float:left;display:inline-block;
            width:30px;height:29px;
            line-height:29px;font-size:20px;
            font-weight:bold;text-align:center;
            color:#ff0101; margin-right:5px;}
	
	.dafen,.defen { display: none;}
	 
	
	
	
	.fullimg { width: 100%; height: 100%; overflow: auto; padding-top: 60px; position: fixed; left: 0; top: 0; background: rgba(255,255,255,0.8); z-index: 99;}
	.fullimg img { display: block; margin: 0 auto; position: relative; top: -10px;}
	.fullimg .btn,.fullimg .full-per { position: fixed; top: 8px; left: 50%; margin-left: -23px;}
	.fullimg .btn.full-small { margin-left: -175px;}
	.fullimg .btn.full-big { margin-left: -99px;}
	.fullimg .btn.full-close { margin-left: 63px;}
	.fullimg .full-per { line-height: 34px; width: 76px; text-align: center;}
	</style>
@stop

<?php $question =collect($data->question)->groupBy('type');?>
	
@section('head_js')
	<script src="{{ asset('osce/theory/js/exam_online.js') }}"></script>

   <script>
		$(function () {
			document.oncontextmenu = function () {
				return false;
			};
			$(document).keydown(function (e) {
				if (e.keyCode == 116) {
					return false;
				}
			});
			
			var _json = {!! $question->toJson() !!};
			
			var end = {{$endtime}};
			end--;
			console.log(end)
			var timer = null;
			toTimeDown();
			if (end>0) {
				timer = setInterval(toTimeDown,1000);
			}
			function toTimeDown() {
				end--;
				if (end<=0) {
					uselayer(3,'考试时间到！');
					clearInterval(timer);
					tojiaojuan();
					return false;
				}
				var h = parseInt(end/3600);
				var m = parseInt(end%3600/60);
				$('#hour').html(addzero(h));
				$('#minute').html(addzero(m));
				$('#second').html(addzero(end%60));
			};
			
			var randomarr = function(arr) {
			    return arr.sort(function(){return Math.random() > 0.5});
			};
			

			
			console.log(_json)
			for (var name in _json) {
				var arr = randomarr(_json[name]);
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
					str = settiankongstr(arr,str);		
				}
				if (name=='5'||name=='6'||name=='7') {
					str = setwendastr(arr,str);	
				}				
				
				$('.step-content').append('<div class="question_type type_'+name+'">'+str+'</div>');	
			}			
			
	
			

			
			
			$('.type_1 li,.type_3 li').click(function () {
				if ($(this).find('.radio_icon').hasClass('check')) {
					$(this).find('.radio_icon').removeClass('check')
				} else {
					$(this).parent().find('li .radio_icon').removeClass('check');
					$(this).find('.radio_icon').addClass('check');
				}
			});
			$('.type_2 li').click(function () {
				if ($(this).find('.radio_icon').hasClass('check')) {
					$(this).find('.radio_icon').removeClass('check')
				} else {
					$(this).find('.radio_icon').addClass('check');
				}
			});
			
			$('#jiaojuan').click(function () {
				uselayer(2,'请确认已答完所有题目，你确定交卷吗？',tojiaojuan);
				return false;
			});				
			
			var s = $(window).width()/$(window).height();
			var wh = 'w';	
			var d = 100;
			
			$('.allSubject img').click(function () {
				d = 100;
				wh ='w';
				console.log($(this).width()/$(this).height(),s)
				if ($(this).width()/$(this).height()<s) {
					wh='h';
				}
				
				$('.fullimg img').attr('src',$(this).attr('src'));
				
				setwh(wh,d);
				
				$('body').addClass('overflow');
				$('.fullimg').removeClass('hide');
			});
			
			$('.full-close').click(function () {
				$('body').removeClass('overflow');
				$('.fullimg').addClass('hide');
				
			});
			$('.full-big').click(function () {
				if (d>500) {
					return false;
				}
				d+=50;
				setwh(wh,d);
			});
			$('.full-small').click(function () {
				if (d<100) {
					return false;
				}
				d-=50;
				setwh(wh,d);
			});
			
			
			function setwh(wh,d) {
				var w = 'width';
				if (wh=='h') {
					w = 'height';
				}
				console.log(w)
				$('.full-per').html(d+'%');
				$('.fullimg img').attr(w,d+'%');
			};
				
		});
		
		
		
		
		
		
   </script>
@stop

@section('body')
	<div class="fullimg hide">
		<img src="" />
		<button class="btn btn-sm btn-default2 full-small">缩小</button>
		<button class="btn btn-sm btn-primary full-big">放大</button>
		<span class="full-per"></span>
		<button class="btn btn-sm btn-warning full-close">关闭</button>
		
	</div>
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">OSCE在线考试系统</h5>
	        </div>
	    </div>
		<div class="row">
			<div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$data->exam->name}}</h2>
                        <span>考试时间：</span>
                        <span class="checkTime">{{$data->times}}分钟</span>
                        <span style="margin-left: 1em;">总分：</span>
                        <span class="score">{{$data->test->score}}分</span>
                    </div>
                </div>
            </div>
			<div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center p-md">
                    	<form method="post" class="form-horizontal form-shijuan p-md cBorder mart_10 clearfix" action="{{route('osce.cexam.addstudentresult')}}">
							<div class="step-content body current">
				
								
							</div>
							<input type="hidden" name="logid" value="{{$data->id}}" />
							<button class="btn btn-primary" id="jiaojuan" type="submit">提交试卷</button>
						</form>
					</div>

                           
                </div>
            </div>            
            
            
			          
            
            			
		</div>
		
		

	</div>

	<div class="btnBox left countdown">
        <span class="marl_10 left" style="display:inline-block; height: 29px; width:100px; line-height: 29px;">剩余时间：</span>
        <div class="colockbox" id="colockbox1">
            <span class="hour" id="hour">00</span><span class="left">:</span>
            <span class="minute" id="minute">00</span><span class="left">:</span>
            <span class="second" id="second">00</span>
        </div>
    </div> 	
	
@stop{{-- 内容主体区域 --}}











