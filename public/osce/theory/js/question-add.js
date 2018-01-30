var aZimu = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
var xxStr = '<li><i class="radio_icon"></i><span class="xuhao-xx"></span><div><input type="text" class="form-control"></div><em class="state2 fa fa-trash-o fa-2x remove-xx"></em></li>';
var uploadStr = '<div><i class="fa fa-plus"></i><input class="uploadimg" type="file" /></div>';


$(function() {
	$('.add-xx').click(function () {
		var _ul = $(this).parent().find('ul');
		if ($(_ul).find('li').length>=14) {
			uselayer(3,'选项已经够多了');
			return false;
		}
		$(_ul).append(xxStr);
		$(_ul).find('li:last .xuhao-xx').html(aZimu[$(_ul).find('li').length-1]+'.');
	});
	
	$('#add-dx').click(function (e) {
		if ($(e.target).hasClass('radio_icon')) {
			if ($('#type').val()!='2') {
				$(e.target).parent().parent().find('.radio_icon').not($(e.target)).removeClass('check');
			} 
			if ($(e.target).hasClass('check')) {
				$(e.target).removeClass('check');
			} else {
				$(e.target).addClass('check');
			}
		}
		if ($(e.target).hasClass('remove-xx')) {
			var _ul = $(e.target).parent().parent();
			if ($(_ul).find('li').length>1) {
				$(e.target).parent().remove();
			}
			$(_ul).find('li').each(function () {
				$(this).find('.xuhao-xx').html(aZimu[$(this).index()]+'.');
			});
		}
		/*删除图片*/
		if ($(e.target).hasClass('remove-image')) {
			uselayer(2,"图片删除后将不可恢复，确定要删除？",function () {
				Api.ajax({
					type:'post',
					url:$('.tp-list').attr('deleteurl'),
					json:{image_url:$(e.target).prev().attr('src')},
					fn:function (res) {
						$(e.target).parent().remove();
					}
				});				
			});

		}
	});
		
	$('#type').change(typechange);
	

	
	$('.addtm-save').click(function () {
		if (noempty('#add-dx')) {
			return false;
		}
		var _da = '';
		var _xx = '';
		var _tp = '';
		if ($('#type').val()=='1'||$('#type').val()=='2') {
			$('.addtm-xz ul li').each(function () {
				if ($.trim($(this).find('input').val())=='') {
					uselayer(3,'选项不能为空');
					$(this).find('input').focus();
					_xx = '';
					return false;
				} else {
					if ($(this).find('.radio_icon.check').length!=0) {
						_da+=$(this).find('.xuhao-xx').html()[0];
					}
					_xx+=$(this).find('.xuhao-xx').html()+$(this).find('input').val()+' ';				
				}
			});	
			if (!_xx) {
				return false;
			}
		}
		/*判断题的校验*/
		if ($('#type').val()=='3') {
			_da = $('.addtm-pd ul li .radio_icon.check').parent().find('input').val();
		}	
		/*主观题*/
		if ($('#type').val()=='4'||$('#type').val()=='5'||$('#type').val()=='6'||$('#type').val()=='7') {
			_da = $.trim($('.addtm-answer').val());
		}	
		
		if ($('#type').val()=='1'||$('#type').val()=='2'||$('#type').val()=='3') {
			if (!_da) {
				uselayer(3,'请勾选出答案！');
				return false;
			}			
		}

		var _tp =  '';
		$('.tp-list img').each(function () {
			_tp+=$(this).prop("outerHTML");
		});
		
		$('#content').val(_xx);
		$('#answer').val(_da);
		$('#images').val(_tp);
		
//		return false;	
	});
	
	inituploadimg($('#add-dx'));

});

function typechange() {
	var _type = $('#type').val();
	$('.addtm-xz,.addtm-pd ,.addtm-wd').css('display','none');
	if (_type=='1'||_type=='2') {
		$('.addtm-xz').css('display','block');
	} else if (_type=='3') {
		$('.addtm-pd').css('display','block');
	} else {
		$('.addtm-wd').css('display','block');
	}
	if (_type=='4') {
		$('.addtm-wd .control-label').html('参考答案');
		$('.addtm-answer').attr('placeholder','请填写参考答案，多个答案请换行');
	} else {
		$('.addtm-wd .control-label').html('参考评分点');
		$('.addtm-answer').attr('placeholder','请填写参考评分点，多个参考评分点请换行');
	}
};

function inituploadimg(obj) {
	$(obj).find('.uploadimg:last').change(function () {
		var _this = $(this);
		uploadFile({
			url:$('.tp-list').attr('uploadurl'),
			json:{
				exam_images:$(_this)[0].files[0]
			},
			fn:function (res) {
				res = JSON.parse(res);
				console.log(res);
				if (res.code!=1) {
					uselayer(1,res.message);
					return false;
				}
				$(_this).parent().parent().append(uploadStr);
				$(_this).parent().html('<img src="'+res.filepath+'" /><em class="fa fa-times-circle remove-image"></em>');
				inituploadimg(obj);
			}
		});
	});				
};	