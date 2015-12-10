@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
@stop

@section('only_js')
    <script src="{{asset('msc/admin/trainarrange/trainarrange.js')}}"></script>
    <script type="text/javascript">
  		
    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>
				使用历史详情
			</h5>
		</div>
		<div class="ibox-content form-horizontal">
			<div class="form-group">
	            <label class="col-sm-2 control-label font12">开放设备名称</label>
	            <div class="col-sm-10 padt_7">
					{{$data['name']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">使用时间段</label>
	            <div class="col-sm-10 padt_7">
	            	{{date('H:i',strtotime($data['original_begin_datetime']))}}-{{date('H:i',strtotime($data['original_end_datetime']))}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">编号</label>
	            <div class="col-sm-10 padt_7">
	            	{{$data['code']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">使用者</label>
	            <div class="col-sm-10 padt_7">
	            	{{$data['student_name']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">使用理由</label>
	            <div class="col-sm-10 padt_7">
	            	{{$data['detail']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">地址</label>
	            <div class="col-sm-10 padt_7">
	            	{{$data['address']}}
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">设备状态</label>
	            <div class="col-sm-10 padt_7">
					@if($data['health'] === 1)
						完好
					@elseif($data['health'] === 2)
						损坏
					@else
						-
					@endif
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
	        <div class="form-group">
	            <label class="col-sm-2 control-label font12">是否复位设备</label>
	            <div class="col-sm-10 padt_7">
					@if($data['init'] === 1)
	            	是
					@elseif($data['init'] === 2)
					否
					@else
					-
					@endif
	            </div>
	        </div>
	        <div class="hr-line-dashed"></div>
		</div>
	</div>
	@stop{{-- 内容主体区域 --}}
