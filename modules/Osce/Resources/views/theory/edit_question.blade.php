@extends('osce::theory.base')

@section('title')
	试题编辑
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

	<script src="{{ asset('osce/theory/js/question-add.js') }}"></script>
	<script>
		$(function () {
			
	
			$('#type').val('{{$question->type}}');
			$('#cognition').val('{{$question->cognition}}');
			$('#source').val('{{$question->source}}');
			$('#lv').val('{{$question->lv}}');
			$('#require').val('{{$question->require}}');
			$('#degree').val('{{$question->degree}}');
			
			var aTp = $('#images').val().match(/<img[^>]*>/gi);
			if (aTp) {
				for (var i = 0 ; i < aTp.length; i++ ) {
					$('<div>'+aTp[i]+'<em class="fa fa-times-circle remove-image"></em></div>').insertBefore($('.tp-list div').last());
				}						
			}
			var _type = $('#type').val();
			var answer = $('#answer').val();
			typechange();
			if (_type=='1'||_type=='2') {
				var aXuan = $('#content').val().replace(/\s/g,"").split(/[a-z|A-Z]\./g);
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
			
			@if (request()->get('from')=='view')
				$('.title-label').html('试题预览');
				$('title').html('试题预览');
				$('.form-control').attr('disabled','disabled');	
				$('.remove-image,.remove-xx,.add-xx,.tp-list div:last').css('display','none');
				$('#add-dx').off('click');
			@endif
			
		});
	</script>
	
@stop


@section('body')

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6">
            <h5 class="title-label">试题编辑</h5>
        </div>
    </div>	

	<form class="form-horizontal addtm-con" id="add-dx" method="post" action="{{route('osce.theory.postEditQuestion')}}">
	    <div class="form-group">
	        <label for="type" class="col-sm-3 control-label">题型：<i>*</i></label>
			<div class="col-sm-7">
				<select id="type" name="type" placeholder="请选择题型" class="form-control">
					<option value="">请选择题型</option>
					@foreach($question->typeValues as $k=>$val)
						<option value="{{$k}}">{{$val}}</option>
					@endforeach
				</select>

			</div>
	    </div>    
	    <div class="hr-line-dashed"></div>   
	    <div class="form-group">
	        <label for="question" class="col-sm-3 control-label"><i>*</i> 题干：</label>
			<div class="col-sm-7">
				<textarea name="question" id="question" placeholder="请填写题干" class="form-control" >{{$question->question or ''}}</textarea>
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
	    <input value="{{$question->images}}" type="hidden" name="images" id="images" />
	    <input value="{{$question->answer}}" type="hidden" name="answer" id="answer" />
	    <input value="{{$question->content}}" type="hidden" name="content" id="content" />
	    <input value="{{$question->id}}" type="hidden" name="id" />
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
				<input value="{{$question->pbase or ''}}" name="pbase" id="pbase" placeholder="请填写考察知识模块" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="base" class="col-sm-3 control-label"><i>*</i> 知识要点：</label>
			<div class="col-sm-7">
				<input value="{{$question->base or ''}}" name="base" id="base" placeholder="请填写知识要点" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    
	    <div class="form-group">
	        <label for="times" class="col-sm-3 control-label"><i>*</i> 答题时间：</label>
			<div class="col-sm-7">
				<input value="{{$question->times or ''}}" name="times" id="times" placeholder="请输入答题时间（按分钟计时）" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    
	    
	    <div class="form-group">
	        <label for="separate" class="col-sm-3 control-label"><i>*</i> 区分度：</label>
			<div class="col-sm-7">
				<input value="{{$question->separate or ''}}" name="separate" id="separate" placeholder="请输入区分度" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    
	    <div class="form-group">
	        <label for="poins" class="col-sm-3 control-label"><i>*</i> 分值：</label>
			<div class="col-sm-7">
				<input value="{{$question->poins or ''}}" name="poins" id="poins" placeholder="请输入分值" class="form-control" type="text" />
			</div>
	    </div>
	    <div class="hr-line-dashed"></div>   
	    	    
	    
	    <div class="form-group">
	        <label for="cognition" class="col-sm-3 control-label"><i>*</i> 认知：</label>
			<div class="col-sm-7">
				<select name="cognition" id="cognition" placeholder="请选择认知" class="form-control">
					@foreach($question->cognitionValues as $k=>$val)
						<option value="{{$k}}">{{$val}}</option>
					@endforeach
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="source" class="col-sm-3 control-label"><i>*</i> 题源：</label>
			<div class="col-sm-7">
				<select name="source" id="source" placeholder="请选择题源" class="form-control">
					@foreach($question->sourceValues as $k=>$val)
						<option value="{{$k}}">{{$val}}</option>
					@endforeach
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="lv" class="col-sm-3 control-label"><i>*</i> 适用层次：</label>
			<div class="col-sm-7">
				<select name="lv" id="lv" placeholder="请选择适用层次" class="form-control">
					@foreach($question->lvValues as $k=>$val)
						<option value="{{$k}}">{{$val}}</option>
					@endforeach
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="require" class="col-sm-3 control-label"><i>*</i> 要求度：</label>
			<div class="col-sm-7">
				<select name="require" id="require" placeholder="请选择要求度" class="form-control">
					@foreach($question->requireValues as $k=>$val)
						<option value="{{$k}}">{{$val}}</option>
					@endforeach
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	    
	    <div class="form-group">
	        <label for="degree" class="col-sm-3 control-label"><i>*</i> 难度：</label>
			<div class="col-sm-7">
				<select name="degree" id="degree" placeholder="请选择难度" class="form-control">
					@foreach($question->degreeValues as $k=>$val)
						<option value="{{$k}}">{{$val}}</option>
					@endforeach
				</select>
			</div>
	    </div>
	    <div class="hr-line-dashed"></div> 
	  
	    <div class="form-group">
	        <div class="col-sm-6 col-sm-offset-4">
				@if (request()->get('from')=='edit')
					<button class="btn btn-primary addtm-save">保存</button>
				@endif
	            <a class="btn btn-white" onclick="javascript:history.go(-1);">返回</a>
	        </div>
	    </div>
	</form>
		
			
</div>		


		
@stop

