@extends('osce::admin.layouts.admin_index')

@section('only_css')
	<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
    <style>
	    button.btn.btn-white.dropdown-toggle {
	        border: none;
	        font-weight: bolder;
	    }
	    .blank-panel .panel-heading {margin-left: -20px;}
		.col-sm-1{margin-top: 6px;}
		.check_label{top: 8px;}
		.check_icon.check,.check_icon{vertical-align: middle;}
		.form-group.col-sm-1{margin-bottom: 0!important;}
	    #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')
	<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>角色选择</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                        <div>
							<form id="sourceForm" method="post" class="form-horizontal" action="{{ route('osce.admin.user.postEditUserRole') }}">
							<div class="form-group">
								<label class="col-sm-2 control-label">角色选择:</label>
								<div class="col-sm-10">
									<select class="form-control" name="role_id" style="width: 500px;">
										    @if($role_id)
											<option value="{{ $role_id->role_id }}" selected="selected">{{ $role_id->role->name }}</option>
										    @endif
										    @foreach($data as $item)
											<option value="{{ $item['role_id'] }}">{{ $item['role_name'] }}</option>
											@endforeach
									</select>
								</div>
							</div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
								<input type="hidden" name="user_id" value="{{ $user_id }}">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
                            </div>
                        </div>
				</form>

                    </form>
                </div>

            </div>
        </div>
    </div>

</div>
@stop{{-- 内容主体区域 --}}