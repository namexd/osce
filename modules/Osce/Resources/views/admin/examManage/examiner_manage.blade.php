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
        .search{
            width: 400px;
        }
        .ope-box{
            margin: 20px;
        }
        .operate button:first-child{
            margin-right: 20px;
        }
        .delete{
            cursor: pointer;
        }
        #file1{
            position: relative;
            display: inline-block;
            overflow: hidden;
        }
        #file1 input{
            position: absolute;
            right: 0;
            top: 0;
            opacity: 0;
            font-size: 100px;
        }
        #add-basic tbody tr td:last-child>a{color: #1ab394;}
        .select2-container--default{width:100% !important;}
        .select2-container--default .select2-selection--multiple{border:1px solid #e5e6e7;}
        .select2-container--default.select2-container--focus .select2-selection--multiple {border:1px solid  #1ab394 !important;outline: 0;}
        .select2-container--open .select2-selection--single {background-color: #fff;border: 1px solid #1ab394 !important;border-radius: 4px;}
        .select2-container--open .select2-dropdown {border: 1px solid #1ab394 !important;}
        .select2-container--open .select2-search--dropdown .select2-search__field {border: 1px solid #1ab394 !important;}
    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'examiner_manage','teacher_list':'{{route('osce.admin.exam-arrange.getInvigilatesBySubject')}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="">
                <div class="panel-heading">
                    <div class="panel-options">
                        <ul class="nav nav-tabs">
                            <li class=""><a href="{{route('osce.admin.exam.getEditExam',['id'=>$id])}}">基础信息</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getChooseExamArrange',['id'=>$id])}}">考场安排</a></li>
                            <li class="active"><a href="{{route('osce.admin.exam-arrange.getInvigilateArrange',['id'=>$id])}}">考官安排</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getExamRemind',['id'=>$id])}}">待考区说明</a></li>
                        </ul>
                    </div>
                </div>
                <table class="table table-bordered" id="add-basic">
                    <thead>
                    <tr>
                        <th>考试项目</th>
                        <th>考站</th>
                        <th>类型</th>
                        <th>考官</th>
                        <th>ＳＰ</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-4 time-modify">
                     <button id="save" class="btn btn-primary" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</button>
                     <a class="btn btn-white" href="javascript:history.back(-1)" {{--href="{{route("osce.admin.exam.getExamList")}}"--}}>&nbsp;&nbsp;取消&nbsp;&nbsp;</a>
                     <a class="btn btn-white" href="">全部邀请</a>
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>
    <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop