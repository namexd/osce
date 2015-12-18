@extends('msc::admin.layouts.admin')

@section('only_css')
<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
@stop

@section('only_js')
<script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
<script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
<script>
	$(document).ready(function() {
		function action(){
			$(".sortable-list").sortable({
				connectWith: ".connectList"
			}).disableSelection();
		}
		
		//上传分组
		$("#file_span").change(function(){
			var str=$("#group_file").val().substring($("#group_file").val().lastIndexOf(".")+1);
			if(str!="xlsx"){
				layer.alert(
                  "请上传正确的文件格式？", 
                  {title:["温馨提示","font-size:16px;color:#408aff"]}
               );
			}else{
				$.ajaxFileUpload({
		            url:'/msc/admin/training/import-training-group',
		            secureuri:false,//
		            fileElementId:'group_file',//必须要是 input file标签 ID
		            dataType: 'json',//
		            success: function (data, status)
		            {
		            	$("#training_group_num").val(data['grpNum']);
		            	$("#student_number").text(data['stuNum']);
		            	
		            	var arr=[];
		            	for (var b=0;b<data['stuNum'];b++) {
		            		var group=data['group'][b].group;
		            		if($.inArray(group, arr)=="-1"){
		            			arr.push(group);
		            		}
		            	}
		            	$("#group_box").append('<input type="hidden" name="training_group_num" id="training_group_num" value="'+data['grpNum']+'" />');
		            	for (var a=0;a<data['grpNum'];a++){
		            		var str='<div class="col-sm-4">'+
										'<div class="ibox">'+
											'<div class="agile_box">'+
												'<p class="agile_tit">'+arr[a]+'组</p>'+
												'<ul id="group'+a+'"  data-group="'+arr[a]+'"  class="sortable-list connectList agile-list clearfix">'+
												'</ul>'+
											'</div>'+
										'</div>'+
									'</div>';
							$("#group_box").append(str);
							$("#group_selected").append('<option value="'+a+'">'+arr[a]+'组</option>');
							$("#add_student").show();
		            	}
		            	for (var i=0;i<data['stuNum'];i++) {
		            		var group=data['group'][i].group;
							var index=arr.indexOf(group);
							$("#group"+index).append('<li data-name="'+data['group'][i].name+'" data-mobile="'+data['group'][i].mobile+'">'+data['group'][i].name+'('+data['group'][i].mobile+')</li>');
		            	}
		            	$("#student_number").text(data['stuNum']);
		            	action();
		            	$("#group_submit").removeAttr("disabled");
		            },
		            error: function (data, status, e)
		            {
		               console.log(data);
		               layer.alert(
		                  "上传失败！", 
		                  {title:["温馨提示","font-size:16px;color:#408aff"]}
		               );
		            }
		        });
			}
		});
		
		$("#return").click(function(){
			//询问框
			layer.confirm('您确定要返回上一步？这可能会造成数据丢失！', {
			    btn: ['确定','取消'] //按钮
			}, function(){
			    history.go(-1);
			}, function(){
				
			});
		})
		$("#group_submit").click(function(){
			for ( var i=0;i<$(".sortable-list").length;i++) {
				var group=$(".sortable-list").eq(i).attr("data-group");
				for (var j=0;j<$(".sortable-list").eq(i).children("li").length;j++) {
					var name=$(".sortable-list").eq(i).children("li").eq(j).attr("data-name");
					var mobile=$(".sortable-list").eq(i).children("li").eq(j).attr("data-mobile");
					obj='group:'+group+',name:'+name+',mobile:'+mobile;
					str='<input type="hidden" name="training_group_student[]" id="" value="'+obj+'" />';
					$("#group_hidden").append(str);
				}
			}
			$("#group_form").submit();
		})
		
			
		$("#add_btn").click(function(){
			var name=$(".student_name").val();
			var tel=$(".student_tel").val();
			var group=$("#group_selected").val();
			var num=parseInt($("#student_number").text());
			var reg=/^1[3|5|8]{1}[0-9]{9}$/;
			var arr=[];
			for (var i=0;i<$("#group_box li").length;i++) {
				var mobile=$("#group_box li").eq(i).attr("data-mobile");
				arr.push(mobile);
			}
			
			
			if(group==""){
				return false;		
			}else if(name==""){
				layer.tips('请输入姓名', '.student_name', {
				    tips: [1, '#408AFF'],
				    time: 2000
				});
				return false;
			}else if(name.length<2){
				layer.tips('姓名不能少于2个字符', '.student_name', {
				    tips: [1, '#408AFF'],
				    time: 2000
				});
				return false;
			}else if(tel==""){
				layer.tips('请输入手机号码', '.student_tel', {
				    tips: [1, '#408AFF'],
				    time: 2000
				});
				return false;
			}else if(!reg.test(tel)){
				layer.tips('请输入正确的手机号码', '.student_tel', {
				    tips: [1, '#408AFF'],
				    time: 2000
				});
				return false;
			}else if($.inArray(tel, arr)!="-1"){
				layer.tips('此用户已存在!', '.student_tel', {
				    tips: [1, '#408AFF'],
				    time: 2000
				});
				return false;
			}else{
				$.ajax("{{url('/msc/admin/training/check-mobile-exist')}}",{
					type: 'post',
		            data: {mobile:tel},
		            success:function(data) {
		            	if(data!="undefined"){
		            		$("#group"+group).append('<li data-name="'+name+'"  data-mobile="'+tel+'">'+name+'('+tel+')</li>');
							$("#student_number").text(num+1);
		            	}else{
		            		layer.tips('此用户未注册!', '.student_tel', {
							    tips: [1, '#408AFF'],
							    time: 2000
							});
		            	}
		            },
		            error:function(){
		              	$.alert({
		                  	title: '提示：',
		                  	content: '通讯失败!',
		                  	confirmButton: '确定',
		                  	confirm: function(){
		                  	}
		              	});
		            },
		            dataType: "json"
                });
			}
		})
		
		$("#group_box").delegate(".sortable-list li","mouseenter",function(){
			$(this).append('<i class="fa fa-remove del_student"></i>');
			$(".del_student").fadeIn(200);
			$(".del_student").click(function(){
				$(this).parent().remove();
				var num=parseInt($("#student_number").text());
				$("#student_number").text(num-1);
			})
		});
		$("#group_box").delegate(".sortable-list li","mouseleave",function(){
			$(".del_student").fadeOut(200);
			$(this).children("i").remove();
		})
	});
</script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>培训安排</h5>
		</div>
		
		<div class="ibox-content">
			<div class="row marb_25 marl_5 mart_5">
				<span class="col-sm-2 btn-ccc marr_15 txta_l">1.第一步</span>
				<span class="col-sm-2 btn-blue marr_15 txta_l">2.第二步</span>
				<span class="col-xs-2 btn-ccc txta_l">3.第三步</span>
			</div>
			<form class="form-horizontal" action="/msc/admin/training/add-training-group" method="post" id="group_form">
				<input type="hidden" name="training_id" id="training_id" value="{{$training->id}}" />
				<input type="hidden" name="training_group_num" id="training_group_num" value=""/>
				<div id="group_hidden">
					
				</div>
				<div class="form-group">
		            <label class="col-sm-1 control-label font12">培训名称</label>
		            <div class="col-sm-11">
		            	<div class="col-sm-12 padt_7">{{$training->name}}</div>
		            </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训时间</label>
		            <div class="col-sm-11 padt_7">
                        <div class="col-sm-6 ">{{$training->begindate}}&nbsp;至&nbsp;{{$training->enddate}}</div>
                    </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训人员</label>
		            <div class="col-sm-11" style="position: relative;">
	                        <input type="button" name="" id="" value="导入培训人员及分组情况" class="btn btn-default" />
		                    <span id="file_span">
		                        <input type="file" name="training" id="group_file" value=""/>
	                        </span>
                    </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训人数</label>
		            <div class="col-sm-11 padt_7">
                        <div class="col-sm-2" id="student_number">0</div>
                    </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">分组预览</label>
		            <div class="col-sm-11" id="group_box">
		            	<div class="col-sm-12" id="add_student" style="display:none;">
			            	<div class="form-group  col-sm-2">
					            <div class="col-sm-12" style="padding-left:0;">
					            	<input type="text" placeholder="请输入姓名" class="form-control padt_7 ccol-sm-12 student_name" name="name" value=""/>
					            </div>
					        </div>
					        <div class="form-group col-sm-2">
					            <div class="col-sm-12">
					            	<input type="text" placeholder="请输入手机号码"  class="form-control padt_7 ccol-sm-12 student_tel" name="tel" value=""/>
					            </div>
					        </div>
			            	<div class="form-group col-sm-4">
			            		<label class="control-label font12 student_left">选择分组：</label>
				            	<div class="col-sm-6">
					            	<select class="form-control" id="group_selected">
					            		
					            	</select>
					            </div>
					            <input type="button" class="btn btn-primary" id="add_btn" value="增加" />
					        </div>
				        </div>
                    </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <input type="button" name="" id="return" class="btn btn-default marr_15" value="上一步" />
		        <input type="button" name="" id="group_submit" disabled="disabled" class="btn btn-primary" value="下一步，导入课程内容" />
			</form>
		</div>
	</div>
</div>
@stop{{-- 内容主体区域 --}}