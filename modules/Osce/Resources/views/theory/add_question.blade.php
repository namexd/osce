@extends('osce::theory.base')

@section('title')
	试题新增
@stop
@section('head_css')
	<style>
			
		textarea.form-control { min-height: 200px; resize: none;}
		.form-horizontal label i,.form-horizontal label em {color: red; }
		.form-control.error {border: 1px solid #cc5965;}	
		
		
		#sj-form ul { padding: 0; margin: 0; color: #337ab7; }
		#sj-form ul li { border-bottom: 1px solid #337ab7; margin: 10px 0; position: relative; cursor: pointer;padding-right: 20px; overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
		#sj-form ul li i { font-style: normal;}
		#sj-form ul li em,#add-dx ul li em {color: #ed5565; position: absolute; right: 0; top: 2px;}
		
		
		#add-dx { overflow: hidden; padding: 20px 0;}
		#add-dx ul { padding: 0;}
		#add-dx ul li { position: relative; margin-bottom: 5px;}
		#add-dx ul li div { padding: 0 20px 0 50px;}
		#add-dx ul li i { position: absolute; left: 0; top: 6px; width: 16px; height: 16px;}
		#add-dx ul li span { position: absolute; text-align: center; left: 30px; top: 0; line-height: 34px; }
		
		.tp-list div { float: left; margin:0 20px 20px 0; position: relative; border: 1px solid #ccc; width: 200px; height: 200px; line-height: 200px; text-align: center; vertical-align: middle;}
		.tp-list div img { max-width: 98%; max-height: 98%;}
		.tp-list div i { font-size: 50px;}
		.tp-list div input { position: absolute; left: 0; top: 0; width: 100%; height: 100%; cursor: pointer; opacity: 0;}
		.tp-list div em { position: absolute; right: -10px; top: -10px; cursor: pointer; z-index: 2; font-size: 26px; color: #ed5565; background: #fff;}
		
		.addtm-xz,.addtm-pd ,.addtm-wd  { display: none;}
		
		
		
	</style>

@stop	
@section('head_js')

	<script>
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
		
	$('#type').change(function () {
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
	});
	
	
	
	
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
					_xx+=$(this).find('.xuhao-xx').html()+$(this).find('input').val();				
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
			console.log($(this).prop("outerHTML"));
			_tp+=$(this).prop("outerHTML");
		});
		
		$('#content').val(_xx);
		$('#answer').val(_da);
		$('#images').val(_tp);
		
		console.log(JSON.parse(formToJson($('#add-dx').serialize())))
//		return false;	
	});
	
	inituploadimg($('#add-dx'));

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
	</script>

@stop


@section('body')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6">
            <h5 class="title-label">试题新增</h5>
        </div>
    </div>	

	<form class="form-horizontal addtm-con" id="add-dx" method="post" action="{{route('osce.theory.postAddQuestion')}}">
	    <div class="form-group">
	        <label for="type" class="col-sm-3 control-label">题型：<i>*</i></label>
			<div class="col-sm-7">
				<select id="type" name="type" placeholder="请选择题型" class="form-control">
					<option value="">请选择题型</option>
					@foreach($types as $k=>$val)
						<option value="{{$k}}">{{$val}}</option>
					@endforeach
				</select>

			</div>
	    </div>    
	    <div class="hr-line-dashed"></div>   
	    <div class="form-group">
	        <label for="question" class="col-sm-3 control-label"><i>*</i> 题干：</label>
			<div class="col-sm-7">
				<textarea name="question" id="question" placeholder="请填写题干" class="form-control" ></textarea>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    <div class="form-group">
	        <label class="col-sm-3 control-label">图片：</label>
			<div class="col-sm-7 tp-list" uploadurl="{{route('osce.theory.toUpload')}}" deleteurl="{{route('osce.theory.toDeleteUpload')}}">
				<div>
					<i class="fa fa-plus"></i>
					<input class="uploadimg" type="file" />
				</div>
			</div>
	    </div>
	    <input type="hidden" name="images" id="images" />
	    <input type="hidden" name="answer" id="answer" />
	    <input type="hidden" name="content" id="content" />
	    
	    <div class="hr-line-dashed"></div>     
	    <div class="form-group addtm-xz">
	        <label class="col-sm-3 control-label"> 选项：</label>
			<div class="col-sm-7">
				<ul>
					<li>
						<i class="radio_icon"></i>
						<span class="xuhao-xx">A.</span>
						<div><input type="text" class="form-control" /></div>
						<em class="state2 fa fa-trash-o fa-2x remove-xx"></em>
					</li>
				</ul>
				<input type="button" class="btn btn-sm btn-primary add-xx" value="新增选项" />
			</div>
	    </div>
	    <div class="form-group addtm-pd">
	        <label class="col-sm-3 control-label"><i>*</i> 选项：</label>
			<div class="col-sm-7">
				<ul>
					<li>
						<i class="radio_icon"></i>
						<div><input type="text" value="正确" readonly="readonly" class="form-control" /></div>
					</li>
					<li>
						<i class="radio_icon"></i>
						<div><input type="text" value="错误" readonly="readonly" class="form-control" /></div>
					</li>
				</ul>
			</div>
	    </div>
	   	
	    <div class="form-group addtm-wd">
	        <label class="col-sm-3 control-label">参考评分点：</label>
			<div class="col-sm-7">
				<textarea class="form-control addtm-answer" placeholder="请填写参考评分点，多个参考评分点请换行" ></textarea>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>    			
		
	    <div class="form-group">
	        <label for="pbase" class="col-sm-3 control-label"><i>*</i> 考察知识模块：</label>
			<div class="col-sm-7">
				<input name="pbase" id="pbase" placeholder="请填写考察知识模块" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="base" class="col-sm-3 control-label"><i>*</i> 知识要点：</label>
			<div class="col-sm-7">
				<input name="base" id="base" placeholder="请填写知识要点" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    
	    <div class="form-group">
	        <label for="times" class="col-sm-3 control-label"><i>*</i> 答题时间：</label>
			<div class="col-sm-7">
				<input name="times" id="times" placeholder="请输入答题时间（按分钟计时）" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    
	    
	    <div class="form-group">
	        <label for="separate" class="col-sm-3 control-label"><i>*</i> 区分度：</label>
			<div class="col-sm-7">
				<input name="separate" id="separate" placeholder="请输入区分度" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    
	    <div class="form-group">
	        <label for="poins" class="col-sm-3 control-label"><i>*</i> 分值：</label>
			<div class="col-sm-7">
				<input name="poins" id="poins" placeholder="请输入分值" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    	    
	    
	    <div class="form-group">
	        <label for="cognition" class="col-sm-3 control-label"><i>*</i> 认知：</label>
			<div class="col-sm-7">
				<select name="cognition" id="cognition" placeholder="请选择认知" class="form-control">
					<option value="1">解释</option>
					<option value="2">记忆</option>
					<option value="3">应用</option>
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="source" class="col-sm-3 control-label"><i>*</i> 题源：</label>
			<div class="col-sm-7">
				<select name="source" id="source" placeholder="请选择题源" class="form-control">
					<option value="1">自编</option>
					<option value="2">国内</option>
					<option value="3">国外</option>
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="lv" class="col-sm-3 control-label"><i>*</i> 适用层次：</label>
			<div class="col-sm-7">
				<select name="lv" id="lv" placeholder="请选择适用层次" class="form-control">
					<option value="1">专科生</option>
					<option value="2">本科生</option>
					<option value="3">研究生</option>
					<option value="4">博士生</option>
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="require" class="col-sm-3 control-label"><i>*</i> 要求度：</label>
			<div class="col-sm-7">
				<select name="require" id="require" placeholder="请选择要求度" class="form-control">
					<option value="1">熟悉</option>
					<option value="2">了解</option>
					<option value="3">掌握</option>
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="degree" class="col-sm-3 control-label"><i>*</i> 难度：</label>
			<div class="col-sm-7">
				<select name="degree" id="degree" placeholder="请选择难度" class="form-control">
					<option value="1">简单</option>
					<option value="2">中等</option>
					<option value="3">较难</option>
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	  
	    <div class="form-group">
	        <div class="col-sm-6 col-sm-offset-4">
	            <button class="btn btn-primary addtm-save">保存</button>
	            <a class="btn btn-white" onclick="javascript:history.go(-1);">取消</a>
	        </div>
	    </div>
	</form>
		
			
</div>		


		
@stop

