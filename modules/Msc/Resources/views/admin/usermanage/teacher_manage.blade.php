@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/msc/admin/usermanage/usermanage.css')}}"/>
@stop

@section('only_js')
	<script type="text/javascript" src="{{asset('/msc/admin/usermanage/usermanage.js')}}" ></script>
	
	<script type="text/javascript">
		$(function(){

		})
	</script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'user_manage','ajaxurl':''}" />
<div class="panel blank-panel">
    <div class="panel-heading">
        <div class="panel-options">
            <ul class="nav nav-tabs">
                <li class=""><a href="/msc/admin/user/student-list">学生管理</a></li>
                <li class="active"><a href="/msc/admin/user/teacher-list">教职工管理</a></li>
            </ul>
        </div>
    </div>
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6 col-md-3">
	            <form action="" method="get">
		            <div class="input-group">
		                <input type="text" id="keyword" name="keyword" placeholder="请输入关键字（姓名/学号）" class="input-sm form-control" value="{{ Input::get('keyword') }}">
			            <span class="input-group-btn">
			                <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
			            </span>
		            </div>
	            </form>
	        </div>
	        <div class="col-xs-6 col-md-9 user_btn">
	        	<input type="button" class="right btn btn-blue" name="" id="" value="新增学生" />
	        	<input type="button" class="right btn btn-default" name="" id="" value="导出" />
	        	<input type="button" class="right btn btn-default" name="" id="" value="导入" />
	        </div>
	    </div>
	    <form class="container-fluid ibox-content" id="list_form">
	        <table class="table table-striped" id="table-striped">
	            <thead>
	            <tr>
	                <th>#</th>
	                <th>姓名</th>
	                <th>胸牌号</th>
	                <th>
	                	<div class="btn-group Examine">
	                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">科室<span class="caret"></span></button>
	                        <ul class="dropdown-menu">
	                            <li>
	                                <a href="#">设备管理科</a>
	                            </li>
	                        </ul>
	                    </div>
	                </th>
	                <th>手机号码</th>
	                <th>性别</th>
	                <th>
	                	<div class="btn-group Examine">
	                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">角色<span class="caret"></span></button>
	                        <ul class="dropdown-menu">
	                            <li>
	                                <a href="#">设备管理员</a>
	                            </li>
	                        </ul>
	                    </div>
	                </th>
	                <th>
	                    <div class="btn-group Examine">
	                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">状态 <span class="caret"></span></button>
	                        <ul class="dropdown-menu">
	                            <li>
	                                <a href="#">正常</a>
	                            </li>
	                            <li>
	                                <a href="#">禁用</a>
	                            </li>
	                        </ul>
	                    </div>
	                </th>
	                <th>操作</th>
	            </tr>
	            </thead>
	            <tbody>
	            	@foreach($list as $list)
		            	<tr>
		                    <td>{{$list['id']}}</td>
		                    <td>{{$list['name']}}</td>
		                    <td>{{$list['code']}}</td>
		                    <td>{{$list['dept_name']}}</td>
		                    <td>{{$list['mobile']}}</td>
		                    <td>{{$list['gender']}}</td>
		                    <td>
		                    	@if(count($list['role'])==0)
		                    		-
		                    	@else
				                    @foreach($list['role'] as $key=>$li)
				                    	@if($key==0)
				                    		{{$li->name}}
				                    	@else
				                    	@endif
				                    @endforeach
			                    @endif
		                    </td>
		                    @if($list['status']=="禁用")
		                    	 <td class="status2">{{$list['status']}}</td>
		                    @else
		                    	<td>{{$list['status']}}</td>
		                    @endif
		                    <td>
		                    	<a href="#" class="status1">查看</a>
		                    	<a href="#" class="status1">编辑</a>
		                    	@if($list['status']=="禁用")
		                    	 	<a href="#" class="status4">恢复</a>
			                    @else
			                    	<a href="#" class="status2">禁用</a>
			                    @endif
		                    	<a href="#" class="status3">删除</a>
		                    </td>
		                </tr>
	            	@endforeach
	            </tbody>
	        </table>
	        <div class="btn-group pull-right">
	            <ul class="pagination">
	            	<li><a href="#" rel="prev">«</a></li>
					<li><a href="#">1</a></li>
					<li class="active"><span>2</span></li>
					<li class="disabled"><span>»</span></li>
				</ul>
	        </div>
	    </form>
	</div>
</div>
@stop{{-- 内容主体区域 --}}