window.onload = function () {
	'use strict';
	common();
	
	//获取所有主副科室
	Api.cycle({
		json:{},
		fn:function (arr) {
			console.log(arr);
			for (var i = 0 ; i < arr.length; i++) {
				var _arr = arr[i].child;
				var _str = '';
				for (var j = 0; j < _arr.length; j++) {
					_str += '<li><input type="checkbox" id="'+_arr[j].name+'" class="childInp" value="'+_arr[j].id+'" /><label for="'+_arr[j].name+'">'+_arr[j].name+'</label></li>';
				}
				var str = 
					'<div>'
						+'<p>'
							+'<input type="checkbox" id="'+arr[i].name+'" class="fatherInp" />'
							+'<label for="'+arr[i].name+'">'+arr[i].name+'</label>'
						+'</p>'
						+'<ul class="clearfix">'
						+_str
						+'</ul>'
					+'</div>';				
				$('.choose-depart .exam-right').append(str);
			}
			//注册科室选择的联动事件
			choosedepart();			
		}
	});
	//获取所有教师
	togetteacher('',0,function (arr) {
		console.log(arr);
		for (var i = 0 ; i < arr.length; i++) {
			$('.set-exam-list .choose-teacher ul').eq(0).append('<li _id="'+arr[i].id+'">'+arr[i].name+'</li>');						
		}
	});	
	//获取所有试题
	Api.ChooseTest({
		json:{
			father_id:0,
			son_id:0
		},
		fn:function (arr) {
			writesel('请选择考题','.sel-question',arr);				
		}
	});
	//获取所有教室
	Api.ClassroomTest({
		fn:function (arr) {
			console.log(arr);
			for (var i = 0 ; i < arr.length; i++) {
				$('.set-exam-list .choose-room ul').eq(0).append('<li _id="'+arr[i].id+'">'+arr[i].floor+arr[i].name+'</li>');
			}
		}
	});



	//注册监考设置和考场设置的点击事件
	$('.set-exam-list .choose-set-left').click(function (e) {
		if (e.target.tagName.toLowerCase()=='li') {
			$(e.target).parent().parent().find('.choose-set-right').append($(e.target));
		}
	});
	$('.set-exam-list .choose-set-right').click(function (e) {
		if (e.target.tagName.toLowerCase()=='li') {
			$(e.target).parent().parent().find('.choose-set-left').append($(e.target));
		}
	});
	//绑定时间选择插件
	var nowtime = new Date();
	var strtime = nowtime.getFullYear()+'-'+(nowtime.getMonth()+1)+'-'+nowtime.getDate()+' '+nowtime.getHours()+':'+nowtime.getMinutes()+':00';				
//	$(".set-exam-list .sel-time").datetimepicker({
//		startDate:strtime,
//		format: "yyyy-mm-dd hh:ii",
//		autoclose: true,
//		pickerPosition: "bottom-left",
//		minView:'0'
//	});	
	
	$('.exam-addbtn').click(function () {
		if (!$('.choose-type input[name="type"]:checked').val()) {
			uselayer(3,'请选择考试类型');
			$('.choose-type input[name="type"]').focus();
			return false;
		}
		if ($('.choose-depart .childInp:checked').length==0) {
			uselayer(3,'请选择科室');
			$('.choose-depart .childInp').focus();
			return false;
		}
		if ($('.sel-question').val()==0) {
			uselayer(3,'请选择试题');
			$('.sel-question').focus();
			return false;
		}
		if ($.trim($('.starttime').val())=='') {
			$('.starttime').focus();
			uselayer(3,'请选择考试开始时间');
			return false;
		}
		if ($.trim($('.endtime').val())=='') {
			$('.endtime').focus();
			uselayer(3,'请选择考试结束时间');
			return false;
		}
		if ($.trim($('.endtime').val())<=$.trim($('.starttime').val())) {
			$('.starttime').focus()
			$('.endtime').focus();
			uselayer(3,'结束时间不能小于开始时间');
			return false;
		}
		if ($('.set-exam-list .choose-teacher ul').eq(1).find('li').length==0) {
			uselayer(3,'请选择监考老师');
			return false;
		}
		if ($('.set-exam-list .choose-teacher ul').eq(1).find('li').length!=$('.choose-depart .childInp:checked').length) {
			uselayer(3,'请检查已选教师是否和已选科室数目相同');
			return false;
		}
		if ($('.set-exam-list .choose-room ul').eq(1).find('li').length!=$('.choose-depart .childInp:checked').length) {
			uselayer(3,'请检查已选考场是否足够已选科室使用');
			return false;
		}
		var adepid = [];
		var atea = [];
		var aroom = [];
		$('.choose-depart .childInp:checked').each(function () {
			adepid.push($(this).val());
		});
		
		$('.set-exam-list .choose-teacher ul').eq(1).find('li').each(function () {
			atea.push($(this).attr('_id'));
		});	
		
		$('.set-exam-list .choose-room ul').eq(1).find('li').each(function () {
			aroom.push($(this).attr('_id'));
		});			
		
		Api.AddExam({
			isrepeat:$(this),
			json:{
				type:$('.choose-type input[name="type"]:checked').val(),
				department_id:adepid,
				tid:$('.sel-question').val(),
				start:datetotime($.trim($('.starttime').val())),
				end:datetotime($.trim($('.endtime').val())),
				teacher:atea,
				classroom:aroom
			},
			fn:function (json) {
				uselayer(1,json.msg,function () {
					if (json.status==1) {
						window.location.reload();
					}
				});
			}
			
		});

	});
	

	//注册科室选择的联动事件
	function choosedepart() {			
			$(".fatherInp").click(function() {//点击父科室
				var $fa = $(this);
				$($fa).parent().parent().find("ul input").each(function () {
					$(this)[0].checked = $($fa)[0].checked;
				});
			});	
			
			$(".childInp").click(function() {//点击子科室
				var $parent = $(this).parent().parent();
				var $fa = $($parent).parent().find('.fatherInp');
				var count = 0;//选中的个数
				$($parent).find("input").each(function () {
					if ($(this)[0].checked == true) {
						count++;
					}
				});
				if (count==0) {
					if ($($fa)[0].checked==true) {
						$($fa)[0].checked=false;
//						departchange($fa);						
					}
				} else {
					if ($($fa)[0].checked==false) {
						$($fa)[0].checked=true;
//						departchange($fa);						
					}					
				}
			});	
//			$(".fatherInp").on('change',function () {
//				departchange($(this));	
//			});			
	};
	
	
	function departchange(obj) {
		
		if ($(obj)[0].checked) {
			
			togetteacher(0,$('.sel-childdepart').val(),function (arr) {
				writesel('','.sel-teacher',arr,0,function () {
					console.log(arr);	
					var $ul = $('.set-exam-list[name="'+$(obj).attr("id")+'"]').find('.choose-teacher ul').eq(0);
					for (var i = 0 ; i < arr.length; i++) {
						$($ul).append('<li>'+arr[i].name+'</li>');						
					}
				});
			});			
			
			
			$('.set-exam').append(examList);
			
			$('.set-exam-list.new .choose-question .exam-left strong').html($(obj).attr('id'));
			$('.set-exam-list.new').attr('name',$(obj).attr('id'));
			
			
			$('.set-exam-list.new .choose-set-left').click(function (e) {
				if (e.target.tagName.toLowerCase()=='li') {
					$(e.target).parent().parent().find('.choose-set-right').append($(e.target));
				}
			});
			$('.set-exam-list.new .choose-set-right').click(function (e) {
				if (e.target.tagName.toLowerCase()=='li') {
					$(e.target).parent().parent().find('.choose-set-left').append($(e.target));
				}
			});
					
					
			var nowtime = new Date();
			var strtime = nowtime.getFullYear()+'-'+(nowtime.getMonth()+1)+'-'+nowtime.getDate()+' '+nowtime.getHours()+':'+nowtime.getMinutes()+':00';				
			$(".set-exam-list.new .sel-time").datetimepicker({
				startDate:strtime,
				format: "yyyy-mm-dd hh:ii",
				autoclose: true,
				pickerPosition: "bottom-left",
				minView:'0'
			});						
					
					
			$('.set-exam-list.new').removeClass('new');
		} else {
			$('.set-exam-list[name="'+$(obj).attr("id")+'"]').remove();			
		}
		
		
		
		
		
	};
	

		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
};








var examList =
	'<div class="set-exam-list new">'
		+'<div class="exam-column clearfix choose-question">'
			+'<span class="exam-left"><strong>急诊科</strong>考题选择：</span>'
			+'<div class="exam-right clearfix">'
				+'<div>'
					+'<select name="" class="form-control">'
						+'<option value="">请选择考题</option>'
					+'</select>'								
				+'</div>'
				+'<a href="javascript:;">考题预览</a>'
			+'</div>'
		+'</div>'	
		+'<div class="exam-column clearfix choose-time">'
			+'<span class="exam-left">考试时间：</span>'
			+'<div class="exam-right clearfix">'
				+'<div>'
					+'<input type="text" placeholder="请选择开始时间" class="form-control sel-time starttime" />'
				+'</div>'
				+'<span>至</span>'
				+'<div>'
					+'<input type="text" placeholder="请选择结束时间" class="form-control sel-time endtime" />'
				+'</div>'
			+'</div>'
		+'</div>'	
		+'<div class="exam-column clearfix choose-teacher choose-set">'
			+'<span class="exam-left">监考设置：</span>'
			+'<div class="exam-right clearfix">'
				+'<ul class="choose-set-left">'
				+'</ul>'
				+'<i></i>'
				+'<ul class="choose-set-right">'
				+'</ul>'
			+'</div>'
		+'</div>'	
		+'<div class="exam-column clearfix choose-set">'
			+'<span class="exam-left">考场设置：</span>'
			+'<div class="exam-right clearfix">'
				+'<ul class="choose-set-left">'
					+'<li>教学示范室一</li>'
					+'<li>教学示范室二</li>'
				+'</ul>'
				+'<i></i>'
				+'<ul class="choose-set-right">'
				+'</ul>'
			+'</div>'
		+'</div>'					
	+'</div>';























