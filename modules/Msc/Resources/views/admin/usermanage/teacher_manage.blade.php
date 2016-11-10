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
	<script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
	<script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
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
					url:"/msc/admin/user/teacher-trashed/"+idName,
					async:true
				});
				 history.go(0);
			})
			$(".btn-forbidden,#recover").click(function(){//禁用恢复
				$.ajax({
					type:"get",
					url:"/msc/admin/user/teacher-status/"+idName,
					async:true
				});
				 history.go(0);
			})
			$(".btn-edit").click(function(){//确认修改验证
				var editName=$.trim($(".edit-name").val());
				var editCode=$.trim($(".edit-code").val());
				var editDept_name=$.trim($(".edit-dept_name").val());
				var editRole=$.trim($(".edit-role").val());
				var editMobile=$.trim($(".edit-mobile").val());
				var reg=/^1[3|5|8]{1}[0-9]{9}$/;
				if(editName==""){
					layer.tips('姓名不能为空', '.edit-name', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(editCode==""){
					layer.tips('胸牌号不能为空', '.edit-code', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(editDept_name==""){
					layer.tips('科室不能为空', '.edit-dept_name', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(editRole==""){
					layer.tips('角色不能为空', '.edit-role', {
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
				$("#Form3").submit();
			})
			$(".btn-new-add").click(function(){//新增学生验证
				var addName=$.trim($(".add-name").val());
				var addCode=$.trim($(".add-code").val());
				var addDept_name=$.trim($(".add-dept_name").val());
				var addRole=$.trim($(".add-role").val());
				var addMobile=$.trim($(".add-mobile").val());
				var reg=/^1[3|5|8]{1}[0-9]{9}$/;
				if(addName==""){
					layer.tips('姓名不能为空', '.add-name', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(addCode==""){
					layer.tips('胸牌号不能为空', '.add-code', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(addDept_name==""){
					layer.tips('科室不能为空', '.add-dept_name', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(addRole==""){
					layer.tips('角色不能为空', '.add-role', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(addMobile==""){
					layer.tips('手机号不能为空', '.add-mobile', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(!reg.test(addMobile)){
					layer.tips('请输入正确的手机号码', '.add-mobile', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				$("#Form1").submit();
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
					url:"/msc/admin/user/teacher-edit/"+idName,
					async:true,
					success:function(res){
						var data=JSON.parse(res);
						$(".edit-name").val(data.name);//姓名
						$(".edit-hidden-name").val(idName);
						$(".edit-code").val(data.code);//胸牌号
						if(data.gender=="男"){
							$(".edit-man").attr("checked","checked");
						}else if(data.gender=="女"){
							$(".edit-woman").attr("checked","checked");
						}
						$(".edit-dept_name").val(data.dept_name)//科室
						if(data.role.length>0){
							$(".edit-role").val(data.role[0])//角色
						}
						$(".edit-mobile").val(data.mobile);//手机
					}
				});
			}
			$("#in").click(function(){
				$("#leading-in").click();
			})
			$("#leading-in").change(function(){
				var str=$("#leading-in").val().substring($("#leading-in").val().lastIndexOf(".")+1);
				if(str!="xlsx"){
					layer.alert(
	                  "请上传正确的文件格式？", 
	                  {title:["温馨提示","font-size:16px;color:#408aff"]}
	              );
				}else{
					$.ajaxFileUpload({
						type:"post",
			            url:'/msc/admin/user/import-teacher-user',
			            fileElementId:'leading-in',//必须要是 input file标签 ID
			            success: function (data, status){
			            	
			            },
			            error: function (data, status, e){
			            	console.log("失败");
			               layer.alert(
			                  "上传失败！", 
			                  {title:["温馨提示","font-size:16px;color:#408aff"]}
			               );
			            }
			        });
				}
			})
			$(".leading-out").click(function(){
				var keyword=$("#keyword").val();
				window.location.href = "/msc/admin/user/export-teacher-user?keyword="+keyword+"";
			})
			var message=$(".message").text();
			if(message.length>0){
				layer.alert(
	              ""+message+"", 
	              {title:["温馨提示","font-size:16px;color:#408aff"]}
	            );
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
	        	<!--<a href="/msc/admin/User/import-Teacher-user" class="btn btn-default right leading-out" style="height: 30px;margin-left: 10px;background: #fff;">导出</a>-->
				<input type="button" class="btn btn-default right leading-out" style="background: #fff;" value="导出">
	        	<div class="right">
                    <input type="button" name="" id="in" value="导入" class="btn btn-default right" style="background: #fff;" />
                    <input type="file" name="training" id="leading-in" value="" style="display: none;"/>
                </div>
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
				<?php echo $pagination->render();?>
	        </div>
	    </form>
	</div>
</div>
@if (count($errors) > 0)
  	<div style="display: none;">
        <ul>
          @foreach ($errors->all() as $error)
            <li class="message">{{ $error }}</li>
          @endforeach
        </ul>
    </div>
    @endif
@stop

@section('layer_content')
<!--新增-->
<form class="form-horizontal" id="Form1" novalidate="novalidate" action="/msc/admin/user/teacher-add" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">新增教职工</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control name add-name" value="" name="name" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">胸牌号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control code add-code" name="code" />
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
                <input type="text" class="form-control dept_name add-dept_name" name="dept_name" id="" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">角色</label>
            <div class="col-sm-10">
                <input type="text" class="form-control role add-role" name="role" id=""/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control mobile add-mobile" name="mobile" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2">
        		<button type="button" class="btn btn-primary btn-new-add" data-dismiss="modal" aria-hidden="true">确定</button>
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
        		<button type="submit" class="btn btn-primary btn-look" data-dismiss="modal" aria-hidden="true">确定</button>
        	</div>
        </div>
    </div>
</form>
<!--编辑-->
<form class="form-horizontal" id="Form3" novalidate="novalidate" action="/msc/admin/user/teacher-save" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">编辑</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control name edit-name" value="" name="name" />
                <input type="hidden" class="edit-hidden-name" value="" name="id"/>
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
        		<input type="radio" class="check_icon edit-man" name="gender"  value="1"/> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon edit-woman" name="gender" value="0" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">科室</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-dept_name" name="dept_name" id=""/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">角色</label>
            <div class="col-sm-10">
            	<input type="text" class="form-control edit-role" name="role" id="" />
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
        		<button type="button" class="btn btn-primary btn-edit" data-dismiss="modal" aria-hidden="true">确定</button>
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