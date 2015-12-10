@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}"><br />
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/webuploader/webuploader.min.js')}}"></script>
	<script src="{{asset('msc/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/jeditable/jquery.jeditable.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/dataTables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/dataTables/dataTables.bootstrap.js')}}"></script>
    <script src="{{asset('msc/admin/trainarrange/trainarrange.js')}}"></script>
    
    <script>
		$(function(){
			//上传分组
			$("#file_span").change(function(){
				var str=$("#arrange_file").val().substring($("#arrange_file").val().lastIndexOf(".")+1);
				if(str!="xlsx"){
					layer.alert(
	                  "请上传正确的文件格式？", 
	                  {title:["温馨提示","font-size:16px;color:#408aff"]}
	               );
				}else{
					$.ajaxFileUpload({
			            url:'/msc/admin/training/import-training-plan',
			            secureuri:false,
			            fileElementId:'arrange_file',//必须要是 input file标签 ID
			            dataType: 'json',//
			            success: function (data, status){
			            	if(data['plan'].length>0){
			            		$(".file_box").hide();
			            		$("#arrange_box").show();
			            		var str='<table class="table table-striped table-bordered table-hover" id="editable">'+
			                         	'<thead>'+
			                         		'<tr>'+
			                                    '<th class="center">分组</th>'+
			                                    '<th class="center">培训课程</th>'+
			                                    '<th class="center">技能中心负责老师</th>'+
			                                    '<th class="center">培训地点</th>'+
			                                    '<th class="center">上课老师</th>'+
			                                    '<th class="center">培训时间</th>'+
			                                '</tr>'+
			                            '</thead>'+
			                         	'<tbody>';
			            		for (var i=0;i<data['plan'].length;i++) {
			            			var time=data['plan'][i]['begin_dt']['date'].substring(0,9);
			            			var begintime=data['plan'][i]['begin_dt']['date'].substring(data['plan'][i]['begin_dt']['date'].length-8,data['plan'][i]['begin_dt']['date'].length-3);
			            			var endtime=data['plan'][i]['end_dt']['date'].substring(data['plan'][i]['end_dt']['date'].length-8,data['plan'][i]['end_dt']['date'].length-3)
			            		    time=time+"&nbsp;"+begintime+'~'+endtime;
			            		    str+='<tr>'+
					            			'<td class="center">'+data['plan'][i]['group']+'组</td>'+
		                                    '<td class="center">'+data['plan'][i]['course_code']+'</td>'+
		                                    '<td class="center">'+data['plan'][i]['manager_name']+'</td>'+
		                                    '<td class="center">'+data['plan'][i]['address_code']+'</td>'+
		                                    '<td class="center">'+data['plan'][i]['teacher']+'</td>'+
		                                    '<td class="center">'+time+'</td>'+
		                                '</tr>';
			            		}
			            		str+='</tbody></table>';
			            		$("#arrange_box").append(str);
			            		$("#editable").dataTable();
			            		$("#editable_length").parents(".row").hide();
			            		$("#editable_paginate").parents(".row").hide();
			            		$("#preview").removeAttr("disabled");
			            	}else{
			            		layer.alert(
				                  "数据错误，请重新导入！", 
				                  {title:["温馨提示","font-size:16px;color:#408aff"]}
				               );
			            	}
			            },
			            error: function (data, status, e)
			            {			            	
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
		})
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
				<span class="col-sm-2 marr_15 txta_l btn-ccc">1.第一步</span>
				<span class="col-sm-2 marr_15 txta_l btn-ccc">2.第二步</span>
				<span class="col-xs-2 txta_l btn-blue">3.第三步</span>
			</div>
			<form class="form-horizontal" action="{{ route('msc.training.addTrainingPreview',array('id'=>$training->id)) }}" method="get" id="">
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
		        	<label class="col-sm-1 control-label font12">培训人数</label>
		            <div class="col-sm-11 padt_7">
                        <div class="col-sm-2">{{$training->total}}</div>
                    </div>
		        </div>
		         <div class="hr-line-dashed"></div>
		        <div class="form-group">
		        	<label class="col-sm-1 control-label font12">培训具体安排</label>
		            <div class="col-sm-11" style="position: relative;">
		            	<div class="file_box">
	                        <input type="button" name="" id="" value="导入培训具体安排" class="btn btn-default" />
	                        <span id="file_span">
	                        	<input type="file" name="training" id="arrange_file" value=""/>
	                        </span>
                        </div>
                        <div id="arrange_box" style="display:none;">
                        	
                        </div>
                    </div>
		        </div>
		        <div class="hr-line-dashed"></div>
		        <input type="button" name="" id="return" class="btn btn-default marr_15 col-sm-offset-1" value="上一步" />
		        <input type="submit" name="" id="preview" disabled="disabled" class="btn btn-primary" value="预览" />
			</form>
		</div>
	</div>
</div>

@stop{{-- 内容主体区域 --}}