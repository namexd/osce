@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        /*显示区域外边框*/
        .cBorder{border: 5px solid #e7eaec;}
        /*头像区别边框*/
        .imgBorder{border: 1px solid #e7eaec;}
        /*动态居中*/
        .goCenter{width: 1000px;margin: 0 auto;}
        body{background-color: #fff!important;}
    </style>
@stop

@section('only_js')

@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'theory_check'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">理论考试</h5>
            </div>
        </div>
        {{--等待学生信息--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$data['name']}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center">
                        <div class="font20" style="padding: 30% 20px;">等待学生</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')

@stop