@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/train.css')}}" type="text/css" />
@stop
@section('only_head_js')
@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="javascript:history.back(-1)">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	查看
       	<a class="right header_btn nou clof header_a" href="#"></a>
    </div>

    <div class="add_main">
        <div class="form-group">
            <label for="">培训名称</label>
            <div class="txt">{{ $train->name  }}</div>
        </div>
        <div class="form-group">
            <label for="">培训地点</label>
            <div class="txt">{{ $train->address  }}</div>
        </div>
        <div class="form-group">
            <label for="">开始时间</label>
            <div class="txt">{{ $train->begin_dt  }}</div>
        </div>
        <div class="form-group">
            <label for="">结束时间</label>
            <div class="txt">{{ $train->end_dt  }}</div>
        </div>
        <div class="form-group">
            <label for="">培训讲师</label>
            <div class="txt">{{ $train->teacher  }}</div>
        </div>
        <div class="form-group">
            <label for="">描述</label>
            <div class="txt">{{ $train->description }}</div>
        </div>
    </div>
    <div class="add_main">
		<div  class="form-group">
        	<label for="">附件</label>
            <div class="txt">
            	<a href="#">2015年第3季度技能培训学生考前培训附件1</a><br />
            	<a href="#">学生考前培训附件2</a><br />
            	<a href="#">学生考前培训附件3</a>
        	</div>
        </div>
    </div>
@stop