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
<input type="hidden" id="parameter" value="{'pagename':'examroom_assignment','spteacher_invitition':'{{route('osce.wechat.invitation.getInvitationList')}}','spteacher_list':'{{route('osce.admin.spteacher.getShow')}}','teacher_list':'{{route('osce.admin.exam.getTeacherListData')}}','url':'{{route('osce.admin.exam.getStationData')}}','list':'{{route('osce.admin.exam.getRoomListData')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
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
                        <li class="active"><a href="{{route('osce.admin.exam.getExamroomAssignment',['id'=>$id])}}">考场安排</a></li>
                        <li class=""><a href="{{route('osce.admin.spteacher.getShow',['id'=>$id])}}">邀请SP</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                        <li class=""><a href="#">智能排考</a></li>
                    </ul>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="row">
                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postExamroomAssignmen')}}">
                            <input type="hidden" name="id" value="{{$id}}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试顺序</label>

                                <div class="col-sm-10">
                                    <select class="form-control" style="width:200px;">
                                        <option value="随机">随机</option>
                                        <option value="顺序">顺序</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考场安排</label>
                                <div class="col-sm-10">
                                    <a  href="javascript:void(0)" class="btn btn-outline btn-default" id="add-new" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
                                    <table class="table table-bordered" id="examroom">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>考场列表</th>
                                            <th>必考&选考</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody index="{{count($examRoomData)}}">
                                        <?php $key = 1; $k1 = 1; $k2 = 1;  ?>

                                        @forelse($examRoomData as $item)
                                            <tr class="pid-{{$k1++}}">
                                                <td>{{$key++}}</td>
                                                <td width="498">
                                                    <select class="form-control js-example-basic-multiple" multiple="multiple" name="room[{{$k2++}}][]">
                                                        @foreach($item as $value)
                                                            <option value="{{$value->id}}" selected="selected">{{$value->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="necessary">{{(count($item)==1)?'必考':'二选一'}}</td>
                                                <td>
                                                    <a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                    <a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>
                                                    <a href="javascript:void(0)"><span class="read state1 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>
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

                            <div class="form-group">
                                <label class="col-sm-2 control-label">监考老师</label>
                                <div class="col-sm-10">
                                    <table class="table table-bordered" id="exam-place">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>考站</th>
                                            <th>类型</th>
                                            <th width="300">老师</th>
                                            <th>SP老师</th>
                                            <th>邀请SP老师</th>
                                        </tr>
                                        </thead>
                                        <tbody index="{{count($examStationData)}}">
                                        @forelse($examStationData as $key => $item)
                                            <tr class="parent-id-{{$item->id}}">
                                                <td>{{$key+1}}<input type="hidden" name="station[{{$key+1}}][id]" value="{{$item->id}}"/></td>
                                                <td>{{$item->name}}</td>
                                                <td>{{($item->type==1)?'技能操作站':(($item->type==2)?'sp站':'理论操作站')}}</td>
                                                <td>
                                                    <select class="form-control teacher-teach js-example-basic-multiple" multiple="multiple" name="station[{{$key+1}}][teacher_id]">
                                                        <option value="{{$item->teacher_id}}" selected="selected">{{$item->teacher_name}}</option>
                                                    </select>
                                                </td>
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
                                                    <div class="pull-right" value="{{$key+1}}">
                                                        <select name="" class="teacher-list js-example-basic-multiple">
                                                            <option>==请选择==</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td><a href="javascript:void(0)" class="invitaion-teacher">发起邀请</a></td>
                                            </tr>
                                        @empty
                                        @endforelse
                                        </tbody>
                                    </table>

                                    <div class="btn-group pull-right">
                                       
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="javascript:history.back(-1)">取消</a>

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
<script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>
<script src="{{asset('osce/common/select2-4.0.0/js/select2.full.js')}}"></script>

@stop