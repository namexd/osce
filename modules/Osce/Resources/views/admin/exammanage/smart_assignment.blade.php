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
        .assign-box{
        }
        .classroom-box>ul>li{
            float: left;
        }
        .clearfloat:after{
            content: '';
            visibility: hidden;
            display: block;
            clear: both;
            height: 0;
        }
        .time-list{
            width: 50px;
            height: 500px;
            background-color: #ccc;
        }
        .assign-box>div{
            float: left;
        }
        .classroom-box{
            width: 1002px;
        }
        ul,dl{
            padding: 0;
            margin: 0;
        }
        dt{
            font-weight: inherit;
        }
        .end{
            margin-top: 460px;
        }
        dd{
            float: left;
            width: 60px;
        }
        /*dl:hover{
            background-color: #f4f4f4;
            cursor: pointer;
        }*/
        dl{
            border: 1px solid #ccc;
        }
        .title{
            border: 1px solid #ccc;
            background-color: #eee;
        }
        .dd-active{
            background-color:#ccc;
        }
        .error{
            background-color:#ff0000;
        }
    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'smart_assignment','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'}" />
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
                            <li class=""><a href="{{route('osce.admin.exam.getEditExam')}}?id={{$id}}">基础信息</a></li>
                            <li class="active"><a href="{{route('osce.admin.exam.getExamroomAssignment',['id'=>$id])}}">考场安排</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="assign-box clearfloat">
                <div class="time-list">
                    <p>8:00</p>
                    <p class="end">12:00</p>
                </div>
                <div class="classroom-box">

                </div>
            </div>
            <div>
                <button class="btn btn-default" type="button">智能排考</button>
                <button class="btn btn-default save" type="button">保存方案</button>
                <button class="btn btn-default" type="button">导出excel</button>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

    <script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>

@stop