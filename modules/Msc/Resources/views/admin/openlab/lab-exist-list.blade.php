@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/msc/admin/usermanage/usermanage.css')}}"/>

@stop

@section('only_js')
	<script>
		$('.user_btn').delegate('.addnew','click',function(){
			alert('qweqw');
			window.localcation.href="/msc/admin/lab/had-open-lab-add";
		});
	</script>
@stop

@section('content')
 <input type="hidden" id="parameter"/>
<div class="panel blank-panel">
    <div class="panel-body">
        <div class="tab-content">
            <div id="tab-4" class="tab-pane active">
            	<div class="wrapper wrapper-content animated fadeInRight">
				    <div class="row table-head-style1 ">
				        <div class="col-xs-6 col-md-3">
				        	<form action="" method="get">
					            <div class="input-group">
					                <input type="text" id="keyword" name="keyword" placeholder="搜索" class="input-sm form-control" value="{{ Input::get('keyword') }}">
									<input type="hidden" name="status" value="{{$status}}">
									<input type="hidden" name="manager_name" value="{{$manager}}">
									<input type="hidden" name="opened" value="{{$opened}}">
									<span class="input-group-btn">
						                <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
						            </span>
					            </div>
				            </form>
				        </div>
				        <div class="col-xs-6 col-md-9 user_btn">
							<a href="/msc/admin/lab/had-open-lab-add" class="right btn btn-blue">新增实验室</a>
				        	{{--<input type="button" class="right btn btn-blue" name="" id="new-add" value="新增实验室"/>--}}
				        	<!--<input type="button" class="right btn btn-default" name="" id="leading-out" value="导出"/>-->
				        	<!--<input type="button" class="right btn btn-default" name="" id="leading-in" value="导入"/>-->

		                    <!--<a href="/msc/admin/user/export-student-user" class="btn btn-default right leading-out" style="height: 30px;margin-left: 10px;background: #fff;">导出</a>-->
				        </div>
				    </div>
				    <form class="container-fluid ibox-content" id="list_form">
				        <table class="table table-striped" id="table-striped">
				            <thead>
				            <tr>
				                <th>#</th>
				                <th>名称</th>
								<th>
									{{--<input type="hidden" name="status" value="{{$status}}">--}}
									{{--<input type="hidden" name="manager_name" value="{{manager_name}}">--}}
									{{--<input type="hidden" name="opened" value="{{opened}}">--}}

									<div class="btn-group Examine">
										<button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">类别<span class="caret"></span></button>
										<ul class="dropdown-menu">
											<li>
												<a href="{{url('/msc/admin/lab/had-open-lab-list?opened=0&keyword='.$keyword.'&status='.$status.'&manager='.$manager)}}">普通实验室</a>
											</li>
											<li>
												<a href="{{url('/msc/admin/lab/had-open-lab-list?opened=1&keyword='.$keyword.'&status='.$status.'&manager='.$manager)}}">开放实验室预约</a>
											</li>
											<li>
												<a href="{{url('/msc/admin/lab/had-open-lab-list?opened=2&keyword='.$keyword.'&status='.$status.'&manager='.$manager)}}">开放实验室设备预约</a>
											</li>
										</ul>
									</div>
								</th>
				                <th>编号</th>
				                <th>
				                	<div class="btn-group Examine">
				                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">负责人<span class="caret"></span></button>
				                        <ul class="dropdown-menu">
											@if(!empty($manager_name))
											@foreach($manager_name as $manager1)
				                            <li>
				                                <a href="{{url('/msc/admin/lab/had-open-lab-list?opened='.$opened.'&keyword='.$keyword.'&status='.$status.'&manager='.$manager1)}}">{{@$manager1}}</a>
				                            </li>
				                           @endforeach
												@endif
				                        </ul>
				                    </div>
				                </th>
								<th>
									负责人电话
								</th>
				                <th>
				                	<div class="btn-group Examine">
				                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">状态<span class="caret"></span></button>
				                        <ul class="dropdown-menu">
				                            <li>
				                                <a href="{{url('/msc/admin/lab/had-open-lab-list?opened='.$opened.'&keyword='.$keyword.'&status=2&manager='.$manager)}}">已预约</a>
				                            </li>
				                            <li>
				                                <a href="{{url('/msc/admin/lab/had-open-lab-list?opened='.$opened.'&keyword='.$keyword.'&status=1&manager='.$manager)}}">正常</a>
				                            </li>
											<li>
												<a href="{{url('/msc/admin/lab/had-open-lab-list?opened='.$opened.'&keyword='.$keyword.'&status=0&manager='.$manager)}}">禁止预约</a>
											</li>
				                        </ul>
				                    </div>
				                </th>
				                <th>操作</th>
				            </tr>
				            </thead>
				            <tbody>
							@if(!empty($pagination))
							@foreach($pagination as $k=>$data)
								<tr>
									<td class="idName">{{@$k}}</td>
									<td class="userName">{{@$data->name}}</td>
									<td>
										@if($data['opened'] == 1)
											开发实验室
										@elseif($data['opened'] == 1)
											开发实验室设备预约
										@else
											普通实验室
										@endif
									</td>
									<td>{{@$data->code}}</td>
									<td>{{@$data->manager_name}}</td>
									<td>{{@$data->manager_mobile}}</td>
									<td>
										@if($data['status'] == 1)
											正常
										@elseif($data['status'] == 1)
											已预约
										@else
											禁止预约
										@endif
									</td>
									<td>
										<a href="/msc/admin/lab/had-open-lab-detail?id={{@$data->id}}" class="status1" id="look" data-toggle="modal" data-target="#myModal">详情</a>
										<a href="/msc/admin/lab/had-open-lab-edit?id={{@$data->id}}" class="status1" id="edit" data-toggle="modal" data-target="#myModal">编辑</a>
									</td>
								</tr>
							@endforeach
								@endif
				            </tbody>
				        </table>
				        <div class="btn-group pull-right">
							{!! $pagination->render() !!}
				        </div>
				    </form>
				</div>
            </div>
            <div id="tab-5" class="tab-pane">
            </div>
        </div>
    </div>
</div>
@stop

