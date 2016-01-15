@extends('msc::wechat.layouts.admin')

@section('only_head_css')
    <link href="{{asset('msc/wechat/index/css/index.css')}}" rel="stylesheet" type="text/css"/>
    <style>

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
                    <a  href="{{ route('msc.Laboratory.LaboratoryTeacherList',['type'=>'ordinary']) }}" ><span>普通实验室预约</span></a>
                </div>
            </div>
            <div class="col-sm-6 column">
                <a href="{{ route('msc.Laboratory.LaboratoryTeacherList',['type'=>'open']) }}">
                    <div class="normal_background">
                        <span class="manageindex_icon icon2"></span>
                        <span>开放实验室预约</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

@stop