@extends('osce::admin.layouts.admin_index')

@section('only_css')
	<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
    <style>
	    button.btn.btn-white.dropdown-toggle {
	        border: none;
	        font-weight: bolder;
	    }
	    .blank-panel .panel-heading {margin-left: -20px;}
	    #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')
	<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
    <script type="text/javascript">
   		$(function(){
   			/**
		     * 下面是进行插件初始化
		     * 你只需传入相应的键值对
		     * */
		    $('#Form3').bootstrapValidator({
		            message: 'This value is not valid',
		            feedbackIcons: {/*输入框不同状态，显示图片的样式*/
		                valid: 'glyphicon glyphicon-ok',
		                invalid: 'glyphicon glyphicon-remove',
		                validating: 'glyphicon glyphicon-refresh'
		            },
		            fields: {/*验证*/
		                name: {/*键名username和input name值对应*/
		                    message: 'The username is not valid',
		                    validators: {
		                        notEmpty: {/*非空提示*/
		                            message: '姓名不能为空'
		                        },
		                        stringLength: {/*长度提示*/
		                            min: 2,
		                            max: 20,
		                            message: '姓名长度请在2到20之间'
		                        }/*最后一个没有逗号*/
		                    }
		                },
		                gender: {
		                    validators: {
		                        notEmpty: {
		                            message: '请选择性别'
		                        }
		                    }
		                },
		                mobile: {
			                 validators: {
			                    notEmpty: {
			                        message: '手机号码不能为空'
			                    },
			                    stringLength: {
			                        min: 11,
			                        max: 11,
			                        message: '请输入11位手机号码'
			                    },
			                    regexp: {
			                        regexp: /^1[3|5|8]{1}[0-9]{9}$/,
			                        message: '请输入正确的手机号码'
			                    }
			                }
			            },
		            }
		        });
       	});
    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>用户新增</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form class="form-horizontal" id="Form3" novalidate="novalidate" action="{{route('osce.admin.user.postAddUser')}}" method="post">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">姓名</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="name" value="">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
	                    <div class="form-group">
                    		<label class="col-sm-2 control-label">性别</label>
			                <div class="col-sm-offset-2" style="padding-left:15px;padding-top:5px;">
			                    <input type="radio" class="check_icon edit-man" name="gender" value="1"/>
								<span style="padding-right: 40px;">男</span>
			                    <input type="radio" class="check_icon edit-woman" name="gender" value="2" />
			                    <span>女</span>
			                </div>
			            </div>


                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">手机号</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="location" id="location" class="form-control" name="mobile">
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
    </div>

</div>
@stop{{-- 内容主体区域 --}}