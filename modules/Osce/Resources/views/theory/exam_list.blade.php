@extends('osce::theory.base')

@section('title')
	考试管理
@stop
@section('head_css')
   <style>
   		table tbody tr td:last-child { width: 230px;}
   		#addlb { display: none;}
   		.layui-layer-page .layui-layer-content {overflow: hidden;}
   		.laydate-icon { padding-left: 5px;}
   </style>

@stop

@section('head_js')
	<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
   <script>
		function deletelist(id) {
			uselayer(2,'确定要删除该考试吗？',function () {
				$('.form-horizontal input').val(id);
				$('.form-horizontal').submit();
			});
		};
		var addlbindex;
		
		$(function () {
			
			uselaydate('start_time','time');
			uselaydate('end_time','time');
			
    		$('.modi-time').click(function () {  
    			$('#exam_name').val($(this).attr('_name')+'的理论考试');
    			$('#start_time').val($(this).attr('_start'));
    			$('#end_time').val($(this).attr('_end'));
    			$('#exam_id').val($(this).attr('_id'))
				addlbindex = layer.open({
					type:1,
					title:'修改考试时间',
					closeBtn:1,
					area:['600px','380px'],
					content:$('#addlb')
				});    			
    		});
    		
    		$('.addlb-close').click(function () {
    			layer.close(addlbindex);
    		});
    		
    		$('.addlb-save').click(function () {
    			if ($('#start_time').val()=='') {
    				uselayer(3,'开始时间不能为空！');
    				return false;
    			}
    			if ($('#end_time').val()=='') {
    				uselayer(3,'结束时间不能为空！');
    				return false;
    			}
    			if ($('#end_time').val()<$('#start_time').val()) {
    				uselayer(3,'结束时间不能小于开始时间！');
    				return false;
    			}
    			$.ajax({
    				type:"post",
    				url:"{{route('osce.cexam.postEditExam')}}",
    				data:{
    					id:$('#exam_id').val(),
    					start:$('#start_time').val(),
    					end:$('#end_time').val(),
    				},
    				success:function (res) {
						if (res.code==1) {
							uselayer2(31,res.message,toReload);
						} else {
							uselayer2(3,res.message);
						}
    				}
    			});
    			
    			
    		});
		});
		
    function uselaydate(id,max) {
    	if (!document.getElementById(id)) {
    		return false;
    	}
    	var format='YYYY-MM-DD';
    	var istime=false;
    	if (max=='time') {
    		format='YYYY-MM-DD hh:mm:ss';
    		istime=true;
    		max=false;
    	}
        var _laydate ={
        	elem:'#'+id,
            event: 'click',
            format: format,
//          min: laydate.now(),
            min: '1900-01-01 00:00',
            max: max?max:'2099-12-31 23:59',
            istime: istime,
            istoday:false,
            choose: function(datas){
//              end.min = datas;
            }
        };
        laydate.skin('molv');   
  		laydate(_laydate);    	
    	
    };		
   </script>
@stop


@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row table-head-style1 ">
	        <div class="col-xs-6">
	            <h5 class="title-label">考试列表</h5>
	        </div>
	        <div class="col-xs-6" style="float: right;">
	            <a  href="{{route('osce.theory.add')}}" class="btn btn-primary" style="float: right;">&nbsp;新增&nbsp;</a>
	        </div>
	    </div>
   		<div class="container-fluid ibox-content" id="list_form">
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>考试名称</th>
                        <th>试卷</th>
                        <th>折合率</th>
                        <th>监考老师</th>
                        <th>开始时间</th>
                        <th>结束时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
            		@foreach($data->items() as $k=>$val)
                    <tr>
                        <td >{{$k+1}}</td>
                        <td >{{$val->exam_id==0?$val->name:$val->exam->name}}的理论考试</td>
                        <td >{{$val->test->name}}</td>
                        <td >{{$val->convert*100}}%</td>
                        <td >{{$val->teacherdata->name}}</td>
                        <td >{{$val->start}}</td>  
                        <td >{{$val->end}}</td>                  
                        
                        <td>
                        	<a class="state1 modal-control" href="{{$val->exam_id==0?route('osce.theory.studentList',['test_id'=>$val->id]):route('osce.admin.exam.getExamineeManage',['id'=>$val->exam_id])}}">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i>考生管理</span>
                            </a>
                        	<a class="state1 modi-time" href="javascript:;" _start="{{$val->start}}" _name="{{$val->exam_id==0?$val->name:$val->exam->name}}" _id="{{$val->id}}" _end="{{$val->end}}">
                                <span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i> 编辑</span>
                            </a>
                            <a class="state1 modal-control" href="javascript:;" onclick="deletelist({{$val->id}})">
                                <span class="read  state2 detail"><i class="fa fa-search fa-2x"></i>删除</span>
                            </a>
	                    </td>
                    </tr>
               		@endforeach
                </tbody>
            </table>
            <form method="get" class="form-horizontal" action="{{route('osce.theory.del')}}">
            	<input type="hidden" name="id" value="" />
            </form>
		</div>
		<div class="pull-left">
			共{{$data->total()}}条
		</div>
		<div class="btn-group pull-right">
			{!! $data->appends($_GET)->render() !!}
		</div>
	</div>
	
	
    
<div class="form-horizontal" id="addlb">
    <input type="hidden" id="exam_id" />
    <div class="form-group" style="margin-top: 50px;">
        <label class="col-sm-3 control-label">考试名称：</label>
		<div class="col-sm-7">
			<input type="text" id="exam_name" class="form-control" readonly="readonly" />	
		</div>
    </div> 
    <div class="form-group" style="margin-top: 20px;">
        <label class="col-sm-3 control-label">开始时间：</label>
		<div class="col-sm-7">
			<input type="text" id="start_time" class="laydate-icon" readonly="readonly"  />	
		</div>
    </div> 
    <div class="form-group" style="margin-top: 20px;">
        <label class="col-sm-3 control-label">结束时间：</label>
		<div class="col-sm-7">
			<input type="text" id="end_time" class="laydate-icon" readonly="readonly"  />	
		</div>
    </div> 
    <div class="form-group" style="margin-top: 50px;">
        <div class="col-sm-6 col-sm-offset-4">
            <button class="btn btn-primary addlb-save">保存</button>
            <a class="btn btn-white addlb-close">取消</a>
        </div>
    </div>
</div>
	
	
@stop{{-- 内容主体区域 --}}