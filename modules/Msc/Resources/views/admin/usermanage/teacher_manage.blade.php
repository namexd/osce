@extends('msc::admin.layouts.admin')
@section('only_css')
    <link rel="stylesheet" type="text/css" href="{{asset('/msc/admin/usermanage/usermanage.css')}}"/>
    <style>
    	.modal-content{width: 500px;}
    	.check_icon{margin: 0!important;}
    	#Form1 .btn-primary,#Form2 .btn-primary,#Form3 .btn-primary,#Form4 .btn-primary,#Form5 .btn-primary{padding:6px 20px;margin:10px 0 0 15px;}
    </style>
@stop

@section('only_js')
	<script type="text/javascript" src="{{asset('/msc/admin/usermanage/usermanage.js')}}" ></script>
	
	<script type="text/javascript">
		$(function(){
			for(var i=0;i<$(".table tr").length;i++){
				$("#false-del").parents("tr").remove();//假删除数据隐藏
			}
			var idName;
			$(".table a").click(function(){
				idName=$(this).parents("tr").children(".idName").text();
				$("#Form1,#Form2,#Form3,#Form4,#Form5").css("display","none");
				var className=$(this).attr("id");
				switch(className){
					case "look":
						look();
						$("#Form2").css("display","block");
						break;
					case "edit":
						edit();
						$("#Form3").css("display","block");
						break;
					case "forbidden":
						$("#Form4 .modal-body").text("确认禁用"+$(this).parents("tr").children(".userName").text()+"用户？")
						$("#Form4").css("display","block");
						break;
					case "del":
						$("#Form5 .modal-body").text("确认删除"+$(this).parents("tr").children(".userName").text()+"用户？");
						$("#Form5").css("display","block");
						break;
				}
			})
			$("#new-add").click(function(){//新增
				$("#Form1,#Form2,#Form3,#Form4,#Form5").css("display","none");
				$("#Form1").css("display","block");
			})
			$(".btn-del").click(function(){//确认删除
				$.ajax({
					type:"get",
					url:"/msc/admin/user/student-trashed/"+idName,
					async:true
				});
				 history.go(0);
			})
			$(".btn-forbidden,#recover").click(function(){//禁用恢复
				$.ajax({
					type:"get",
					url:"/msc/admin/user/student-status/"+idName,
					async:true
				});
				 history.go(0);
			})
			$(".btn-edit,.btn-new-add").click(function(){//确认修改、新增学生验证
				var editName=$.trim($(".edit-name").val());
				var editCode=$.trim($(".edit-code").val());
				var editProfessional_name=$.trim($(".edit-professional_name").val());
				var editMobile=$.trim($(".edit-mobile").val());
				var editCard=$.trim($(".edit-card").val());
				var reg=/^1[3|5|8]{1}[0-9]{9}$/;
				if(editName==""){
					layer.tips('用户名不能为空', '.edit-name', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(editCode==""){
					layer.tips('学号不能为空', '.edit-code', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(editProfessional_name==""){
					layer.tips('专业不能为空', '.edit-professional_name', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(editMobile==""){
					layer.tips('手机号不能为空', '.edit-mobile', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(!reg.test(editMobile)){
					layer.tips('请输入正确的手机号码', '.edit-mobile', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(editCard==""){
					layer.tips('证件号不能为空', '.edit-card', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				history.go(0);
			})

			function look(){//查看
				$.ajax({
					type:"get",
					url: "/msc/admin/user/teacher-item/"+idName,
					async:false,
					success:function(res){
						var data=JSON.parse(res);
						$(".look-name").val(data.name);//姓名
						$(".look-code").val(data.code);//胸牌号
						if(data.gender=="男"){
							$(".look-man").attr("checked","checked");
						}else if(data.gender=="女"){
							$(".look-woman").attr("checked","checked");
						}
						$(".look-dept_name").val(data.dept_name)//科室
						if(data.role.length>0){
							$(".look-role").val(data.role[0])//角色
						}
						$(".look-mobile").val(data.mobile);//手机
						
					}
				});
			}
			function edit(){//修改
				$.ajax({
					type:"get",
					url:"/msc/admin/user/student-edit/"+idName,
					async:true,
					success:function(res){
						var data=JSON.parse(res);
						console.log(data);
						$(".edit-name").val(data.name);//姓名
						$(".edit-code").val(data.code);//学号
						if(data.gender=="男"){
							$(".edit-man").attr("checked","checked");
						}else if(data.gender=="女"){
							$(".edit-woman").attr("checked","checked");
						}
						$(".edit-grade").val(data.grade);//年级
						$(".edit-student_type").find("option[text='"+data.student_type+"']").attr(".edit-student_type",true);//类别
						$(".edit-professional_name").val(data.profession_name)//专业
						$(".edit-mobile").val(data.mobile);//手机
						$(".edit-card").val(data.idcard);//证件号码
					}
				});
			}
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
	        	<input type="button" class="right btn btn-blue" name="" id="new-add" value="新增教职工" data-toggle="modal" data-target="#myModal" />
	        	<a href="/msc/admin/user/export-teacher-user" class="btn btn-default right" style="height: 30px;margin-left: 10px;background: #fff;">导出</a>
	        	<input type="button" class="right btn btn-default" name="" id="leading-out" value="导入" style="background: #fff;" />
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
		                    <td class="idName">{{$list['id']}}</td>
		                    <td class="userName">{{$list['name']}}</td>
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
		                    	<a href="#" class="status1" id="look" data-toggle="modal" data-target="#myModal">查看</a>
					            <a href="#" class="status1" id="edit" data-toggle="modal" data-target="#myModal">编辑</a>
		                    	@if($list['status']=="禁用")
			                    	<a href="#" class="status4" id="recover">恢复</a>
			                    @elseif($list['status']=="删除")
			                    	<a href="#" class="status4" id="false-del">删除</a> 
			                    @else
			                    	<a href="#" class="status2" id="forbidden" data-toggle="modal" data-target="#myModal">禁用</a>
			                    @endif
		                    	<a href="#" class="status3" id="del" data-toggle="modal" data-target="#myModal">删除</a>
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
@stop

@section('layer_content')
<!--新增-->
<form class="form-horizontal" id="Form1" novalidate="novalidate" action="" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">新增教职工</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control name edit-name" value="" name="name" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">胸牌号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control code edit-code" name="code" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon" name="gender"  value="1"/> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon" name="gender" value="2" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">科室</label>
            <div class="col-sm-10">
                <select class="form-control student_type" name="student_type" id="">
                    <option value="">本科</option>
                    <option value="">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">角色</label>
            <div class="col-sm-10">
                <select class="form-control student_type" name="student_type" id="">
                    <option value="">本科</option>
                    <option value="">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control mobile edit-mobile" name="mobile" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2">
        		<button type="submit" class="btn btn-primary btn-new-add" data-dismiss="modal" aria-hidden="true">确定</button>
        	</div>
        </div>
    </div>
</form>
<!--查看-->
<form class="form-horizontal" id="Form2" novalidate="novalidate" action="" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">查看</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control name look-name" value="" name="name" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">胸牌号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control code look-code" name="code" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon look-man" name="gender"  value="1" disabled="disabled"/> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon look-woman" name="gender" value="0" disabled="disabled" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">科室</label>
            <div class="col-sm-10">
                <input type="text" class="form-control look-dept_name" name="dept_name" id="" disabled="disabled"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">角色</label>
            <div class="col-sm-10">
            	<input type="text" class="form-control look-role" name="role" id="" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control mobile look-mobile" name="mobile" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2">
        		<button type="submit" class="btn btn-primary btn-new-add" data-dismiss="modal" aria-hidden="true">确定</button>
        	</div>
        </div>
    </div>
</form>
<!--编辑-->
<form class="form-horizontal" id="Form3" novalidate="novalidate" action="" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">编辑</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control name edit-name" value="" name="name" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">胸牌号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control code edit-code" name="code" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon" name="gender"  value="1"/> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon" name="gender" value="0" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">科室</label>
            <div class="col-sm-10">
                <select class="form-control student_type" name="student_type" id="">
                    <option value="">本科</option>
                    <option value="">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">角色</label>
            <div class="col-sm-10">
                <select class="form-control student_type" name="student_type" id="">
                    <option value="">本科</option>
                    <option value="">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control mobile edit-mobile" name="mobile" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2">
        		<button type="submit" class="btn btn-primary btn-new-add" data-dismiss="modal" aria-hidden="true">确定</button>
        	</div>
        </div>
    </div>
</form>
<!--禁用-->
<form class="form-horizontal" id="Form4" novalidate="novalidate" action="" method="post" style="display: none;">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">禁用</h4>
    </div>
    <div class="modal-body">
                    确定禁用xxx用户?
    </div>
    <div class="form-group" style="text-align: center;">
    	<button type="button" class="btn btn-primary btn-forbidden" data-dismiss="modal" aria-hidden="true">确定</button>
    </div>
</form>
<!--删除-->
<form class="form-horizontal" id="Form5" novalidate="novalidate" action="" method="post" style="display: none;">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">删除</h4>
    </div>
    <div class="modal-body">
                    确定删除xxx用户?
    </div>
    <div class="form-group" style="text-align: center;">
    	<button type="button" class="btn btn-primary btn-del" data-dismiss="modal" aria-hidden="true">确定</button>
    </div>
</form>
@stop{{-- 内容主体区域 --}}