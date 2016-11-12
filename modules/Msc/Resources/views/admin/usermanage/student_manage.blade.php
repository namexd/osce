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
			$(".btn-edit").click(function(){//确认修改验证
				var editName=$.trim($(".edit-name").val());
				var editCode=$.trim($(".edit-code").val());
				var editProfessional_name=$.trim($(".edit-professional_name").val());
				var editMobile=$.trim($(".edit-mobile").val());
				var editCard=$.trim($(".edit-idcard").val());
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
					layer.tips('证件号不能为空', '.edit-idcard', {
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
				var addProfession_name=$.trim($(".add-profession_name").val());
				var addMobile=$.trim($(".add-mobile").val());
				var addCard=$.trim($(".add-card").val());
				var reg=/^1[3|5|8]{1}[0-9]{9}$/;
				if(addName==""){
					layer.tips('用户名不能为空', '.add-name', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(addCode==""){
					layer.tips('学号不能为空', '.add-code', {
					    tips: [1, '#408AFF'],
					    time: 4000
					});
					return false;
				}
				if(addProfession_name==""){
					layer.tips('专业不能为空', '.add-profession_name', {
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
				if(addCard==""){
					layer.tips('证件号不能为空', '.add-card', {
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
					url: "/msc/admin/user/student-item/"+idName,
					async:false,
					success:function(res){
						var data=JSON.parse(res);
						console.log(data);
						$(".look-name").val(data.name);//姓名
						$(".look-code").val(data.code);//学号
						if(data.gender=="男"){
							$(".look-man").attr("checked","checked");
						}else if(data.gender=="女"){
							$(".look-woman").attr("checked","checked");
						}
						$(".look-grade").val(data.grade);//年级
						$(".look-student_type").find("option[text='"+data.student_type+"']").attr(".look-student_type",true);//类别
						$(".look-profession_name").val(data.profession_name);//专业
						$(".look-mobile").val(data.mobile);//手机
						$(".look-card").val(data.idcard);//证件号码

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
						$(".edit-name").val(data.name);//姓名
						$(".edit-hidden-name").val(idName);
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
			            url:'/msc/admin/user/import-student-user',
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
				window.location.href = "/msc/admin/user/export-student-user/?keyword="+keyword+"";
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
 <input type="hidden" id="parameter" value="{'pagename':'student_manage','ajaxurl':'{{route('msc.admin.user.StudentList')}}'}" />
<div class="panel blank-panel">
    <div class="panel-heading">
        <div class="panel-options">
            <ul class="nav nav-tabs">
                <li class="active"><a href="/msc/admin/user/student-list">学生管理</a></li>
                <li class=""><a href="/msc/admin/user/teacher-list">教职工管理</a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <div class="tab-content">
            <div id="tab-4" class="tab-pane active">
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
				        	<input type="button" class="right btn btn-blue" name="" id="new-add" value="新增学生" data-toggle="modal" data-target="#myModal"/>
				        	<!--<input type="button" class="right btn btn-default" name="" id="leading-out" value="导出"/>-->
				        	<!--<input type="button" class="right btn btn-default" name="" id="leading-in" value="导入"/>-->
				        	
		                    <!--<a href="/msc/admin/user/export-student-user" class="btn btn-default right leading-out" style="height: 30px;margin-left: 10px;background: #fff;">导出</a>-->
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
				                <th>学号</th>
				                <th>
				                	<div class="btn-group Examine">
				                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">年级<span class="caret"></span></button>
				                        <ul class="dropdown-menu">
				                            <li>
				                                <a href="#">2015</a>
				                            </li>
				                            <li>
				                                <a href="#">2014</a>
				                            </li>
				                        </ul>
				                    </div>
				                </th>
				                <th>
				                	<div class="btn-group Examine">
				                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">类别<span class="caret"></span></button>
				                        <ul class="dropdown-menu">
				                            <li>
				                                <a href="#">本科</a>
				                            </li>
				                            <li>
				                                <a href="#">专科</a>
				                            </li>
				                        </ul>
				                    </div>
				                </th>
				                <th>
				                	<div class="btn-group Examine">
				                        <button data-toggle="dropdown" class="btn btn-white dropdown-toggle" type="button">专业<span class="caret"></span></button>
				                        <ul class="dropdown-menu">
				                            <li>
				                                <a href="#">临床医学</a>
				                            </li>
				                        </ul>
				                    </div>
				                </th>
				                <th>
				                	手机号码
				                </th>
				                <th>
				                	证件号码
				                </th>
				                <th>
				                	性别
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
					                    <td>{{$list['grade']}}</td>
					                    <td>{{$list['student_type']}}</td>
					                    <td>{{$list['profession_name']}}</td>
					                    <td>{{$list['mobile']}}</td>
					                    <td>{{$list['idcard']}}</td>
					                    <td>{{$list['gender']}}</td>
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
            <div id="tab-5" class="tab-pane">
            </div>
        </div>
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
<form class="form-horizontal" id="Form1" novalidate="novalidate" action="/msc/admin/user/student-add" method="post" style="display: none;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">新增学生</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control name add-name" name="name" value="" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">学号</label>
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
            <label class="col-sm-2 control-label">年级</label>
            <div class="col-sm-10">
                <select class="form-control grade" id="" name="grade">
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">类别</label>
            <div class="col-sm-10">
                <select class="form-control student_type" id="" name="student_type">
                    <option value="1">本科</option>
                    <option value="2">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">专业</label>
            <div class="col-sm-10">
                <input type="text" class="form-control profession_name add-profession_name" name="profession_name" />
                <!--<select class="form-control profession_name" name="profession_name">
                	<option value="儿科">儿科</option>
                	<option value="设计">设计</option>
                </select>-->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control mobile add-mobile" name="mobile" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">证件</label>
            <div class="col-sm-4" style="padding-right: 0;">
                <select class="form-control idcard" id="" name="idcard_type">
                    <option value="0">证件类型</option>
                    <option value="1" selected="selected">身份证</option>
                    <option value="2">护照</option>
                </select>
            </div>
            <div class="col-sm-6" style="padding-left: 0;">
            	<input type="text" class="form-control card add-card" name="idcard" />
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
                <input type="text" class="form-control look-name" value="" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">学号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control look-code" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon look-man" name="student_type"  value="1" disabled="disabled"/> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon look-woman" name="student_type" value="2" disabled="disabled" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">年级</label>
            <div class="col-sm-10">
                <select class="form-control look-grade" id="" disabled="disabled">
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">类别</label>
            <div class="col-sm-10">
                <select class="form-control look-student_type" id="" disabled="disabled">
                    <option value="1">本科</option>
                    <option value="2">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 look-control-label">专业</label>
            <div class="col-sm-10">
                <input type="text"class="form-control look-profession_name" name="profession_name" disabled="disabled" />
                <!--<select class="form-control look-profession_name" name="professional" disabled="disabled">
                	<option value="1">儿科</option>
                	<option value="2">设计</option>
                </select>-->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control look-mobile" disabled="disabled" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">证件</label>
            <div class="col-sm-4" style="padding-right: 0;">
                <select class="form-control look-idcard" id="" disabled="disabled">
                    <option value="0">证件类型</option>
                    <option value="1" selected="selected">身份证</option>
                    <option value="2">护照</option>
                </select>
            </div>
            <div class="col-sm-6" style="padding-left: 0;">
            	<input type="text" class="form-control look-card" disabled="disabled" />
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
<form class="form-horizontal" id="Form3" novalidate="novalidate" action="/msc/admin/user/student-save" method="post" style="display: none;">
	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">编辑</h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label">姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-name" value="张三" name="name" />
                <input type="hidden" class="edit-hidden-name" value="" name="id"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">学号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-code" name="code"/>
            </div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2" style="padding-left: 15px;">
        		<input type="radio" class="check_icon edit-man" name="gender" value="1" /> <span style="padding-right: 40px;">男</span>
            	<input type="radio" class="check_icon edit-woman" name="gender" value="2" /> <span>女</span>
        	</div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">年级</label>
            <div class="col-sm-10">
                <select class="form-control edit-grade" id="" name="grade">
                    <option value="2015">2015</option>
                    <option value="2014">2014</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">类别</label>
            <div class="col-sm-10">
                <select class="form-control edit-student_type" id="" name="student_type">
                    <option value="1">本科</option>
                    <option value="2">专科</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">专业</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-professional_name" name="professional_name" />
                <!--<select class="form-control edit-professional_name" name="professional_name">
                	<option value="1">儿科</option>
                	<option value="2">设计</option>
                </select>-->
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">手机号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control edit-mobile" name="mobile" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">证件</label>
            <div class="col-sm-4" style="padding-right: 0;">
                <select class="form-control edit-idcard_type" id="" name="idcard_type">
                    <option value="0">证件类型</option>
                    <option value="1" selected="selected">身份证</option>
                    <option value="2">护照</option>
                </select>
            </div>
            <div class="col-sm-6" style="padding-left: 0;">
            	<input type="text" class="form-control edit-idcard" name="idcard" />
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