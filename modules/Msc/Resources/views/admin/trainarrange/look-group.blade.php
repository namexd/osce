@extends('msc::admin.layouts.admin')

@section('only_css')
<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
@stop

@section('only_js')
<script>
	$(document).ready(function() {
		function action(){
			$(".sortable-list").sortable({
				connectWith: ".connectList"
			}).disableSelection();
		}
		$.ajax('/msc/admin/training/edit-training-group-data',{
            type: 'get',
            success:function(data) {
            	console.log(data);
            	
            	//获取组的数组
            	var arr=[];
            	for (var b=0;b<data.groupinfo.length;b++) {
            		var group=data.groupinfo[b].group;
            		if($.inArray(group, arr)=="-1"){
            			arr.push(group);
            		}
            	}
            	for (var a=0;a<arr.length;a++){
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
            	}
            	for (var i=0;i<data.groupinfo.length;i++) {
            		var group=data.groupinfo[i].group;
					var index=arr.indexOf(group);
					$("#group"+index).append('<li data-name="'+data.groupinfo[i].name+'" data-mobile="'+data.groupinfo[i].mobile+'">'+data.groupinfo[i].name+'('+data.groupinfo[i].mobile+')</li>');
            	}
            	action();
            },
            error:function() {
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
        
        //新增学员
        $("#add_btn").click(function(){
			var name=$(".student_name").val();
			var tel=$(".student_tel").val();
			var group=$("#group_selected").val();
			if(group==""){
								
			}else{
				$("#group"+group).append('<li data-name="'+name+'"  data-mobile="'+tel+'">'+name+'('+tel+')</li>');
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
		
		//提交
		$("#group_submit").click(function(){
			for ( var i=0;i<$(".sortable-list").length;i++) {
				var group=$(".sortable-list").eq(i).attr("data-group");
				for (var j=0;j<$(".sortable-list").eq(i).children("li").length;j++) {
					var name=$(".sortable-list").eq(i).children("li").eq(j).attr("data-name");
					var mobile=$(".sortable-list").eq(i).children("li").eq(j).attr("data-mobile");
					obj='group:'+group+',name:'+name+',mobile:'+mobile;
					str='<input type="hidden" name="list[]" id="" value="'+obj+'" />';
					console.log(str);
					$("#group_hidden").append(str);
				}
			}
			$("#group_form").submit();
		})
		$("#return").click(function(){
			//询问框
			layer.confirm('您确定要返回上一步？这可能会造成数据丢失！', {
			    btn: ['确定','取消'] //按钮
			}, function(){
			    history.go(-1);
			}, function(){
				
			});
		})
	});
</script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title of">
			<h5>2015岗前培训</h5>
			<div class="right_btn">
				<button class="btn btn-default marr_5">打印</button>
				<button class="btn btn-default marr_5" id="return">取消</button>
				<button class="btn btn-primary" id="group_submit">确定</button>
			</div>
		</div>
		<div class="ibox-content bor0">
			<form class="form-horizontal" action="{{url('/msc/admin/training/edit-training-group')}}" method="post" id="group_form">
				<input type="hidden" name="id" value="{{$id}}" />
				<div class="form-group">
		            <label class="col-sm-1 control-label font12">学员姓名</label>
		            <div class="col-sm-11">
		            	<input type="text" class="form-control padt_7 ccol-sm-12 student_name" value="" name="" />
		            </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		            <label class="col-sm-1 control-label font12">手机号码</label>
		            <div class="col-sm-11">
		            	<input type="text" class="form-control padt_7 ccol-sm-12 student_tel" value="" name="" />
		            </div>
		        </div>
		        <div class="hr-line-dashed"></div>
				<div class="form-group">
		            <label class="col-sm-1 control-label font12">选择分组</label>
		            <div class="col-sm-2">
		            	<select class="form-control" id="group_selected"></select>
		            </div>
		            <input type="button" class="btn btn-primary" id="add_btn" value="增加" />
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div id="group_hidden">
				</div>
		        <div class="form-group">
		        	 <label class="col-sm-1 control-label font12">分组列表</label>
		            <div class="col-sm-11" id="group_box">
                    </div>
		        </div>
			</form>
		</div>
	</div>
</div>
@stop{{-- 内容主体区域 --}}