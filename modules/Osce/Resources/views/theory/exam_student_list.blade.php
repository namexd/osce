@extends('osce::theory.base')
@section('title')
	考生列表
@stop

@section('head_css')
    <style>
   	body { padding: 0;}
	.box { width: 100%; height: 100%; }
    .top{
        background:#16beb0;
        line-height: 1.4rem;
        font-size: 0.5rem;
    }
    .no-data { text-align: center;  font-size: 0.25rem; border-top: 1px solid #ccc; line-height: 3rem;}
	p,h3,ul,li { margin: 0; padding: 0;}
    .white{color: white;}
    .list { height: 100%; width: 100%; position: absolute; left: 0; top: 0; padding-top: 2.5rem;}
    .list ul { width: 100%; height: 100%; overflow: auto;}
    .list li { border-top: 1px solid #ccc;}   
    .list-left { width: 2.5rem; float: left; text-align: center; font-size: 0.4rem; }
    .list-right { width: 13rem; float: left;} 
    .list-title .list-left {line-height: 1rem;}
    .list-title .list-right {line-height: 1rem; font-size: 0.4rem; text-align: center; }
    .list .list-left { line-height: 1.2rem;}
    .list .list-right { padding: 0.1rem 0rem; font-size: 0.25rem; line-height: 0.5rem;  }
    .list .list-right span { float: left; width: 1.2rem; text-align: center; overflow: hidden; margin: 0 0.05rem; }
    </style>
@stop
@section('head_js')
   <script>

		window.onresize = function () {
			document.documentElement.style.fontSize=document.documentElement.clientWidth/16+'px';
		}
		document.documentElement.style.fontSize=document.documentElement.clientWidth/16+'px';
  		$(function () {
  			var _json = {!! collect($data) !!};
  			var start = parseInt(datetotime(_json.test.start));
  			var time = _json.test.times*60;
  			var arr = _json.student;
  			var str = '';
  			if (!arr||arr.length==0) {
  				str = '<p class="no-data">暂无考试信息</p>';
  			}
  			for (var i = 0 ; i < arr.length; i++) {
  				var _arr= arr[i];
  				var _str = '';
  				for (var j = 0 ; j < _arr.length; j++) {
  					_str+='<span>'+_arr[j].name+'</span>';
  				}
  				str+=
		    		'<li class="clearfix">'
				    	+'<div class="list-left">'+timetodate(start+parseInt(time*i),'h:mm')+'</div>'
				    	+'<div class="list-right clearfix">'
				    	+_str
				    	+'</div>'    		
		    		+'</li>' 				
  			}
			$('.list ul').html(str);
  			
  		});

    </script>		
@stop

@section('body')
 <div class="box">

    <h3 class="top white center">{{$data['test']->exam->name}}——理论考试</h3>
    	<div class="clearfix list-title">
	    	<div class="list-left">
	    		<p>考试时间</p>
	    	</div>
	    	<div class="list-right ">
	    		<p>考生</p>	    		
	    	</div>   		
    	</div>
    	<div class="list">
	    	<ul>
	    		<p class="no-data">暂无考试信息</p>
	    	</ul>   		
    	</div>	







 
 

</div>

@stop{{-- 内容主体区域 --}}
