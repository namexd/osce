@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        /*显示区域外边框*/
        .cBorder{border: 1px solid #e7eaec;}
        /*得分*/
        .showScore{padding: 20px 0 30px;color: #ff0101;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/theoryTest/theory_validate.js')}}"> </script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'theory_complete','goUrl':'{{ route('osce.admin.ApiController.LoginAuthView') }}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">理论考试</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$data['name']}}</h2>
                        <span>考试时间：</span>
                        <span class="checkTime">{{$data['length']}}</span>
                        <span style="margin-left: 1em;">总分：</span>
                        <span class="score">{{$data['total_score']}}分</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content text-center p-md">
                        <div class="p-md cBorder mart_10">
                            <p class="font18" style="padding-top: 55px;">考试得分</p>
                            <div class="showScore">
                                <span class="" style="font-size: 40px;">{{$data['totalScore']}}</span>
                                <span class="font16">分</span>
                            </div>
                            <div class="timeBox font16">
                                <span>考试用时：{{$data['actual_length']}}分钟</span>
                            </div>
                            <div class="" style="padding: 70px 0 55px;">
                                <button class="btn btn-primary" id="sure">确认</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')

@stop