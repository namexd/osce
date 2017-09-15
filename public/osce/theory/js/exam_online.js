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
function setnamestr(question,i) {
	var _name = 
		'<div class="clearfix">'
			+'<span class="font16">'+(i+1)+'、'+question.question+'（'+question.poins+'分）'
			+(question.score||question.score==0?'<i class="defen">（得分：'+question.score+'分）</i>':'')+'</span>'
			+(question.images?'<img src="question.images" />':'')
			+'<div class="dafen"><strong>得分：</strong><input type="text" name="poins[]" _max="'+question.poins+'" class="form-control" /></div>'
		+'</div>'				
	return	_name		
};
function setdanxuanstr(arr,str) {
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
					'<li class="">'
						+'<div class="radio_icon left"></div>'
						+'<label class="marl_10"><span>'+aZimu[j]+'</span>.'+_arr[j]+'</label>' 
					+'</li>'
		}
		str+=
			'<div class="allSubject" _a="'+arr[i].answer+'" _ra="'+arr[i].rightanswer+'">'
				+setnamestr(arr[i],i)
				+'<ul>'
					+_str2
				+'</ul>'
				+'<input type="hidden" name="type[]" value="'+arr[i].type+'" />'
				+'<input type="hidden" name="cid[]" value="'+arr[i].id+'" />'
				+'<input type="hidden" name="answer[]" class="answer" value="" />'
			+'</div>';								
	}				
	return str;
};
function setpanduanstr(arr,str) {
	for (var i = 0 ; i < arr.length; i++) {
		str+=
			'<div class="allSubject" _a="'+arr[i].answer+'" _ra="'+arr[i].rightanswer+'">'
				+setnamestr(arr[i],i)
				+'<ul>'
					+'<li class="">'
						+'<div class="radio_icon left"></div>'
						+'<span class="marl_10">正确</span>' 
					+'</li>'
					+'<li class="">'
						+'<div class="radio_icon left"></div>'
						+'<span class="marl_10">错误</span>' 
					+'</li>'										
				+'</ul>'
				+'<input type="hidden" name="type[]" value="'+arr[i].type+'" />'
				+'<input type="hidden" name="cid[]" value="'+arr[i].id+'" />'
				+'<input type="hidden" name="answer[]" class="answer" value="" />'
			+'</div>';								
	}	
	return str;
};
function settiankongstr(arr,str) {
	for (var i = 0 ; i < arr.length; i++) {
		var _str2 = '';
		var _arr = arr[i].question.replace(/\s/g,"").replace(/_+/g,'__').match(/__/g);							
		for (var j = 0 ; j< _arr.length; j++) {
			_str2+='<li class=""><input type="text" class="form-control" /></li>';
		}
		str+=
			'<div class="allSubject" _a="'+arr[i].answer+'" _ra="'+arr[i].rightanswer+'">'
				+setnamestr(arr[i],i)
				+'<ul>'
					+_str2
				+'</ul>'
				+'<input type="hidden" name="type[]" value="'+arr[i].type+'" />'	
				+'<input type="hidden" name="cid[]" value="'+arr[i].id+'" />'
				+'<input type="hidden" name="answer[]" class="answer" value="" />'
			+'</div>';								
	}	
	return str;
};
function setwendastr(arr,str) {
	for (var i = 0 ; i < arr.length; i++) {
		str+=
			'<div class="allSubject" _a="'+arr[i].answer+'" _ra="'+arr[i].rightanswer+'">'
				+setnamestr(arr[i],i)
				+'<ul>'
					+'<li class=""><textarea class="form-control"></textarea></li>'
				+'</ul>'
				+'<input type="hidden" name="type[]" value="'+arr[i].type+'" />'
				+'<input type="hidden" name="cid[]" value="'+arr[i].id+'" />'
				+'<input type="hidden" name="answer[]" class="answer" value="" />'
			+'</div>';								
	}	
	return str;
};

function tojiaojuan() {
	$('.type_1 .allSubject,.type_3 .allSubject').each(function () {
		$(this).find('.answer').val($(this).find('.radio_icon.check').parent().find('span').html());
	});
	$('.type_2 .allSubject').each(function () {
		var _arr = [];
		$(this).find('.radio_icon.check').each(function () {
			_arr.push($(this).parent().find('span').html());
		});
		$(this).find('.answer').val(_arr.join(' '));
	});
	$('.type_4 .allSubject,.type_5 .allSubject,.type_6 .allSubject,.type_7 .allSubject').each(function () {
		var _arr = [];
		$(this).find('.form-control').each(function () {
			_arr.push($.trim($(this).val()));
		});	
		$(this).find('.answer').val(_arr.join(' '));
	});		
	$('.form-shijuan').submit();
};

function setzhuguan(arr,str) {
	for (var i = 0 ; i < arr.length; i++) {
		str+=
			'<div class="allSubject" _a="'+arr[i].answer+'" _ra="'+arr[i].rightanswer+'">'
				+setnamestr(arr[i],i)
				+'<ul>'
					+'<li class=""><span class="form-control">参考答案：'+arr[i].rightanswer+'</span></li>'
					+'<li class=""><span class="form-control">学生答案：'+arr[i].answer+'</span></li>'
				+'</ul>'
				+'<input type="hidden" name="id[]" value="'+arr[i].cid+'" />'
				+'<input type="hidden" name="isright[]" value="3" />'
			+'</div>';								
	}	
	return str;
};