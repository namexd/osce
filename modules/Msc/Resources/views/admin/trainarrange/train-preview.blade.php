@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
	<style type="text/css">
		#editable td input{width:90%;margin-left:5%;}
		.look_group:hover{color:#000;}
	</style>
@stop

@section('only_js')

<script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/dataTables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/dataTables/dataTables.bootstrap.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('msc/admin/trainarrange/trainarrange.js')}}"></script>
    <script type="text/javascript">
    	var start = {
		    elem: "#start",
		    format: "YYYY/MM/DD hh:mm:ss",
		    min: laydate.now(),
		    max: "2099-06-16 23:59:59",
		    istime: true,
		    istoday: false,
		    choose: function (a) {
		        end.min = a;
		        end.start = a
		    }
		};
		var end = {
		    elem: "#end",
		    format: "YYYY/MM/DD hh:mm:ss",
		    min: laydate.now(),
		    max: "2099-06-16 23:59:59",
		    istime: true,
		    istoday: false,
		    choose: function (a) {
		        start.max = a
		    }
		};
		$(function(){
			$("#editable").dataTable();
    		$("#editable_length").parents(".row").hide();
    		$("#editable_paginate").parents(".row").hide();
			laydate(start);
			laydate(end);
			$(".edit").on('click',function(){
				if($(this).text()=="编辑"){
					$(this).text("完成").removeClass("btn-default").addClass("btn-primary");
					$(".tj").attr("data-toggle","");
					$(".train_name,.train_num").removeAttr("readonly").css("border","1px solid #ccc");
					$(".train_name").focus().val($(".train_name").val());
					$(".tj").attr("disabled","disabled");
					$(".time_no").hide();
					$(".time_ok").show();
				}else{
					$(this).text("编辑").removeClass("btn-danger").addClass("btn-default");
					$(".tj").attr("data-toggle","modal");
					$(".tj").removeAttr("disabled");
					$(this).blur();
					$(".train_name,.train_num").attr("readonly","readonly").css("border","0");
					var begin=$("#start").val();
					var end=$("#end").val();
					$("#begindate").text(begin);
					$("#enddate").text(end);
					$(".time_no").show();
					$(".time_ok").hide();
					
				}
			});
			$("#editable td").bind("click",function(){
				if ($(this).children("input").length>0) { 
					return false; 
				}
				var val=$.trim($(this).text());
				$(this).html('<input type="text" class="" value="'+val+'" />');
				$(this).children("input").focus().val(val);
				$("#editable td input").bind("blur",function(){
					var txt=$(this).val();
					if(txt==""){
						alert("内容不能为空");
					}
					$(this).parent("td").html(txt);
				})
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
			
			$("#submit").click(function(){
				
			})
		});
    </script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<form class="form-horizontal" action="url(/msc/admin/training/add-training-preview)" method="post" id="form">
			<div class="ibox-title of">
				<h5 class="col-sm-6">培训安排</h5>
				<div class="right_btn">
					<button class="btn btn-default marr_5" id="return">取消</button>
					<button class="btn btn-default marr_5">打印</button>
					<button class="btn btn-default marr_5"><a class="nou clo0 look_group" href="{{ route('msc.training.editTrainingGroup', ['id'=>$training->id])}}">查看分组学员</a></button>
					<button class="btn btn-default marr_5 edit">编辑</button>
					<button class="btn btn-primary tj" id="submit" data-toggle="modal" data-target="#myModal" flag="yes">提交</button>
				</div>
			</div>
			<div class="ibox-content bor0">
				<input type="hidden" name="id" id="id" value="$training['id']}}" />
				<div class="form-group">
		            <label class="col-sm-1 control-label font12">培训名称</label>
		            <div class="col-sm-11">
		            	<input type="text" name="name" class="form-control col-sm-12 padt_7 bgw train_name" value="$training['name']}}" readonly="readonly" />
		            </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训人数</label>
                    <div class="col-sm-11">
		            	<input type="text" name="num" class="form-control col-sm-12 padt_7 bgw train_num" value="$training['total']}}" readonly="readonly" />
		            </div>
		        </div>
		         <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训时间</label>
		            <div class="col-sm-11 padt_7 time_no">
                        <div class="col-sm-6 "><span id="begindate">$training['begindate']}}</span>&nbsp;至&nbsp;<span id="enddate">$training['enddate']}}</span></div>
                    </div>
                    <div class="col-sm-11 time_ok">
                        <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start" name="begindate">
                        <span class="arrange-go">至</span>
                        <input placeholder="结束日期" class="form-control layer-date laydate-icon" id="end" name="enddate">
                    </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训具体安排</label>
		            <div class="col-sm-11">
		            	<table class="table table-striped table-bordered table-hover" id="editable">
	                     	<thead>
	                     		<tr>
	                                <th class="center">分组</th>
	                                <th class="center">培训课程</th>
	                                <th class="center">技能中心负责老师</th>
	                                <th class="center">培训地点</th>
	                                <th class="center">上课老师</th>
	                                <th class="center">培训时间</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        	@foreach($planinfo as $planinfo)
		                        	<tr>
		                        		<input type="hidden" name="trainingCourses[]" id="" value="group:{{$planinfo['group']}},courseId:{{$planinfo['course_code']}},addressId:{{$planinfo['address_code']}},begintime:{{$planinfo['begin_dt']}},endtime:{{$planinfo['end_dt']}}" />
	                                	<td class="center">
	                                		{{$planinfo['group']}}
	                                	</td>
	                                    <td class="center">
	                                    	{{$planinfo['course_code']}}
	                                    </td>
	                                    <td class="center">
	                                    	{{$planinfo['manager_name']}}
	                                    </td>
	                                    <td class="center">
	                                    	{{$planinfo['address_code']}}
	                                    </td>
	                                    <td class="center">
	                                    	{{$planinfo['teacher']}}
	                                    </td>
	                                    <td class="center">
	                                    	{{$planinfo['time']}}
	                                    </td>
	                                </tr>
	                            @endforeach
	                        </tbody>
                         </table>
                    </div>
		        </div>
			</div>
		</form>
	</div>
@stop

@section('layer_content')
	<!-- 通过 -->
    <form class="form-horizontal" id="Form3" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">提交</h4>
        </div>
        <div class="modal-body">
	        <div class="emergency-1" style="display:none;">确认提交岗前安排？</div>
	        <div class="emergency-2" style="display: block;">
	            <p>岗前培训信息与以下基础课程安排发生冲突</p>
	            <p class="edit state2">课程一： <span id="meet-info" class="colr">临床技能中心7F 2015/09/18 08:00-15:00</span></p>
	            <p>请执行以下课程变更。</p>
	            <div class="form-group">
	                <label class="col-sm-2 control-label">调整方式</label>
	                <div class="col-sm-10">
	                    <select class="form-control" id="recommend-edit">
	                        <option value="1">推荐新的教室与时间</option>
	                        <option value="2">修改有冲突的基础课程内容</option>
	                    </select>
	                </div>
	            </div>
	            <div class="hr-line-dashed"></div>
	            <div class="change-select">
	                  <div class="change-edit">
	                      <div class="form-group">
	                          <label class="col-sm-2 control-label">现时安排</label>
	                          <div class="col-sm-10 padt_7"><p>开放性伤口包扎课程：临床技能中心7F 2015/09/18 08:00-15:00</p></div>
	                      </div>
	                      <div class="form-group">
	                          <div class="col-sm-2 control-label"><label>变更安排</label></div>
	                          <div class="col-sm-10 padt_7"><p>开放性伤口包扎课程</p></div>
	                      </div>
	                      <div class="form-group">
	                          <div class="col-sm-2 control-label"><label>&nbsp;</label></div>
	                          <div class="col-sm-10">
	                            <select class="form-control" id="classroom-chioce">
	                                <option value="1">临床技能中心7F</option>
	                                <option value="2">临床技能中心8F</option>
	                            </select>
	                          </div>
	                      </div>
	                      <div class="form-group">
	                          <div class="col-sm-2 control-label"><label>&nbsp;</label></div>
	                          <div class="col-sm-10">
	                            <select class="form-control" id="classroom-time">
	                                <option value="1">时间</option>
	                                <option value="2">世间2</option>
	                            </select>
	                          </div>
	                      </div>
	                  </div>
	            </div>
	        </div>
        </div>
        <div class="modal-footer bor0 align_left">
            <span class="btn btn-primary ok" data-toggle="modal" data-target="#myModal" flag="yes">确定</span>
        </div>
    </form>
@stop{{-- 内容主体区域 --}}