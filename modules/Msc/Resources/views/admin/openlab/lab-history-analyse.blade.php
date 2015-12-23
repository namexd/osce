@extends('msc::admin.layouts.admin')

@section('only_css')
	<link rel="stylesheet" href="{{asset('msc/admin/trainarrange/trainarrange.css')}}">
@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
	<script src="{{asset('msc/admin/openlab/openlab.js')}}"></script>
@stop

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
<input type="hidden"  id="parameter" value="{'pagename':'lab_history_analyse'}" >
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5>
				统计分析
			</h5>
		</div>
		
		<div class="ibox-content form-horizontal">
			<div class="btn-group marr_25">
				<button type="button" class="btn btn-white">图表类型</button>
				<div class="btn-group" role="group">
					<select class="form-control cur chart" id="chart-type">
						<option value="line">折线图</option>
						<option value="bar">柱形图</option>
						<option value="pie">饼状图</option>
					</select>
				</div>
	        </div>
	        <input placeholder="日期" class="form-control layer-date laydate-icon marr_5 mart2_7 date" id="start" name="begindate">
	        <div class="btn-group marr_5" style="display:none">
				<button type="button" class="btn btn-white">年级</button>
				<div class="btn-group" role="group">
					<select class="form-control cur grade">
						<option value="0">全部</option>
						<option value="1">一年级</option>
						<option value="2">二年级</option>
						<option value="3">三年级</option>
					</select>
				</div>
	        </div>
	        <div class="btn-group marr_5"  style="display:none">
				<button type="button" class="btn btn-white specialty">专业</button>
				<div class="btn-group" role="group">
					<select class="form-control cur profession">
						<option value="0">全部</option>
						<option value="1">计算机</option>
						<option value="2">设计</option>
						<option value="3">摄影</option>
					</select>
				</div>
	        </div>
	        <div class="btn-group marr_5">
				<button type="button" class="btn btn-white">复位状态</button>
				<div class="btn-group" role="group">
					<select class="form-control" id="status">
                        <option value="0">良好</option>
                        <option value="1">损坏</option>
                        <option value="2">严重损坏</option>
                    </select>
				</div>
	        </div>
	        <button class="btn btn-primary marr_15 inquiry">查询</button>
	        <a href="javascript:void(0)" class="btn btn-w-m btn-white marl_10">导出Excel文件</a>
	        
	        <div id="main" style="height:400px"></div>
	        
		</div>
	</div>
<script src="{{asset('msc/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<!--script type="text/javascript">
    	var start = {
		    elem: "#start",
		    format: "YYYY/MM/DD",
		    min: '1970-01-01',
		    max: "2099-06-16",
		    istime: false,
		    istoday: false,
		    choose: function (a) {
		        /*end.min = a;
		        end.start = a*/
		    }
		};
		
		$(function(){
			laydate(start);
			
			var num=$(".chart").val();//图表下标
			var date=$('.date').val();
			var grade=$(".grade").val();
			var profession=$(".profession").val();
			var result_init=$(".result_init").val();
			
			$(".chart").change(function(){
				num=$(this).val();
				$.ajax({
					type:"get",
					url:"/msc/admin/lab/openlab-history-analyze",
					async:true,
					data:{
		            	date : date, //日期
		            	grade : grade, //年级
		            	profession : profession ,//专业 
		            	result_init : result_init//复位状态
		            },
					success:function(data){
						for(var i in data){
							chart(data,document.getElementById('main'),num);
						}
					}
				});
			})
			$(".grade").change(function(){
				grade=$(this).val();
			})
			$(".profession").change(function(){
				profession=$(this).val();
			})
			$(".result_init").change(function(){
				result_init=$(this).val();
			})
			
			$.ajax({
				type:"get",
				url:"/msc/admin/lab/openlab-history-analyze",
				async:true,
				data:{
	            	date : date, //日期
	            	grade : grade, //年级
	            	profession : profession ,//专业 
	            	result_init : result_init//复位状态
	            },
				success:function(data){
					for(var i in data){
						chart(data,document.getElementById('main'),num);
					}
				}
			});
			$(".inquiry").click(function(){
				date=$(".date").val().substring(0,10);
				//console.log(date);
				$.ajax({
					type:"get",
					url:"/msc/admin/lab/openlab-history-analyze",
					async:true,
					data:{
		            	date : date, //日期
		            	grade : grade, //年级
		            	profession : profession ,//专业 
		            	result_init : result_init//复位状态
		            },
					success:function(data){
						for(var i in data){
							chart(data,document.getElementById('main'),num);
						}
					}
				});
				
			})    	  	
		})
		
		function chart(req,name,num){
			var a;
			var b;
			for(var i in req){
        		a='"'+i+'"';
        		b=''+req[i]+''
        	}
			var type="line";
			if(num==1){
				type="line";
			}else if(num==2){
				type="bar";
			}
			var myChart = echarts.init(name);
			var option = {
	       		tooltip : {
			         trigger: 'axis'
			    },
	            xAxis : [
	                {
	                    type : 'category',
	                    data : [a,]/*["实验室1","实验室2","实验室3","实验室4","实验室5","实验室6"]*/
	                }
	            ],
	            yAxis : [
	                {
	                    type : 'value'
	                }
	            ],
	            series : [
	                {
	                    "name":"使用次数",
	                    "type":type,
	                    "data":[b,]/*[5, 20, 40, 10, 10, 20]*/
	                }
	            ]
	        };
	        var option2 = {
			    tooltip : {
			        trigger: 'item',
			    },
			    series : [
			        {
			            type:'pie',
			            data:[
			                /*{value:5, name:'实验室1'},
			                {value:20, name:'实验室2'},
			                {value:40, name:'实验室3'},
			                {value:10, name:'实验室4'},
			                {value:10, name:'实验室5'},
			                {value:20, name:'实验室6'}*/
			                {value:b,name:a}
			            ]
			        }
			    ]
			};
	        if(num==3){
	        	myChart.setOption(option2);
	        }else{
	        	myChart.setOption(option);
	        }
	        
		}

    </script-->
@stop{{-- 内容主体区域 --}}
