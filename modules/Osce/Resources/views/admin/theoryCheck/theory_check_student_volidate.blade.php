@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        body{background-color: #fff!important;}
        /*显示区域外边框*/
        .cBorder{border: 5px solid #e7eaec;}
        /*头像区别边框*/
        .imgBorder{border: 1px solid #e7eaec;}
        /*动态居中*/
        .goCenter{width: 1000px;margin: 0 auto;}

    </style>
@stop

@section('only_js')
    {{--<script src="{{asset('osce/admin/theoryTest/theory_validate.js')}}"> </script>--}}
@stop

@section('content')
            <input type="hidden" id="parameter" value="{'pagename':'theory_validate','paperUrl':'{{ route('osce.admin.ApiController.getExamPaperId') }}','examUrl':'{{ route('osce.admin.AnswerController.formalPaperList') }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">理论考试</h5>
            </div>
        </div>
        {{--学生信息展示--}}
        <div class="row showImf">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{@$data['name']}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-left" style="border-top: none;">
                        <div class="font20 padt_20 goCenter">当前考生</div>
                        <div class="p-md cBorder mart_10 overflow goCenter">
                            <div class="col-sm-6">
                                <div style="padding: 40px 60px;">
                                    <img src="" alt="" class="imgBorder myImg" style="height: 250px;width: 196px;">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div style="padding: 40px 60px;">
                                    <div class="nameBox font20 marb_40">
                                        <span>考生姓名：</span>
                                        <span class="stuName"></span>
                                    </div>
                                    <div class="stuBox font20 marb_40">
                                        <span>考生学号：</span>
                                        <span class="stuNum"></span>
                                    </div>
                                    <div class="idBox font20 marb_40">
                                        <span>身份证号：</span>
                                        <span class="idNum"></span>
                                    </div>
                                    <div class="admissionBox font20 marb_40">
                                        <span>准考证号：</span>
                                        <span class="admissionNum"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content mart_20">
                        <div class="font20">待考考试</div>
                        <div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')

@stop