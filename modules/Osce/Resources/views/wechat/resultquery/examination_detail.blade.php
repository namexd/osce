@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
@stop
@section('only_head_js')

@stop

@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	成绩查询
       	<a class="right header_btn nou clof header_a" href="javascript:;">
       	</a>
    </div>
    <div class="form-group">
        <select  id="examination" disabled="disabled" class="form-control normal_select select_indent" name="student_type" required>
            <option value="3">OSCE考试2016年第3期</option>
        </select>
    </div>
  	
  	<div class="examination_msg">
		<div class="form-group">
            <label for="">评价老师</label>
            <div class="txt">张老师</div>
        </div>
        <div class="form-group">
            <label for="">成绩</label>
            <div class="txt">82分</div>
        </div>
        <div class="form-group">
            <label for="">提交时间</label>
            <div class="txt">2016-11-21&nbsp;08:25:00</div>
        </div>
        <div class="form-group">
            <label for="">评价</label>
            <div class="txt">操作很规范，但是细节注重不足，有待提高</div>
        </div>
  	</div>
  	<div class="detail_box">
	  	<table id="detail_tb">
	  		<tr>
	  			<th>序号</th>
	  			<th>考核内容</th>
	  			<th>满分</th>
	  			<th>得分</th>
	  		</tr>
	  		<tr class="active">
	  			<td>1</td>
	  			<td>正确连接呼吸机管道</td>
	  			<td>10</td>
	  			<td>9</td>
	  		</tr>
	  		<tr>
	  			<td>1-1</td>
	  			<td>(1)连接湿化器正确</td>
	  			<td>4</td>
	  			<td>3</td>
	  		</tr>
	  		<tr>
	  			<td>1-2</td>
	  			<td>(2)连接吸气管路正确</td>
	  			<td>3</td>
	  			<td>3</td>
	  		</tr>
	  		<tr>
	  			<td>1-3</td>
	  			<td>(3)连接呼气管路正确</td>
	  			<td>3</td>
	  			<td>3</td>
	  		</tr>
	  		<tr class="active">
	  			<td>2</td>
	  			<td>正确连接呼吸机管道</td>
	  			<td>4</td>
	  			<td>4</td>
	  		</tr>
	  		<tr>
	  			<td>2-1</td>
	  			<td>(1)连接湿化器正确</td>
	  			<td>4</td>
	  			<td>3</td>
	  		</tr>
	  	</table>
	</div>
@stop