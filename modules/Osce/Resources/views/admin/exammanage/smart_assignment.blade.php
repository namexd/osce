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
    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'add_basic','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
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
                            <li class=""><a href="#">考场安排</a></li>
                            <li class=""><a href="#">邀请SP</a></li>
                            <li class=""><a href="#">考生管理</a></li>
                            <li class="active"><a href="#">智能排考</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div>

            </div>
            <div>
                <button class="btn btn-default" type="button">智能排考</button>
                <button class="btn btn-default" type="button">保存方案</button>
                <button class="btn btn-default" type="button">导出excel</button>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

    <script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>

@stop