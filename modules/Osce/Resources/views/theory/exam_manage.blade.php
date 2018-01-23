@extends('osce::theory.base')

@section('title')
	新增考试
@stop
@section('head_css')
	<style>
		.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
		    background-color: #fff;
		}		
		
	</style>
		
		

@stop	
@section('head_js')
	<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
	<script>
		$(function () {
			var start = {
				elem: '#start',
				format: 'YYYY-MM-DD hh:mm:ss',
				min: laydate.now(), //设定最小日期为当前日期
				max: '2099-06-16 23:59:59', //最大日期
				istime: true,
				istoday: false,
				choose: function(datas){
					end.min = datas; //开始日选好后，重置结束日的最小日期
					end.start = datas //将结束日的初始值设定为开始日
				}
			};
			var end = {
				elem: '#end',
				format: 'YYYY-MM-DD hh:mm:ss',
				min: laydate.now(),
				max: '2099-06-16 23:59:59',
				istime: true,
				istoday: false,
				choose: function(datas){
					start.max = datas; //结束日选好后，重置开始日的最大日期
				}
			};
			laydate.skin('molv');
			laydate(start);
			laydate(end);
			
			$('#rate_choose').change(function () {
				if ($(this).val()=='0') {
					$('.convert').addClass('hide');
					$('#convert').val('100');
				} else {
					$('#convert').val('');
					$('.convert').removeClass('hide');
				}
			});
		    $("#times,#convert").keyup(function () {
		        this.value = this.value.replace(/[^\d]/g, '');
		    });				
			
			$('.btn-primary').click(function () {
				
				if(noempty('.form-horizontal')){
					return false;
				}
				if ($('#rate_choose').val()=='1') {
					if ($.trim($('#convert').val())=='') {
						uselayer(3,'请填写统一折算率');
						$('#convert').focus();
						return false;						
					}
					if ($.trim($('#convert').val())<1||$.trim($('#convert').val())>100) {
						uselayer(3,'折算率范围为1-100');
						$('#convert').focus();
						return false;						
					}
				}
				uselayer(2,'确定要新增考试吗？',function () {
					$('.form-horizontal').submit();
				});
				return false;
			});
			
			$('#exam_id').change(function () {
				if ($(this).val()=='0') {
					$('.exam_name').removeClass('hide');
				} else {
					$('.exam_name').addClass('hide');
				}
			});
			
			
		});

	</script>

@stop


@section('body_attr') class="fixed-sidebar full-height-layout gray-bg"@stop
@section('body')
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6">
                <h5 class="title-label">新增考试</h5>
            </div>
        </div>	
        <div class="ibox-content">
        	<div class="row">
		        <form method="post" class="form-horizontal" action="{{route('osce.cexam.postAddExam')}}">
	                <div class="form-group">
	                    <label for="tid" class="col-sm-2 control-label">考题选择：<i></i></label>
	                    <div class="col-sm-5">
							<select name="tid" id="tid" class="form-control" placeholder="请选择考题">
								<option value="">请选择考题</option>
								@foreach($data['choose'] as $val)
									<option value="{{$val->id}}">{{$val->name}}</option>
								@endforeach
							</select>                   	
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>	
	                <div class="form-group">
	                    <label for="exam_id" class="col-sm-2 control-label">所属考试：<i></i></label>
	                    <div class="col-sm-5">
							<select name="exam_id" id="exam_id" class="form-control" placeholder="请选择所属考试">
								<option value="">请选择所属考试</option>
								<option value="0">无所属考试</option>
								@foreach($data['chooseexam'] as $val)
									<option value="{{$val->id}}">{{$val->name}}</option>
								@endforeach
							</select>
	                    </div>
	                </div>
	                <div class="form-group exam_name hide">
	                    <label for="exam_name" class="col-sm-2 control-label">考试名称：<i></i></label>
	                    <div class="col-sm-5">
							<input type="text" name="exam_name" id="exam_name"  placeholder="请填写考试名称" class="form-control" />
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>
	                <div class="form-group">
	                    <label for="rate_choose" class="col-sm-2 control-label">折算方式：</label>
						<div class="col-sm-5">
							<select id="rate_choose" class="form-control">
								<option value="0">不需要折算</option>
								<option value="1">统一折算率</option>
							</select>
						</div>
	                </div>
	                <div class="form-group convert hide">
	                    <label for="convert" class="col-sm-2 control-label">统一折算率：</label>
	                    <div class="col-sm-5">
							<input type="text" name="convert" id="convert" value="100" placeholder="请填写折算率(%)" class="form-control"  />
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>	
	                <div class="form-group">
	                    <label for="times" class="col-sm-2 control-label">考试时长：<i></i></label>
	                    <div class="col-sm-5">
							<input type="text" name="times" id="times"  placeholder="请填写考试时长(分钟)" class="form-control" />
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>	
	                <div class="form-group">
	                    <label for="start" class="col-sm-2 control-label">开始时间：<i></i></label>
	                    <div class="col-sm-5">
							<input type="text" readonly="readonly" placeholder="请选择开始时间" class="form-control sel-time" id="start" name="start" />
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>	
	                <div class="form-group">
	                    <label for="end" class="col-sm-2 control-label">结束时间：<i></i></label>
	                    <div class="col-sm-5">
							<input type="text" readonly="readonly" placeholder="请选择结束时间" class="form-control sel-time" id="end" name="end" />
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>	
	                <div class="form-group">
	                    <label for="bed_id" class="col-sm-2 control-label">监考老师：<i></i></label>
	                    <div class="col-sm-5">
							<select name="teacher" class="form-control" placeholder="请选择老师">
								<option value="">请选择老师</option>
								@foreach($data['chooseteacher'] as $val)
									<option value="{{$val->id}}">{{$val->name}}</option>
								@endforeach
							</select>	
	                    </div>
	                </div>
	                <div class="hr-line-dashed"></div>				
			        <div class="form-group">
	                    <div class="col-sm-4 col-sm-offset-2">
	                        <button class="btn btn-primary" type="submit">保存</button>
	                        <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
	                    </div>
	                </div>
				</form>
			</div>
		</div>
		
		
			
</div>		


		
		
@stop

