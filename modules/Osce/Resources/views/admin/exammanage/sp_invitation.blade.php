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

        .teacher{
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
        }
        .teacher>div{
            margin-right: 5px;

        }
        .ibox-content{
           border: none;
        }
        .sp-teacher select{
            height: 31px;
            margin: 5px;
        }
        .teacher{
            margin: 5px;
        }
        .teacher-box{
            width: 320px;
        }
        .teacher-warn{
            background-color: #ed5565;
            color: #fff;
        }
        .teacher-primary{
            background-color: #1ab394;
            color: #fff;
        }

    </style>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'sp_invitation','teacher_list':'{{route('osce.admin.spteacher.getShow')}}'}" />
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
                            {{--<li class=""><a href="{{route('osce.admin.exam.getEditExam')}}?id={{$id}}">基础信息</a></li>--}}
                            {{--<li><a href="{{route('osce.admin.exam.getExamroomAssignment',['id'=>$id])}}">考场安排</a></li>--}}
                            {{--<li class="active"><a href="{{route('osce.admin.spteacher.getInvitationIndex',['id'=>$id])}}">邀请SP</a></li>--}}
                            {{--<li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>--}}
                            {{--<li class=""><a href="#">智能排考</a></li> --}}
                        </ul>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>sp考站</th>
                            <th>sp老师</th>
                            <th><a href="">全部邀请</a></th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr value="1">
                                <td>1</td>
                                <td>肠胃炎考站</td>
                                <td class="sp-teacher">
                                    <div class="teacher-box pull-left">
                                        <div class="input-group teacher pull-left" value="1">
                                            <div class="pull-left">张老师</div>
                                            <div class="pull-left"><i class="fa fa-times"></i></div>
                                        </div>
                                        <div class="input-group teacher pull-left teacher-primary" value="3">
                                            <div class="pull-left">张老师</div>
                                            <div class="pull-left"><i class="fa fa-times"></i></div>
                                        </div>
                                        <div class="input-group teacher pull-left teacher-warn" value="2">
                                            <div class="pull-left">张老师</div>
                                            <div class="pull-left"><i class="fa fa-times"></i></div>
                                        </div>
                                    </div>
                                    <div class="pull-right">
                                        <select name="" class="teacher-list">
                                            <option value="">选择</option>
                                            <option value="1">张老师</option>
                                            <option value="2">王老师</option>
                                        </select>
                                    </div>
                                </td>
                                <td><a href="">发起邀请</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                <div class="btn-group pull-right">

                </div>
                <div class="form-group">
                    <div class="col-sm-12 col-xs-12 col-sm-offset-5 col-xs-offset-5">
                        <button class="btn btn-primary" type="submit">保存</button>
                        <button class="btn btn-white" type="submit">取消</button>

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