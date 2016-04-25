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
    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'examinee_manage',
    'background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}',
    'excel':'{{route('osce.admin.exam.postImportStudent')}}',
    'id':'{{$id}}',
    'judgeUrl':'{{route('osce.admin.exam.postJudgeStudent')}}',
    'deleteUrl':'{{route('osce.admin.exam.postDelStudent')}}',
    'reload':'{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}'}"/>
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
                            <li class=""><a href="{{route('osce.admin.exam.getEditExam')}}?id={{$id}}">基础信息</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getChooseExamArrange',['id'=>$id])}}">考场安排</a></li>
                            <li class=""><a href="{{route('osce.admin.exam-arrange.getInvigilateArrange',['id'=>$id])}}">考官安排</a></li>
                            <li class="active"><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getExamRemind',['id'=>$id])}}">待考区说明</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row ope-box">
                    <form action="{{route('osce.admin.exam.getExamineeManage')}}" method="get">

                        <div class="input-group search pull-left">
                            <input type="text" placeholder="姓名、学号、身份证、电话" class="form-control" name="keyword" value="{{@$keyword}}">
                            <input type="hidden" name="id" value="{{$id}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search">搜索</button>
                        </span>
                        </div>
                    </form>
                    <div class="operate pull-right">
                        <a href="{{route('osce.admin.exam.getAddExaminee',['id'=>$id])}}" {{$status==0?'':'style=display:none;'}} class="btn btn-outline btn-default">新增考生</a>
                        <a href="{{route('osce.admin.exam.getdownloadStudentImprotTpl')}}" class="btn btn-outline btn-default">下载模板</a>
                        <a  href="javascript:void(0)" class="btn btn-outline btn-default" id="file1" examId="{{$id}}" {{$status==0?'':'style=display:none;'}}>导入考生
                            <input type="file" name="student" id="file0" multiple="multiple" />
                        </a>
                    </div>
                </div>
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>考生姓名</th>
                        <th>性别</th>
                        <th>学号</th>
                        <th>身份证号</th>
                        <th>准考证号</th>
                        <th>班级</th>
                        <th>班主任姓名</th>
                        <th>联系电话</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>{{$item->userInfo->gender}}</td>
                            <td>{{$item->code}}</td>
                            <td>{{$item->idcard}}</td>
                            <td>{{$item->exam_sequence}}</td>
                            <td>{{$item->grade_class}}</td>
                            <td>{{$item->teacher_name}}</td>
                            <td>{{$item->mobile}}</td>
                            <td>
                                <a href="{{route('osce.admin.exam.postEditExaminee',['id'=>$item->id])}}" {{$status==0?'':'style=display:none;'}}><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                                <span class="read  state2 delete" sid="{{$item->id}}" examid="{{$id}}" {{$status==0?'':'style=display:none;'}}><i class="fa fa-trash-o fa-2x"></i></span>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
                <div class="pull-left">
                    共{{$data->total()}}条
                </div>
                <div class="btn-group pull-right">
                    {!! $data->appends($_GET)->render() !!}
                </div>
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop