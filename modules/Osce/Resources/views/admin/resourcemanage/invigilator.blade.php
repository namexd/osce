@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
        .active{
            font-weight: 700;
        }
        .route-nav{
            margin-bottom: 30px;
        }
        ul{
            margin: 0;
        }

    </style>
@stop
@section('only_js')
    <script src="{{asset('osce/admin/resourcemanage/js/resourcemanage.js')}}" ></script>
@stop
@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'invigilator','deletes':'{{route('osce.admin.invigilator.postDelInvitation')}}','firstpage':'{{route('osce.admin.invigilator.getInvigilatorList')}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">人员管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.invigilator.getAddInvigilator')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <ul class="nav nav-tabs teacher-tabs">
                <li role="presentation" class="active"><a href="{{route('osce.admin.invigilator.getInvigilatorList')}}">监巡考老师</a></li>
                <li role="presentation"><a href="{{route('osce.admin.invigilator.getSpInvigilatorList')}}">SP老师</a></li>
            </ul>
            <table class="table table-striped" id="table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>姓名</th>
                        <th>联系电话</th>
                        <th>最后登录时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list as $key => $item)
                    <tr>
                        <td>{{$key+1}}</td>
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