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
        position: relative;
    }
    .top button { position: absolute; top: 0; right: 1.4rem; border: none; outline: none; width: 1rem; font-size: 0.25rem; background: none;}
    .top .play {/* right: 1.4rem;*/ right: 0.2rem; font-size: 0.35rem;}
    
    .no-data { text-align: center;  font-size: 0.4rem; border-top: 1px solid #ccc; line-height: 3rem;}
	p,h3,h4,ul,li { margin: 0; padding: 0;}
    .white{color: white;}
    .list-title h4 { padding: 0.2rem 0;}
    .list,.list-title { border-bottom: 1px solid #ccc; }
    .list li,.list-title { display: flex; }
    .list li div,.list-title h4 { flex: 1; font-size: 0.25rem; text-align: center; line-height: 0.5rem; border-right: 1px solid #ccc; }   
    .list li div:first-child,.list-title h4:first-child {flex: none;width: 12%; } 
    .list li div:last-child,.list-title h4:last-child { border-right: none;} 
    
    .list li:first-child div { padding-top: 0.2rem;}
    .list li:last-child div { padding-bottom: 0.2rem;}
    
    #audio { display: none;}
    
    </style>
@stop
@section('head_js')
   <script>
   		var timer;
   		var next = 0;
   		var room_len=0;
   		
		window.onresize = function () {
			document.documentElement.style.fontSize=document.documentElement.clientWidth/16+'px';
		}
		document.documentElement.style.fontSize=document.documentElement.clientWidth/16+'px';
  		$(function () {
			var data = {!! collect($plan) !!};
			console.log(data);
			room_len = data.number;
			var str_kz = '<h4>时间</h4>';
			for (var name in data.room) {
//				room_len++;
				str_kz+='<h4>'+data.room[name]+'</h4>';
				for (var kz in data.plan) {
					if (!data.plan[kz][name]) {
						data.plan[kz][name] = {student:[{name:'&nbsp;'}]};
					}				
				}					
			}
			var arr = [];
			var num = 0 ;
			for (var time in data.plan) {
				if (num%room_len==0) {
					arr.push({});
				}
				arr[arr.length-1][time]=data.plan[time];
				num++;
			}		
			var str = '';
			for (var i = 0 ; i < arr.length; i++) {
				var _str = '';
				for (var time in arr[i]) {
					var _list = '';
					var _arr = arr[i][time];
					for (var room in _arr) {
						var _arr2 = [];
						for (var j = 0 ; j < _arr[room].student.length; j++) {
							_arr2.push(_arr[room].student[j].name);
						}
						_list+='<div _room="'+_arr[room].name+'">'+_arr2.join('、')+'</div>';
					}
					_str+='<li><div class="time">'+timetodate(time,6)+'</div>'+_list+'</li>';
				}				
				str+='<ul class="list hide">'+_str+'</ul>';
			}
			
			$('.list-con').html('<div class="list-title">'+str_kz+'</div>'+str);
			
			var cate = '{{$exam->sequence_cate or ''}}';
			if (cate=='2') {
				$('.list:not(:last) li:last-child div,.list:not(:first) li:first-child div').css('padding','0');
				$('.list:not(:last)').css('border-bottom','none');
				$('.list,.next').removeClass('hide');
				
			}
			$('.next').click(function () {				
	  			next+=room_len;
				if ($('.play i').hasClass('fa-volume-up')) {
					playshunxu();
				}				
			});
			
			$('.play').click(function () {
				if ($(this).find('i').hasClass('fa-volume-up')) {
					pauseaudio();
					$(this).find('i').removeClass('fa-volume-up');
					$(this).find('i').addClass('fa-volume-off');
				} else {
					if (cate=='2') {
						playshunxu();
					} else {
						playaudio();
					}					
					
					$(this).find('i').removeClass('fa-volume-off');
					$(this).find('i').addClass('fa-volume-up');
				}
			});
			@if($exam)
//			getspeechnow();
//			timer = setInterval(getspeechnow,10000);
			@endif
  		});
  		
  		function getspeechnow() {
			$.ajax({
				type:"get",
				url:"{{route('osce.admin.exam.getSpeechNow')}}",
				data:{exam_id:'{{$exam->id or ''}}'},
				success:function (res) {
					hasname(res.data.list,res.data.name);
				}
			});	
  		};
  		
  		
  		function hasname(arr,room) {
			if ($('.list').length==0 || arr.length==0) {
				clearInterval(timer);
				$('.list-con').html('<p class="no-data">考试已结束</p>');
				return false;
			}
			var name = getlistname($('.list:first div:not(.time)[_room="'+room+'"]'));
			var ok = true;
			for (var i = 0 ; i < arr.length; i++) {
				if (!name[arr[i]]) {
					ok = false;
					break;
				}
			}
			if (!ok) {
				$('.list:first').remove();
				hasname(arr,room);
			} else {
				$('.list').eq(0).removeClass('hide');
				$('.list').eq(1).removeClass('hide');
				if ($('.list:first').attr('isplay')!='1'&&$('.play i').hasClass('fa-volume-up')) {
					playaudio();
				}
				$('.list:first').attr('isplay','1');
			}  			
  			
  		};
  		
  		
  		
  		function getlistname(obj) {
  			obj = obj||$('.list:first div:not(.time)');
  			var name = {};
			$(obj).each(function () {
				if ($(this).html()=='&nbsp;') {
					return true;
				} 
				var _arr = $(this).html().split('、');
				for (var i = 0 ; i < _arr.length; i++) {
					if (_arr[i]=='&nbsp;'||$.trim(_arr[i])=='') {
						continue;
					} 
					if (!name[_arr[i]]) {
						name[_arr[i]] = 1;
					}	
				}
			});		
  			return name;
  		};
  		
  		function pauseaudio() {
  			$('#audio')[0].onended = null;
  			$('#audio')[0].pause();
  			clearInterval($('#audio')[0].timer);
  			$('#audio')[0].times = 0;;
  			$('#audio').attr('src','');
  		};
  		
  		function playshunxu () {
  			var _arr = [];
  			for (var i = 0 ; i <room_len; i++ ) {
  				var _obj = $('.list-con li').eq(next+i);
  				
  				if ($(_obj)) {
  					
  					if ($(_obj).find('div').eq(1).html()!='&nbsp;'&&$(_obj).find('div').eq(1).html()!=undefined) {
  						_arr.push($(_obj).find('div').eq(1).html());
  					}
  				}
  			}
  			if (_arr.length==0) {
  				$('.next').css('display','none');
  				next-=room_len;
  			} else {
  				toplayaudio('请考生：'+_arr.join('、')+'，做好准备，，！');
  			}
  			
  		};

  		
		function playaudio() {
			
			if ($('.list').length==0) {
				return false;
			}
			var arr = [];
			var name = getlistname();
			for (var _name in name) {	
				arr.push(_name);
			}
			console.log(arr);
			toplayaudio('请考生：'+arr.join('、')+'，做好准备，，！');
		};
  		function toplayaudio(str) {
  			pauseaudio();
			$.ajax({
				type:"get",
				url:"/getSpeechUrl",
				data:{text:str},
				success:function (res) {
					console.log(res);
					$('#audio').attr('src',res);
					$('#audio')[0].oncanplay = function () {
						$('#audio')[0].play();
						$('#audio')[0].onended = function () {
							$('#audio')[0].times++;
							if ($('#audio')[0].times>=3) {
								pauseaudio();
							} else {
								$('#audio')[0].play();
							}
						}
					};
				}
			});	 			
  		};					
    </script>		
@stop

@section('body')
 <div class="box">

    <h3 class="top white center">
    	{{$exam->name or '考试未开始！'}}
    	<button class="next hide">下一组</button>
    	<button class="play"><i class="fa fa-volume-off" aria-hidden="true"></i></button>
    </h3>
    
    <div class="list-con">
	    
		
	</div>
	
	<audio id="audio" src=""></audio>
	
</div>

@stop{{-- 内容主体区域 --}}
