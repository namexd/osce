@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .blank-panel .panel-heading {margin-left: -20px;}
    span.laydate-icon{
        border: 0;
        background-position: right;
        background-image: none;
        padding-right: 27px;
        display: inline-block;
        width: 151px;
        line-height: 30px;
    }
    .form-group {
        margin: 15px;
        height: 30px;
        line-height: 30px;
    }
    table tr td input[type="checkbox"]{margin-top: 0}
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e5e6e7;
        border-radius: 0;
    }
    .select2-container--default .select2-selection--multiple:focus{
        border-color: #1ab394!important;
        width: 100%;
    }
    </style>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'examroom_assignment'}" />
<div class="ibox-title route-nav">
    <ol class="breadcrumb">
        <li><a href="#">考试安排</a></li>
        <li class="route-active">考场安排</li>
    </ol>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考场安排</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                
            </div>
        </div>
    <form class="container-fluid ibox-content" id="list_form">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="#">基础信息</a></li>
                        <li class="active"><a href="#">考场安排</a></li>
                        <li class=""><a href="#">邀请SP</a></li>
                        <li class=""><a href="#">考生管理</a></li>
                        <li class=""><a href="#">智能排考</a></li>
                    </ul>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="row">
                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试顺序</label>

                                <div class="col-sm-10">
                                    <select class="form-control" style="width:200px;">
                                        <option value="随机">随机</option>
                                        <option value="顺序">顺序</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考场安排</label>
                                <div class="col-sm-10">
                                    <a  href="javascript:void(0)" class="btn btn-outline btn-default" id="add-new" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
                                    <table class="table table-bordered" id="examroom">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>考场列表</th>
                                            <th>必考&选考</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody index="0">

                                        </tbody>
                                    </table>

                                    <div class="btn-group pull-right">
                                       
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考场安排</label>
                                <div class="col-sm-10">
                                    <table class="table table-bordered" id="exam-place">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>考站</th>
                                            <th>老师</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>3:00</td>
                                                <td>
                                                    <select class="form-control">
                                                        <option>==请选择==</option>
                                                        <option>李老师</option>
                                                        <option>张老师</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="btn-group pull-right">
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <button class="btn btn-white" type="submit">取消</button>

                                </div>
                            </div>



                    </div>

                </div>
            </div>

        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>
<script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>

@stop