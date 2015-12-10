@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('msc/wechat/personalcenter/css/resourceborrow.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('only_head_js')

@stop

@section('content')
<div id="info_list">
    <div class="wait mart_20">
        <img src="{{asset('msc/wechat/common/img/waiting.png')}}" width="30%"/>
        <p>等待审核中</p>
    </div>
</div>

@stop