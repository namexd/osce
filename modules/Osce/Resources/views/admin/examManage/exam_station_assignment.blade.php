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
    .teacher-primary{background-color: #dff0d8;}
    .input-group.teacher.pull-left>.pull-left{line-height: 20px!important;}
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
<input type="hidden" id="parameter" value="{'pagename':'station_assignment','spteacher_invitition':'{{route('osce.wechat.invitation.getInvitationList')}}','spteacher_list':'{{route('osce.admin.spteacher.getShow')}}','teacher_list':'{{route('osce.admin.exam.getTeacherListData')}}','url':'{{route('osce.admin.exam.getAjaxStationRow')}}','list':'{{route('osce.admin.exam.getAjaxStation')}}'}" />
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
                        <li class=""><a href="{{route('osce.admin.exam.getEditExam',['id'=>$id])}}">基础信息</a></li>
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
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postStationAssignment')}}">
                            <input type="hidden" name="id" value="{{$id}}">

                            <div class="station-container">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">&nbsp;</label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-sm-4"><label class="control-label">考站1</label></div>
                                            <div class="col-sm-6">
                                                    <label class="control-label col-sm-2">阶段：</label>
                                                    <select class="form-control col-sm-10" style="width: 381px;"></select>
                                            </div>
                                            <div class="col-sm-2">
                                                <a class="btn btn-primary" href="javascript:void(0)">必考</a>
                                                <a  href="javascript:void(0)" class="btn btn-primary" id="del-station" style="float: right;">删除</a>
                                            </div>
                                        </div>
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
                                                <tr>
                                                    <td>3</td>
                                                    <td>4</td>
                                                    <td>5</td>
                                                    <td>6</td>
                                                    <td>8</td>
                                                    <td>

                                                        <a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>
                                                        <a href="javascript:void(0)"><span class="read state2 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- 新增考站 -->
                            <div class="form-group">
                                <div class="col-sm-2 col-sm-offset-4">
                                    <button id="save" class="btn btn-primary" type="submit">保存考场安排</button>
                                </div>
                                <div class="col-sm-2">
                                    <a class="btn btn-white" href="javascript:history.back(-1)">取消</a>
                                </div>
                                <div class="col-sm-2">
                                    <a class="btn btn-primary" href="javascript:void(0)" id="station-add">新增考站</a>
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