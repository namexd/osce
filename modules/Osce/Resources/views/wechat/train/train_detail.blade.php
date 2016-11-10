@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/train.css')}}" type="text/css" />
@stop
@section('only_head_js')
@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.getTrainlists')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	查看
       	<a class="right header_btn nou clof header_a" href="#"></a>
    </div>

    <div class="add_main">
        <div class="form-group">
            <label for="">培训名称</label>
            <div class="txt">{{ $data['name']  }}</div>
        </div>
        <div class="form-group">
            <label for="">培训地点</label>
            <div class="txt">{{ $data['address']  }}</div>
        </div>
        <div class="form-group">
            <label for="">开始时间</label>
            <div class="txt">{{ $data['begin_dt']  }}</div>
        </div>
        <div class="form-group">
            <label for="">结束时间</label>
            <div class="txt">{{ $data['end_dt']  }}</div>
        </div>
        <div class="form-group">
            <label for="">培训讲师</label>
            <div class="txt">{{ $data['teacher']  }}</div>
        </div>
        <div class="form-group">
            <label for="">描述</label>
            <div class="txt">{!! $data['content'] !!}</div>
        </div>
    </div>
    <div class="add_main">
		<div  class="form-group">
        	<label for="">附件</label>
            <div class="txt">
                @if($data['attachments'])
                @foreach($data['attachments'] as $key=>$list)
                <a href="{{ route('osce.admin.getDownloadDocument',['id'=>$data['id'],'attch_index'=>$key])}}"><?php $pathInfo=explode('/',$list)?>{{array_pop($pathInfo)}}</a><br />
            	@endforeach
                @endif
        	</div>
        </div>
    </div>
@stop