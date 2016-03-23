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
        {{--学生信息展示--}}
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>2016年第一期OSCE考试理论考试</h2>
                        <span>考试时间：</span>
                        <span class="checkTime">20分钟</span>
                        <span class="marl_10">总分：</span>
                        <span class="score">100分</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-left">
                        <div class="font20 padt_20 goCenter">当前考生</div>
                        <div class="p-md cBorder mart_10 overflow goCenter">
                            <div class="col-sm-6">
                                <div style="padding: 40px 60px;">
                                    <img src="" alt="" class="imgBorder myImg" style="height: 250px;width: 196px;">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div style="padding: 40px 60px;">
                                    <div class="nameBox font20 marb_25">
                                        <span>考生姓名：</span>
                                        <span class="stuName">江缤</span>
                                    </div>
                                    <div class="stuBox font20 marb_25">
                                        <span>考生学号：</span>
                                        <span class="stuNum">201013031401</span>
                                    </div>
                                    <div class="idBox font20 marb_25">
                                        <span>身份证号：</span>
                                        <span class="idNum">201013031401</span>
                                    </div>
                                    <div class="admissionBox font20 marb_25">
                                        <span>准考证号：</span>
                                        <span class="admissionNum">201013031401</span>
                                    </div>
                                    <div class="btnBox">
                                        <button class="btn btn-primary btn-lg font16" style="padding: 9px 40px;">进入考试</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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