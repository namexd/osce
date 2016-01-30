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
	@forelse($examresultList as $examResult)
  	<div class="examination_msg">
		<div class="form-group">
            <label for="">评价老师</label>
            <div class="txt">{{$examResult->teacher->name}}</div>
        </div>
        <div class="form-group">
            <label for="">成绩</label>
            <div class="txt">{{$examResult->score}}</div>
        </div>
        <div class="form-group">
            <label for="">提交时间</label>
            <div class="txt">{{$examResult->end_dt}}</div>
        </div>
        <div class="form-group">
            <label for="">评价</label>
            <div class="txt">{{$examResult->evaluate}}</div>
        </div>
  	</div>
	@empty
		@endforelse
  	<div class="detail_box">
	  	<table id="detail_tb">
	  		<tr>
	  			<th>序号</th>
	  			<th>考核内容</th>
	  			<th>满分</th>
	  			<th>得分</th>
	  		</tr>
			@forelse($examScoreList as $examScore)
	  		<tr class="active">
	  			<td>{{$examScore->standard->pid==0? $examScore->standard->sort:$examScore->standard->parent->sort.'-'.$examScore->standard->sort}}</td>
	  			<td>{{$examScore->standard->content}}</td>
	  			<td>{{$examScore->standard->score}}</td>
	  			<td>{{$examScore->score}}</td>
	  		</tr>
				@empty

			@endforelse

	  	</table>
	</div>
@stop