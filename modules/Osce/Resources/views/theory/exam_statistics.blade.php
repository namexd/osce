@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <link href="{{asset('/osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style type="text/css">
        .select2-container--default .select2-selection--single{border:1px solid #e5e6e7;height:34px;line-height:34px;}
        .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:34px;}
    	.form-horizontal .control-label.text-left { text-align: left;}
    </style>
@stop


@section('only_js')
	<script src="{{ asset('osce/theory/js/echarts-all.js') }}"></script>
	<script>
		$(function () {
			var json = {!! json_encode($data['score_list']) !!}
			console.log(json)
			
			var aName = [];
			var aValue = [];
			
			for (var name in json) {
				aName.push(name);
				aValue.push(json[name]);
			}
			
			var option = {
			    title : {
			        text: '考生总成绩统计图',
			        subtext: '',
			        x:'center'
			    },
			    tooltip : {
			        trigger: 'item',
			        formatter:'{a}<br/>{b} : {c}分'
			    },
			    legend: {
			    	show:false,
			        data:['学生成绩']
			    },
			    toolbox: {
			        show : true,
			        feature : {
			            mark : {show: false},
			            dataView : {show: false, readOnly: false},
			            magicType : {show: true, type: ['line', 'bar']},
			            restore : {show: true},
			            saveAsImage : {show: true}
			        }
			    },
			    calculable : false,
			    xAxis : [
			        {
			        	
			        	splitLine:{show:false},
			        	splitLine:{show:false},
			            type : 'category',
			            data : aName
			        }
			    ],
			    yAxis : [
			        {
			        	splitLine:{show:false},
			            type : 'value'
			        }
			    ],
			    series : [
			        {
			            name:'考试总得分',
			            type:'bar',
			            data:aValue,
			            barMaxWidth:'50',
			            tooltip:{
			            	show:true
			            },
						itemStyle: {
						    normal: {
						    	color:'#2ec7c9',
						    	barBorderRadius:[5,5,0,0],
						        label: {
						            show: true,
						            position: 'top',
						            textStyle: {
						                color: '#615a5a'
						            }
						        }
						    }
						},
			            markLine : {
			                data : [
			                    {itemStyle:{normal:{color:'#1dc5a3'}},type:'average',name: '平均分'},
			                    {itemStyle:{normal:{color:'red'}},type:'max',name:'最高分'},
			                    {itemStyle:{normal:{color:'#000'}},type:'min',name:'最低分'}
			                ]
			            }
			        }
			    ]
			};
			
	        var myChart = echarts.init(document.getElementById('main'));
	        myChart.setOption(option);
			
		});
	</script>
@stop

@section('content')

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>成绩统计</h5>
                <a  href="{{route('osce.theory.studentscore',['id'=>request()->get('id')])}}" class="btn btn-primary" style="float: right; position: relative;top: -10px;">&nbsp;查看详细成绩&nbsp;</a>
            </div>
	
            <div class="ibox-content">
            	<h2 class="text-center p-md">{{$data['exam_name']}}</h2>
                <div class="row">
                    <div class="col-md-12 ">
                        <div class="form-horizontal" id="sourceForm">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">考试总得分：</label>
                                <label class="col-sm-2 control-label text-left">{{$data['total_score']}}分</label>
                                <label class="col-sm-2 control-label">考试平均分：</label>
                                <label class="col-sm-2 control-label text-left">{{$data['avg_score']}}分</label>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">最高分：</label>
                                <label class="col-sm-2 control-label text-left">{{$data['student_score_max']}}分</label>
                                <label class="col-sm-2 control-label">最低分：</label>
                                <label class="col-sm-2 control-label text-left">{{$data['student_score_min']}}分</label>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">合格率：</label>
                                <label class="col-sm-2 control-label text-left">{{$data['pass_percent']}}</label>
                                <label class="col-sm-2 control-label">试题难度：</label>
                                <label class="col-sm-2 control-label text-left">{{$data['hard_level']}}</label>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">未参考学生：</label>
                                <label class="col-sm-6 control-label text-left">{{implode(',',$data['students_absence'])}}</label>
                            </div>
                            <div class="hr-line-dashed"></div>

							<div id="main" style="height:400px;"></div>
							
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-4">
                                    <a class="btn btn-white" href="javascript:history.go(-1);">返回</a>
                                    {{--<button class="btn btn-white" type="submit">取消</button>--}}
                                </div>
                            </div>


                        </div>

                    </div>

                </div>
            	
            </div>
        </div>

    </div>

@stop{{-- 内容主体区域 --}}
@section('footer_js')
    @parent
    <script src="{{asset('/osce/common/select2-4.0.0/js/select2.full.min.js')}}"></script>
    <script src="{{asset('/osce/common/select2-4.0.0/js/i18n/zh-CN.js')}}"></script>
@stop