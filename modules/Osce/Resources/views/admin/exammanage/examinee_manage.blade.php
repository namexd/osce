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
    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'examinee_manage',
    'background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}',
    'excel':'{{route('osce.admin.exam.postImportStudent')}}',
    'deleteUrl':'{{route('osce.admin.exam.getDelStudent')}}'}"/>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
            </div>
        </div>
            <div class="panel blank-panel">
                <div class="panel-heading">
                    <div class="panel-options">
                        <ul class="nav nav-tabs">
                            <li class=""><a href="{{route('osce.admin.exam.getEditExam')}}?id={{$id}}">基础信息</a></li>
                            <li class=""><a href="{{route('osce.admin.exam.getExamroomAssignment', ['id'=>$id])}}">考场安排</a></li>
                            <li class=""><a href="#">邀请SP</a></li>
                            <li class="active"><a href="#">考生管理</a></li>
                            <li class=""><a href="#">智能排考</a></li>
                        </ul>
                    </div>
                </div>
                <div class="row ope-box">
                    <form action="{{route('osce.admin.exam.getExamineeManage')}}" method="get">

                        <div class="input-group search pull-left">
                            <input type="text" placeholder="姓名、学号、身份证、电话" class="form-control" name="keyword" value="{{$keyword}}">
                            <input type="hidden" name="id" value="{{$id}}">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-sm btn-primary" id="search">搜索</button>
                        </span>
                        </div>
                    </form>
                    <div class="operate pull-right">
                        <a href="{{route('osce.admin.exam.getAddExaminee',['id'=>$id])}}">
                            <button type="button" class="btn btn-md btn-white" id="">新增考生</button>
                        </a>
                        导入考生
                        <a  href="javascript:void(0)" class="btn btn-outline btn-default" id="file1" examId="" style="height:34px;padding:5px;width:184px;">
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
                        <th>联系电话</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>&nbsp;</td>
                            <td>{{$item->code}}</td>
                            <td>{{$item->idcard}}</td>
                            <td>{{$item->mobile}}</td>
                            <td>
                                {{--<a href="{{route('osce.admin.exam.getDelStudent')}}?id={{$item->id}}&exam_id={{$id}}"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>--}}
                                <span class="read  state2 delete" sid="{{$item->id}}" examid="{{$id}}"><i class="fa fa-trash-o fa-2x"></i></span>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>

                <div class="btn-group pull-right">

                </div>


            </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')
    <script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>
@stop