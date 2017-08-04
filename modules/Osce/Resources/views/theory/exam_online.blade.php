@extends('osce::theory.base')

@section('title')
	OSCE在线考试系统
@stop
@section('head_css')
	<style>
		*{ margin: 0; padding: 0;}
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


        .colockbox{width:250px;height:30px;overflow: hidden; color:#000;}
        .colockbox span{
            float:left;display:inline-block;
            width:30px;height:29px;
            line-height:29px;font-size:20px;
            font-weight:bold;text-align:center;
            color:#ff0101; margin-right:5px;}
	
	</style>
@stop

<?php $question =collect($data->question)->groupBy('type');?>
	
@section('head_js')
   <script>
		
		
		
		$(function () {
			var title={
				1:'选择题',
				2:'多选题',
				3:'判断题',
				4:'填空题',
				5:'名词解释题',
				6:'论述题',
				7:'简答题'
			};			
			var aZimu = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
			var _json = {!! $question->toJson() !!};
			
			
			function setnamestr(question,i) {
				var _name = 
					'<div class="mart_10">'
						+'<span class="font16">'+(i+1)+'、'+question.question+'（'+question.poins+'分）</span>'
						+(question.images?'<img src="question.images" />':'')
					+'</div>'				
				return	_name		
			};
			

			
			console.log(_json)
				for (var name in _json) {
					var arr = _json[name];
					var str = 
							'<p>'
								+'<label class="font20">'+title[name]+'</label>'
								+'<span style="margin-left: 1em;">共<span>'+arr.length+'</span>题</span>'
							+'</p>';
					
					if (name=='1'||name=='2') {
						for (var i = 0 ; i < arr.length; i++) {
							var _str2 = '';
							var _arr = arr[i].content.replace(/\s/g,"").split(/\w\./g);
							for(var j = 0 ;j<_arr.length;j++){
							    if(_arr[j] == "" || typeof(_arr[j]) == "undefined"){
							        _arr.splice(j,1);
							        j= j-1;
							    }
							}								
							for (var j = 0 ; j< _arr.length; j++) {
								_str2+=
										'<li class="mart_10">'
											+'<div class="radio_icon left"></div>'
											+'<span class="marl_10 answer">'+aZimu[j]+'.'+_arr[j]+'</span>' 
										+'</li>'
							}
							str+=
								'<div class="allSubject">'
									+setnamestr(arr[i],i)
									+'<ul>'
										+_str2
									+'</ul>'
								+'</div>';								
						}						
					}
					if (name=='3') {
						for (var i = 0 ; i < arr.length; i++) {
							str+=
								'<div class="allSubject">'
									+setnamestr(arr[i],i)
									+'<ul>'
										+'<li class="mart_10">'
											+'<div class="radio_icon left"></div>'
											+'<span class="marl_10 answer">正确</span>' 
										+'</li>'
										+'<li class="mart_10">'
											+'<div class="radio_icon left"></div>'
											+'<span class="marl_10 answer">错误</span>' 
										+'</li>'										
									+'</ul>'
								+'</div>';								
						}							
					}
					if (name=='4') {
						for (var i = 0 ; i < arr.length; i++) {
							var _str2 = '';
							var _arr = arr[i].question.replace(/\s/g,"").match(/__/g);							
							for (var j = 0 ; j< _arr.length; j++) {
								_str2+='<li class="mart_10"><input type="text" class="form-control" /></li>';
							}
							str+=
								'<div class="allSubject">'
									+setnamestr(arr[i],i)
									+'<ul>'
										+_str2
									+'</ul>'
								+'</div>';								
						}							
					}
					if (name=='5'||name=='6'||name=='7') {
						for (var i = 0 ; i < arr.length; i++) {
							str+=
								'<div class="allSubject">'
									+setnamestr(arr[i],i)
									+'<ul>'
										+'<li class="mart_10"><textarea class="form-control"></textarea></li>'
									+'</ul>'
								+'</div>';								
						}
					}				
					
					$('.step-content').append('<div class="question_type type_'+name+'">'+str+'</div>');	
				}			
			
			
			
		});
		
		
		
		
		
		
   </script>
@stop

@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6 col-md-2">
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
                        <div class="p-md cBorder mart_10 clearfix">
                        	
							<div class="step-content body current">
				
								
							</div>
						</div>
					</div>

                           
                </div>
            </div>            
            
            
			          
            
            			
		</div>
		
		

	</div>
{{dd(session('enterTime'))}}
	<div class="btnBox left countdown">
        <span class="marl_10 left" style="display:inline-block; height: 29px; width:100px; line-height: 29px;">剩余时间：</span>
        <div class="colockbox" id="colockbox1">
            <span class="hour" id="hour">00</span><span class="left">:</span>
            <span class="minute" id="minute">59</span><span class="left">:</span>
            <span class="second" id="second">27</span>
        </div>
    </div> 	
	
@stop{{-- 内容主体区域 --}}











