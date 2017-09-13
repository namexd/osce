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
        line-height: 1rem;
        font-size: 0.4rem;
    }
    .no-data { text-align: center;  font-size: 0.25rem; border-top: 1px solid #ccc; line-height: 3rem;}
	p,h3,h4,ul,li { margin: 0; padding: 0;}
    .white{color: white;}
    .list,.list-title { display: flex; border-bottom: 1px solid #ccc; }
    .list li,.list-title h4 { flex: 1;padding: 0.2rem 0; font-size: 0.25rem; text-align: center; line-height: 0.5rem; border-right: 1px solid #ccc; }   
    .list li:first-child,.list-title h4:first-child {flex: none;width: 12%; } 
    .list li:last-child,.list-title h4:last-child { border-right: none;} 
    
    
    </style>
@stop
@section('head_js')
   <script>
   	
   	
		window.onresize = function () {
			document.documentElement.style.fontSize=document.documentElement.clientWidth/16+'px';
		}
		document.documentElement.style.fontSize=document.documentElement.clientWidth/16+'px';
  		$(function () {
			var data = {!! collect($plan) !!};
			console.log(data);
			
			var str = '';
			for (var name in data ) {
				writekaozhan(data[name]);
				
			}
			
			function writekaozhan(kaozhan) {
				console.log(kaozhan);
				
				
				var str_kz = '<h4>时间</h4>';
				var _list = '';
				
				var _time = '';
				
				
				for (var kz in kaozhan) {
					str_kz+='<h4>'+kaozhan[kz].name+'</h4>';
					var _stu = kaozhan[kz].child;
					
//					for (var time in _stu) {
//						_time+='<div>'+time+'</div>';
//						for (var _item in stu[time].items) {
//							_name+='<div>'+stu[time].items[_item].name+'</div>';
//						}
//						
//						
//						
//					}
					
					
					
				}
				
				_list = '<li>'+_time+'</li>';
				
				
//				return '<div class="list-title">'+str_kz+'</div><ul class="list">'+_list+'</ul>';
				$('.box').append('<div class="list-con">'+str+'</div>');
			};
			
  			
  		});

    </script>		
@stop

@section('body')
 <div class="box">

    <h3 class="top white center">{{$exam->name}}</h3>
    
    <div class="list-con">
	    <div class="list-title">
	    	<h4 class="time">时间</h4>
	    	<h4>考站1</h4>
	    	<h4>考站2</h4>
	    	<h4>考站3</h4>
	    	<h4>考站4</h4>
	    	<h4>考站3</h4>
	    	<h4>考站4</h4>
	    </div>
	    <ul class="list">
	    	<li>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    	</li>
	    	<li>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    	</li>
	    	<li>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    	</li>
	    	<li>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    	</li>
	    	<li>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    	</li>
	    	<li>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    	</li>
	    	<li>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    	</li>
	    </ul>
   	
    </div>
	
	<div class="list-con">
	    <div class="list-title">
	    	<h4 class="time">时间</h4>
	    	<h4>考站1</h4>
	    	<h4>考站2</h4>
	    	<h4>考站3</h4>
	    	<h4>考站4</h4>
	    </div>
	    <ul class="list">
	    	<li>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    		<div>12:56</div>
	    	</li>
	    	<li>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    	</li>
	    	<li>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    	</li>
	    	<li>
	    		<div>范典</div>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    	</li>
	    	<li>
	    		<div>兰功伟</div>
	    		<div>孙杰</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    		<div>唐小伟</div>
	    		<div>范典</div>
	    	</li>
	    </ul>
	
	
		
	</div>



 
 

</div>

@stop{{-- 内容主体区域 --}}
