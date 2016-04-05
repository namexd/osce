@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <link href="{{asset('osce/common/select2-4.0.0/css/select2.css')}}" rel="stylesheet"/>
    <style>
    .table-head-style1{border-bottom: 1px solid #e7eaec;}
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
    .control-label{text-align: right;}


    /*sp老师选择*/
    .teacher{
        padding: 1px;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
        margin: 5px;
    }
    .teacher-list{
        height: 34px!important;
        width: 120px!important;
    }
    .teacher>div{
        margin-right: 1px;

    }
    .ibox-content{
       border: none;
    }
    .sp-teacher select{
        height: 31px;
        margin: 5px;
    }
    .pull-right>select{width: 120px;}
    .teacher-box{
        width: 75%;
    }
    .sp-teacher .pull-right{width:20%;}
    .teacher-warn{background-color: #ebccd1;}
    .teacher-primary{background-color: #a7d495;}
    .input-group.teacher.pull-left>.pull-left{line-height: 20px!important;}
    button.btn.btn-default.dropdown-toggle {
        height: 34px;
        width: 48px;
        display: inline-block;
        padding: 0;
        margin: 0;
    }
    #exam-place tbody tr td:last-child>a{color: #1ab394;}
    .panel-options .nav.nav-tabs{
        margin-left: 20px!important;
    }
    </style>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'examroom_assignment','spteacher_invitition':'{{route('osce.wechat.invitation.getInvitationList')}}','spteacher_list':'{{route('osce.admin.spteacher.getShow')}}','teacher_list':'{{route('osce.admin.exam.getTeacherListData')}}','url':'{{route('osce.admin.exam.getStationData')}}','list':'{{route('osce.admin.exam.getRoomListData')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考场安排</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                
            </div>
        </div>
    <div class="container-fluid ibox-content">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class=""><a href="{{route('osce.admin.exam.getEditExam')}}?id={{$id}}">基础信息</a></li>
                        <li class="active"><a href="{{route('osce.admin.exam.getChooseExamArrange',['id'=>$id])}}">考场安排</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamRemind',['id'=>$id])}}">待考区说明</a></li>
                    </ul>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="row">
                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postExamroomAssignmen')}}">
                            <input type="hidden" name="id" value="{{$id}}">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考场安排</label>
                                <div class="col-sm-10">
                                    <a  href="javascript:void(0)" class="btn btn-primary" id="add-new" style="float: right;{{$status==0?'':'display:none;'}}">新增</a>
                                    <table class="table table-bordered" id="examroom">
                                        <thead>
                                            <tr>
                                                <td>考试项目</td>
                                                <td>考站</td>
                                                <td>类型</td>
                                                <td>考官</td>
                                                <td>sp</td>
                                                <td>操作</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="">
                                                <td>3</td>
                                                <td>4</td>
                                                <td>5</td>
                                                <td>6</td>
                                                <td>8</td>
                                                    <a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                    <a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>
                                                    <a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" id="save" type="submit" {{$status==0?'':'style=display:none;'}}>保存</button>
                                    <a class="btn btn-white" href="{{route("osce.admin.exam.getExamList")}}">取消</a>

                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
<script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>

<script>
    $(function(){
        @if(isset($_GET['succ']) && $_GET['succ'] ==1)
            layer.msg('保存成功！',{skin:'msg-success',icon:1});
        @endif
    })
</script>
@stop