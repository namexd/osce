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
		<form method="get" action="{{route('msc.admin.lab.getOpenLabHistoryExcl')}}">
			<div class="btn-group marr_25">
				<button type="button" class="btn btn-white">图表类型</button>
				<div class="btn-group" role="group">
					<select class="form-control cur chart" id="chart-type">
						<option value="bar">柱形图</option>
						<option value="pie">饼状图</option>
					</select>
				</div>
	        </div>
	        <input placeholder="日期" class="form-control layer-date laydate-icon marr_5 mart2_7 date" id="start" name="date">
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
					<select class="form-control" id="status" name="result_init">
                        <option value="0">良好</option>
                        <option value="1">损坏</option>
                        <option value="2">严重损坏</option>
                    </select>
				</div>
	        </div>
	        <a href="javascript:void(0)" class="btn btn-primary marr_15 inquiry">查询</a>
	        <input type="submit" class="btn btn-w-m btn-white marl_10" value="导出Excel文件">
	        </form>
	        <div id="main" style="height:400px"></div>
	        
		</div>
	</div>
<script src="{{asset('msc/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
@stop{{-- 内容主体区域 --}}
