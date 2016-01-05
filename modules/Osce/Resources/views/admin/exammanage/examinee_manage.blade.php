@extends('osce::admin.layouts.admin_index')

@section('only_css')
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
        .search{
            width: 400px;
        }
        .ope-box{
            margin: 20px;
        }
        .operate button:first-child{
            margin-right: 20px;
        }
    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'add_basic','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'}" />
    <div class="ibox-title route-nav">
        <ol class="breadcrumb">
            <li><a href="#">考试安排</a></li>
            <li class="route-active">考试安排</li>
        </ol>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <div class="panel blank-panel">
                <div class="panel-heading">
                    <div class="panel-options">
                        <ul class="nav nav-tabs">
                            <li class=""><a href="#">基础信息</a></li>
                            <li class=""><a href="#">考场安排</a></li>
                            <li class=""><a href="#">邀请SP</a></li>
                            <li class="active"><a href="#">考生管理</a></li>
                            <li class=""><a href="#">智能排考</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row ope-box">
                    <div class="input-group search pull-left">
                        <input type="text" placeholder="姓名、学号、身份证、电话" class="input-md form-control">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-md btn-primary" id="search">搜索</button>
                        </span>
                    </div>
                    <div class="operate pull-right">
                        <button type="button" class="btn btn-md btn-white" id="">新增考生</button>
                        <button type="button" class="btn btn-md btn-white" id="">导入</button>
                    </div>
                </div>
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>考生姓名</th>
                        <th>性别</th>
                        <th>学号</th>
                        <th>身份证号</th>
                        <th>联系电话</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

                <div class="btn-group pull-right">

                </div>


            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

    <script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>

@stop