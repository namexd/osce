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
    <script src="{{asset('osce/admin/systemManage/system_manage.js')}}" ></script>  
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'user_manage_add'}" />

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