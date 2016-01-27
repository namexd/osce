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
</script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
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
        	<form class="container-fluid"  id="list_form" method="post" action="#">
	             <div class="clearfix form-group">
	                <label class="col-sm-1 control-label" >说明内容:</label>
	                <div class="col-sm-11">
	                    <script id="editor" type="text/plain" style="width:100%;height:500px;cursor: text;" name="content"></script>
	                </div>
	            </div>
	            <div class="clearfix form-group">
	                <label class="col-sm-1 control-label" ></label>
	                <div class="col-sm-11">
	                	<input class="btn btn-primary save" type="submit" name="" id="" value="保存" />
	                </div>
	            </div>
	        </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}