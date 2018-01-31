var aZimu = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N'];
var txType = {'单选题':'1','多选题':'2','判断题':'3','填空题':'4','名词解释题':'5','论述题':'6','简答题':'7'};
var xxStr = '<li><i class="radio_icon"></i><span class="xuhao-xx"></span><div><input type="text" class="form-control"></div><em class="state2 fa fa-trash-o fa-2x remove-xx"></em></li>';
var uploadStr = '<div><i class="fa fa-plus"></i><input class="uploadimg" type="file" /></div>';
var openindex;
var listobj;		
$(function () {
	
	$('.dx-btn').click(function () {
		var _ul = $(this).parent().find('ul');
		$(_ul).append('<li><i>'+($(_ul).find('li').length+1)+'、</i><span></span><em class="state2 fa fa-trash-o fa-2x"></em></li>');
		listobj = $(_ul).find('li:last');
		opendanxuan($(this).val().substring(2,$(this).val().length));
	});
	
	$('.dx-list').click(function (e) {
		var _tag = e.target.tagName.toLowerCase();
		if (_tag=='li'||_tag=='i'||_tag=='span') {
			if (_tag=='li') {
				listobj = $(e.target);	
			} else {
				listobj = $(e.target).parent();	
			}
			opendanxuan($(listobj).attr('_typename'));
		}
		if (_tag=='em') {
			$(e.target).parent().remove();
		}
	});

    $('.addtm-points').keyup(function () {
        this.value = this.value.replace(/[^\d]/g, '');
    });				
			
	function opendanxuan(name) {
		
		openindex = layer.open({
			type: 1,
			title: '新增'+name,
			closeBtn: 1,
			shadeClose:true,
			area: ['800px',$(window).height()*0.8+'px'],
			content: $('#add-dx'), //iframe的url，no代表不显示滚动条
			success:function () {
				var _type = txType[name];
				$('.type-name').val(name);
				$('.type-value').val(_type);
				if (txType[name]=='1'||txType[name]=='2') {
					$('.addtm-xz').css('display','block');
				} else if (txType[name]=='3') {
					$('.addtm-pd').css('display','block');
				} else {
					$('.addtm-wd').css('display','block');
				}
				
				if ($(listobj).attr('issave')==1) {
//					$('.type-value').val(_type);
//					$('.type-name').val($(listobj).attr('_typename'));					
					
					$('.addtm-meditype').val($(listobj).attr('_mt'));
					$('.addtm-points').val($(listobj).attr('_fz'));
					$('.addtm-question').val($(listobj).attr('_tg'));
					var answer = $(listobj).attr('_da');
					var aTp = $(listobj).attr('_tp').match(/<img[^>]*>/gi);
					if (aTp) {
						for (var i = 0 ; i < aTp.length; i++ ) {
							$('<div>'+aTp[i]+'<em class="fa fa-times-circle remove-image"></em></div>').insertBefore($('.tp-list div').last());
						}						
					}

					
					if (_type=='1'||_type=='2') {
						var aXuan = $(listobj).attr('_xx').replace(/\s/g,"").split(/[a-z|A-Z]\./g);
						for (var i = 0 ; i < aXuan.length; i++) {
							if(aXuan[i] == "" || typeof(aXuan[i]) == "undefined"){
								continue;
							}
							$('.addtm-xz ul li:last input').val(aXuan[i]);
							$('.add-xx').click();
						}
						if ($('.addtm-xz ul li:last input').val()=='') {
							$('.addtm-xz ul li:last').remove();
						}
						var aDa = answer.replace(/\s/g,"");
						for (var i = 0 ; i < aDa.length; i++) {
							$('.addtm-xz ul li').each(function () {
								console.log(aDa[i],$(this).find('.xuhao-xx').html()[0])
								if ($(this).find('.xuhao-xx').html()[0]==aDa[i]) {
									$(this).find('.radio_icon').addClass('check');
									return false;
								}
							});
						}						
					}
					
					if (_type=='3') {
						if (answer == '正确') {
							$('.addtm-pd ul li:first .radio_icon').addClass('check');
						} else if (answer == '错误') {
							$('.addtm-pd ul li:last .radio_icon').addClass('check');
						}
					}	
					
					if (_type=='4'||_type=='5'||_type=='6'||_type=='7') {
						$('.addtm-answer').val(answer);
					}
				}
				if (_type=='4') {
					$('.addtm-wd .control-label').html('参考答案')
					$('.addtm-answer').attr('placeholder','请填写参考答案，多个答案请换行');
				}
				inituploadimg($('#add-dx'));
			},
			end:function () {
				if ($(listobj).attr('issave')!=1) {
					$(listobj).remove();
				}
				
				$('.type-value').val('');
				$('.type-name').val('');					
				$('.addtm-meditype').val('');				
				$('.addtm-points').val('');
				$('.addtm-question').val('');

				$('.tp-list').html(uploadStr);
				$('.addtm-xz ul li').remove();
				$('.add-xx').click();
				
				$('.addtm-pd .radio_icon').removeClass('check');
				
				$('.addtm-wd .control-label').html('参考评分点');
				$('.addtm-answer').attr('placeholder','请填写参考评分点，多个参考评分点请换行');
				$('.addtm-answer').val('');
				$('.addtm-xz,.addtm-pd,.addtm-wd').css('display','none');
			}
		});				
	};
	
	$('.addtm-close').click(function () {
		layer.close(openindex);
	});
	
	
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
			if ($('.type-value').val()!='2') {
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
	
	
	
	$('.addtm-save').click(function () {
		if ($('.type-name').val()==''||$('.type-value').val()=='') {
			uselayer(3,'保存失败，请刷新重试！');
			return false;
		}
		var _fz =  $.trim($('.addtm-points').val());
		var _tg =  $.trim($('.addtm-question').val());
		if (_fz=='') {
			uselayer(3,'分值不能为空！');
			$('.addtm-points').focus();
			return false;
		}
		if (_tg=='') {
			uselayer(3,'题干不能为空！');
			$('.addtm-question').focus();
			return false;
		}
		
		var _da = '';
		var _xx = '';	
		/*单选题和多选题的校验*/
		if ($('.type-value').val()=='1'||$('.type-value').val()=='2') {
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
					_xx+=$(this).find('.xuhao-xx').html()+$(this).find('input').val();				
				}
			});				
			if (!_xx) {
				return false;
			}			
		}
		/*判断题的校验*/
		if ($('.type-value').val()=='3') {
			_da = $('.addtm-pd ul li .radio_icon.check').parent().find('input').val();
		}	
		/*主观题*/
		if ($('.type-value').val()=='4'||$('.type-value').val()=='5'||$('.type-value').val()=='6'||$('.type-value').val()=='7') {
			_da = $.trim($('.addtm-answer').val());
		}	
		
		if ($('.type-value').val()=='1'||$('.type-value').val()=='2'||$('.type-value').val()=='3') {
			if (!_da) {
				uselayer(3,'请勾选出答案！');
				return false;
			}			
		}

		var _tp =  '';
		$('.tp-list img').each(function () {
			_tp+=$(this).prop("outerHTML");
		});
		
		
		$(listobj).attr({
			issave:'1',
			_type:$('.type-value').val(),
			_typename:$('.type-name').val(),
			_mt:$('.addtm-meditype').val(),
			_fz:_fz,
			_tg:_tg,
			_tp:_tp,
			_da:_da,
			_xx:_xx
		});
		$(listobj).find('span').html('（'+_fz+'分）'+_tg);
		layer.close(openindex);				
	});
	
	
	$('.ja-save').click(function () {
		if ($.trim($('#sj-name').val())=='') {
			uselayer(3,'试卷名称不能为空');
			$('#sj-name').focus();
			return false;
		}
		var question = [];
		$('.dx-list li').each(function () {
			var aTp = $(this).attr('_tp').match(/<img[^>]*>/gi)
			question.push({
				type:$(this).attr('_type'),
				category:$(this).attr('_mt').toUpperCase(),
				question:$(this).attr('_tg')+(aTp?aTp.join(''):''),
				content:$(this).attr('_xx'),
				answer:$(this).attr('_da'),
				poins:$(this).attr('_fz')
			});
		});
		if (question.length==0) {
			uselayer(3,'请添加试题');
			return false;			
		}
		uselayer(2,'确定要保存吗？',function () {
			var _json = {
					name:$.trim($('#sj-name').val()),
					question:question
			};
			if ($('#sj-form').attr('_id')) {
				_json.id = $('#sj-form').attr('_id');
			}
			Api.ajax({
				type:'post',
				url:$('#sj-form').attr('posturl'),
				json:_json,
				fn:function (res) {
					uselayer(1,'保存成功！',function () {
						window.location.href=$('#sj-form').attr('jumpurl');
					});
				}
			});				
		});
	
	});
	
	
});

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