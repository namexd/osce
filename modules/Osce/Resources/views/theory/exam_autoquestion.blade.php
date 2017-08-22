@extends('osce::theory.base')

@section('title')
	新增试卷
@stop
@section('head_css')
	<style>
		.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
		    background-color: #fff;
		}		
		.form-horizontal .control-name {
			text-align: left; padding-left: 0; width: 100px;
		}
		.padL0 { padding-left: 0;}
	</style>
		
		

@stop	
@section('head_js')
	<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
	<script>
		$(function () {
			'use strict'
			
		    $('input[name="number[]"],input[name="score[]"]').keyup(function () {
		        this.value = this.value.replace(/[^\d]/g, '');
		    });				
			
			$('.btn-primary').click(function () {
				if(noempty('.form-horizontal')){
					return false;
				}
				var bok = true;
				var str = '';
				$('.form-horizontal .form-group:not(:first):not(:last)').each(function () {
					if ($(this).find('input').eq(0).val()==''&&$(this).find('input').eq(1).val()!='') {
						uselayer(3,'请填写试题数量');
						$(this).find('input').eq(0).focus();
						bok = false;
						return false;
					}
					if ($(this).find('input').eq(1).val()==''&&$(this).find('input').eq(0).val()!='') {
						uselayer(3,'请填写分值');
						$(this).find('input').eq(1).focus();
						bok = false;
						return false;
					}
					str+=$(this).find('input').eq(0).val();
				});
				if (!str) {
					uselayer(3,'请至少选择一种题型！');
					$('.form-horizontal .form-group:not(:first):not(:last) input').eq(0).focus();
					bok = false;
				}
				if (!bok) {
					return false;
				}

				uselayer(2,'确定要新增考试吗？',function () {
					Api.ajax({
						type:'post',
						url:"{{route('osce.theory.autoexam')}}",
						json:$('.form-horizontal').serialize(),
						fn:function (res) {
							window.location.href = "{{route('osce.theory.autoexampreview')}}?id="+res.test_id+'&type=add';
						}
					});
				
				});
				return false;
			});
			
		});

	</script>

@stop


@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop
@section('body')
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6">
                <h5 class="title-label">新增试卷</h5>
            </div>
        </div>	
        <div class="ibox-content">
        	<div class="row">
		        <form method="get" class="form-horizontal" action="">
	                
	               	
	                <div class="form-group">
	                    <label for="end" class="col-sm-4 control-label">试卷名称：<i></i></label>
	                    <div class="col-sm-5">
							<input type="text" placeholder="请填写试卷名称" class="form-control" name="name" />
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>
	                
	                
	                
	            	@foreach($data as $val)
		                <div class="form-group">
		                    <label for="end" class="col-sm-4 control-label">{{$val->typeValues[$val->type]}}（共{{$val->sum_count}}题），选</label>
		                    <div class="col-sm-2">
								<input type="text" placeholder="" class="form-control " name="number[]" />
		                    </div>
		                    <label for="end" class="col-sm-4 control-label control-name">题，每题分值</label>
		                    <div class="col-sm-2 padL0">
								<input type="text" placeholder="" class="form-control" name="score[]"/>
								<input type="hidden" name="type[]" value="{{$val->type}}" />
		                    </div>
		                    <label for="end" class="col-sm-4 control-label control-name">分</label>
		                </div>
		                <div class="hr-line-dashed"></div>		               	
	               	
	               	
	            	@endforeach 
	               
	                	
			
			        <div class="form-group">
	                    <div class="col-sm-4 col-sm-offset-4">
	                        <button class="btn btn-primary" type="submit">生成试卷</button>
	                        <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
	                    </div>
	                </div>
				</form>
			</div>
		</div>
		
		
			
</div>		


		
		
@stop

