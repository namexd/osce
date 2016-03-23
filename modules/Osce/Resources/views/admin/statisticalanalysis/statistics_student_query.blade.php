@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
    <style>
        body{background-color: #fff!important;}
    </style>
@stop

@section('only_js')

@stop

@section('content')
    <div class="row table-head-style1">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考生成绩统计统计</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a href="javascript:history.go(-1)" class="btn btn-primary" style="float: right;">返回</a>
        </div>
    </div>
    <input type="hidden" id="parameter" value="{'pagename':'subject_papers_add}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="center">
            <h2>2016第一期OSCE考试理论考试</h2>
            <p>考试姓名：张三　　　考试用时：15分钟25秒 　　　最后得分：80</p>
        </div>


                <div class="form-group marb_25">
                    <h4>一、单选题 　<p>共四题,每题5分</p></h4>
                            <div class="form-group">
                                    <p>下列感染中，不具有传染性的是？</p>
                                    <span class="marr_15"><input type="radio" name="A">A.潜伏期感染</span>
                                　　<span class="marr_15"><input type="radio" name="B">B.显性感染潜伏期</span>
                               　　 <span class="marr_15"><input type="radio" name="C">C.显性感染症状明显期</span>　
                                    <span class="marr_15"><input type="radio" name="Ｄ">D.病因携带状态</span>
                            </div>
                            <p>考生答案：<span>D</span>（A）</p>
                            <p>解析：女，35岁，餐后突然起上腹持续疼痛，呕吐8h,查体：脉搏116次/分，收缩压68mmHg,上腹有压痛,肠鸣音无明显异常，WBC，14x109L尿定粉</p>

                </div>

    </div>
@stop{{-- 内容主体区域 --}}

