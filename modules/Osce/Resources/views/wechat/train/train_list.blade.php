@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/train.css')}}" type="text/css" />
@stop
@section('only_head_js')
<script src="{{asset('osce/wechat/train/train.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'train_list','URL':'{{route('osce.wechat.getTrainList')}}','href':'{{route('osce.wechat.getTrainDetail')}}'}" />
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	考前培训
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <ul id="discussion_ul">
    </ul>
@stop