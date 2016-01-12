@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/personalcenter/css/personalcenter.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('msc/wechat/courseorder/css/course_search.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('msc/wechat/index/css/index.css')}}" rel="stylesheet" type="text/css"/>
<style>
    .add_main .form-group label{width: 95px;}
    .add_main .form-group .txt{padding-left: 100px;}
    .w85{width: 85%;}
    .w15{width: 15%;}
    .manage_list p{padding: 5px 8px 0 8px;}
    .manage_list div p:last-child{padding:0 8px 2px 8px;}
    .manage_list span{color: #9c9c9c; font-size: 12px;  }
    .manage_list{box-shadow: 0 1px 4px #DCE0E4; }
</style>
@stop

@section('only_head_js')

@stop

@section('content')
<div class="user_header">
   预约实验室
</div>
<div class="container container_index">
    <div class="row clearfix manageindex row1">
        <div class="col-sm-6 column">
            <div class="normal_background ">
                <span class="manageindex_icon icon1"></span>
                <a  href="" ><span>普通实验室预约</span></a>
            </div>
        </div>
        <div class="col-sm-6 column">
            <a href="">
                <div class="normal_background">
                    <span class="manageindex_icon icon2"></span>
                    <span>开放实验室预约</span>
                </div>
            </a>
        </div>
    </div>
</div>

@stop