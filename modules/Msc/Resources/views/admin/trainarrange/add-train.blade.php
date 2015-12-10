@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
@stop

@section('only_js')
	<script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('msc/admin/trainarrange/trainarrange.js')}}"></script>
    <script type="text/javascript">
    	var start = {
		    elem: "#start",
		    format: "YYYY/MM/DD hh:mm:ss",
		    min: laydate.now(),
		    max: "2099-06-16 23:59:59",
		    istime: true,
		    istoday: false,
		    choose: function (a) {
		        end.min = a;
		        end.start = a
		    }
		};
		var end = {
		    elem: "#end",
		    format: "YYYY/MM/DD hh:mm:ss",
		    min: laydate.now(),
		    max: "2099-06-16 23:59:59",
		    istime: true,
		    istoday: false,
		    choose: function (a) {
		        start.max = a
		    }
		};
		$(function(){
			laydate(start);
			laydate(end);
			
			/*{}{
			 * 下面是进行插件初始化
			 * 你只需传入相应的键值对
			 * */
			$('#add-train-form').bootstrapValidator({
			    message: 'This value is not valid',
			    feedbackIcons: {/*输入框不同状态，显示图片的样式*/
			        valid: 'glyphicon glyphicon-ok',
			        invalid: 'glyphicon glyphicon-remove',
			        validating: 'glyphicon glyphicon-refresh'
			    },
			    fields: {/*验证*/
			        name: {/*键名username和input name值对应*/
			            validators: {
			                notEmpty: {/*非空提示*/
			                    message: '用户名不能为空'
			                },
			                stringLength: {
			                    min: 1,
			                    max: 50,
			                    message: '用户名长度必须在1-50之间'
			                }
			            }
			        }
			    }
			});
			$("#name").blur(function(){
				var name=$(this).val();
			})
		})
    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>培训安排</h5>
		</div>
		<div class="ibox-content">
			<div class="row marb_25 marl_5 mart_5">
				<span class="col-sm-2 marr_15 txta_l btn-blue">1.第一步</span>
				<span class="col-sm-2 marr_15 txta_l btn-ccc">2.第二步</span>
				<span class="col-xs-2 txta_l btn-ccc">3.第三步</span>
			</div>
			
			<form class="form-horizontal" action="{{url('/msc/admin/training/add-training')}}" method="post" id="add-train-form">
				<div class="form-group">
		            <label class="col-sm-1 control-label font12">培训名称</label>
		            <div class="col-sm-11">
		            	<input type="text" class="form-control" name="name" id="name">
		            </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训时间</label>
		            <div class="col-sm-11">
                        <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start" name="begindate">
                        <span class="arrange-go">至</span>
                        <input placeholder="结束日期" class="form-control layer-date laydate-icon" id="end" name="enddate">
                    </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <input type="submit" name="" id="" class="btn btn-primary col-sm-offset-2" value="下一步，导入学员信息" />
			</form>
		</div>
	</div>
</div>
@stop{{-- 内容主体区域 --}}