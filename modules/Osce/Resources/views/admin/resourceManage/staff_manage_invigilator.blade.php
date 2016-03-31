@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
        .active{
            font-weight: 700;
        }
        .route-nav{
            margin-bottom: 30px;
        }
        ul{margin: 0;}
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
        .col-xs-6.col-md-3 a{float: right;}
    </style>
@stop
@section('only_js')
    <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop
@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'staff_manage_invigilator',
        'deletes':'{{route('osce.admin.invigilator.postDelInvitation',['type'=>$type])}}',
        'firstpage':'{{route('osce.admin.invigilator.getInvigilatorList',['type'=>$type])}}',
        'excel':'{{route('osce.admin.invigilator.postImportTeachers',['type'=>$type])}}'}" />

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">人员管理</h5>
            </div>
            <div class="col-xs-6 col-md-3" style="float: right;">
                <a href="{{route('osce.admin.invigilator.getdownloadTeacherImprotTpl')}}" class="btn btn-outline btn-default">下载模板</a>
                <a  href="javascript:void(0)" class="btn btn-outline btn-default" id="file1">导入
                    <input type="file" name="teacher" id="file0" multiple="multiple" />
                </a>
                @if($type==1)
                    <a  href="{{route('osce.admin.invigilator.getAddInvigilator')}}" class="btn btn-primary">新增</a>     {{--//进入新增监考老师表单页--}}
                @else
                    <a  href="{{route('osce.admin.invigilator.getAddPatrol')}}" class="btn btn-primary">新增</a>          {{--//进入新增巡考老师表单页--}}
                @endif

            </div>
        </div>
        <div class="container-fluid ibox-content">
            <ul class="nav nav-tabs teacher-tabs">
                <li role="presentation" class="{{($type==1)?'active':''}}"><a href="{{route('osce.admin.invigilator.getInvigilatorList')}}">考官</a></li>
                <li role="presentation"><a href="{{route('osce.admin.invigilator.getSpInvigilatorList')}}">SP</a></li>
                <li role="presentation" class="{{($type==3)?'active':''}}"><a href="{{route('osce.admin.invigilator.getInvigilatorList',['type'=>3])}}">巡考</a></li>
            </ul>
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>姓名</th>
                        <th>联系电话</th>
                        <th>最近登录</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list as $key => $item)
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->userInfo->mobile or '-'}}</td>
                        <td>{{is_null($item->userInfo)? '-':$item->userInfo->lastlogindate}}</td>
                        <td>
                            <a href="{{route('osce.admin.invigilator.postEditInvigilator',['id'=>$item->id])}}">
                                <span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span>
                            </a>
                            <a href="javascript:void(0)" class="delete" tid="{{$item->id}}"><span class="read state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
                <div class="pull-left">
                    共{{$list->total()}}条
                </div>
                <div class="pull-right">
                    {!! $list->appends($_GET)->render() !!}
                </div>
        </div>
    </div>
@stop