@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/msc/admin/usermanage/usermanage.css')}}"/>

@stop

@section('only_js')

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
						            <span class="input-group-btn">
						                <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
						            </span>
					            </div>
				            </form>
				        </div>
				        <div class="col-xs-6 col-md-9 user_btn">
				        	<input type="button" class="right btn btn-blue" name="" id="new-add" value="新增实验室" data-toggle="modal" data-target="#myModal"/>
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
									<div class="btn-group Examine">
										<button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">类别<span class="caret"></span></button>
										<ul class="dropdown-menu">
											<li>
												<a href="#">普通实验室</a>
											</li>
											<li>
												<a href="#">开放实验室</a>
											</li>
										</ul>
									</div>
								</th>
				                <th>编号</th>
				                <th>
				                	<div class="btn-group Examine">
				                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">负责人<span class="caret"></span></button>
				                        <ul class="dropdown-menu">
				                            <li>
				                                <a href="#">李老师</a>
				                            </li>
				                            <li>
				                                <a href="#">张老师</a>
				                            </li>
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
				                                <a href="#">已预约</a>
				                            </li>
				                            <li>
				                                <a href="#">可预约</a>
				                            </li>
				                        </ul>
				                    </div>
				                </th>
				                <th>操作</th>
				            </tr>
				            </thead>
				            <tbody>
								<tr>
									<td class="idName">11</td>
									<td class="userName">心肺听诊实验室</td>
									<td>普通实验室</td>
									<td>P1234</td>
									<td>李老师</td>
									<td>18888888888</td>
									<td>已预约</td>
									<td>
										<a href="#" class="status1" id="look" data-toggle="modal" data-target="#myModal">详情</a>
										<a href="#" class="status1" id="edit" data-toggle="modal" data-target="#myModal">编辑</a>
									</td>
								</tr>
								<tr>
									<td class="idName">11</td>
									<td class="userName">心肺听诊实验室</td>
									<td>普通实验室</td>
									<td>P1234</td>
									<td>李老师</td>
									<td>18888888888</td>
									<td class="state3">可预约</td>
									<td>
										<a href="#" class="status1" id="look" data-toggle="modal" data-target="#myModal">详情</a>
										<a href="#" class="status1" id="edit" data-toggle="modal" data-target="#myModal">编辑</a>
									</td>
								</tr>
								<tr>
									<td class="idName">11</td>
									<td class="userName">心肺听诊实验室</td>
									<td>普通实验室</td>
									<td>P1234</td>
									<td>李老师</td>
									<td>18888888888</td>
									<td class="state4">不允许预约</td>
									<td>
										<a href="#" class="status1" id="look" data-toggle="modal" data-target="#myModal">详情</a>
										<a href="#" class="status1" id="edit" data-toggle="modal" data-target="#myModal">编辑</a>
									</td>
								</tr>
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
            <div id="tab-5" class="tab-pane">
            </div>
        </div>
    </div>
</div>
@stop

