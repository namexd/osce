@extends('osce::admin.layouts.admin_index')
@section('only_css')
	<style type="text/css">
		.ibox-content{}
	</style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor1.config.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.all.min.js')}}"></script>
<script src="{{asset('osce/admin/plugins/js/plugins/UEditor/lang/zh-cn/zh-cn.js')}}"></script>
<script type="text/javascript" >
	var ue = UE.getEditor('editor');
	$(function(){
		@if(isset($_GET['suc']) && $_GET['suc']==1 && empty($errors->getMessages()))
            layer.msg('保存成功！',{skin:'msg-success',icon:1});
		@endif
    })
</script>

@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
    	<div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="{{route('osce.admin.exam.getEditExam',['id'=>$id])}}">基础信息</a></li>
						<li class=""><a href="{{route('osce.admin.exam.getChooseExamArrange',['id'=>$id])}}">考场安排</a></li>
						<li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
						<li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                        <li class="active"><a href="{{route('osce.admin.exam.getExamRemind',['id'=>$id])}}">待考区说明</a></li>
                    </ul>
                </div>
            </div>
            <div class="clearfix form-group"></div>
        	<form class="container-fluid"  id="list_form" method="post" action="{{route('osce.admin.exam.postExamRemind')}}">
				<input type="hidden" name="id" value="{{$id}}">
	        	<div class="clearfix form-group">
	            	<label class="col-sm-1 control-label" >说明内容:</label>
	                <div class="col-sm-11">
	                    <script id="editor" type="text/plain" style="width:100%;height:500px;cursor: text;" name="content">
							{!! (!empty($data['rules'])?strip_tags($data['rules']):'') !!}
						</script>
	                </div>
	            </div>
	            <div class="clearfix form-group">
	                <label class="col-sm-1 control-label" ></label>
	                <div class="col-sm-11">
	                	<input class="btn btn-primary save" type="submit"  value="保存"  style="display: {{$data['status']==0?'':'none;'}}"/>
						<a class="btn btn-white" href="{{route("osce.admin.exam.getExamList")}}">取消</a>
	                </div>
	            </div>
	        </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}