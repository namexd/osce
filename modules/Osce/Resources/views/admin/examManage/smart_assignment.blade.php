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
            width:1000px;
            height: 600px;
            margin-top: 20px;
            overflow: scroll;
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
        /*.time-list{
            width: 200px;
            margin-top: 5px;
            text-align: center;
        }
        .time-list li{
            border: 1px solid #ccc;
        }*/
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
            cursor: pointer;
            margin: 5px;
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
            padding: 5px;
            font-weight: 700;
        }
        .time>li{
            border: 1px solid #ccc;
        }
        .dd-active{
            background-color:#ccc;
        }
        .error{
            background-color:#ff0000;
        }
        .operate{
            width: 600px;
            margin: 20px auto;
        }
        .operate button{
            margin-left: 50px;
        }
        .classroom-box{
            min-height: 500px;
            text-align: center;
        }

        .clicked{
            background-color: #0a6aa1;
        }
        p{
            margin: 0;
        }
        .tables>li{
            width: 250px;
            float: left;
        }
        .screening_box>ul{
            float: left;
        }
        .screening_box{
            margin-top: 10px;
        }
        .screening_box>ul:first-child{
            width: 200px;
        }
    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'smart_assignment','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'
    ,'makePlanUrl':'{{route('osce.admin.arrangement.postBegin',['exam_id'=>$_GET['id']])}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">

            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form" action="{{route('osce.admin.arrangement.postStore')}}" method="post">
            <input type="hidden" name="exam_id" value="{{$_GET['id']}}">
            <div class="panel blank-panel">
                <div class="panel-heading">
                    <div class="panel-options">
                        <ul class="nav nav-tabs">
                            <li class=""><a href="{{route('osce.admin.exam.getEditExam')}}?id={{$_GET['id']}}">基础信息</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getChooseExamArrange',['id'=>$_GET['id']])}}">考场安排</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$_GET['id']])}}">考生管理</a></li>
                            <li class="active"><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$_GET['id']])}}">智能排考</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getExamRemind',['id'=>$_GET['id']])}}">待考区说明</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="assign-box clearfloat">

                <div class="classroom-box">

                </div>


            </div>
            <textarea id="plan" style="display: none;">{{json_encode($plan)}}</textarea>
            @if($exam->status==0)
            <div class="operate" >
                <button class="btn btn-default" type="button" id="makePlan">智能排考</button>
                <button class="btn btn-default save" type="submit">保存方案</button>
                <button class="btn btn-default" type="button" style="display: none;">导出excel</button>
            </div>
            @endif
        </form>
    </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop