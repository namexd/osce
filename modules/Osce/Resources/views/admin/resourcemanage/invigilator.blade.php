@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
        .active{
            font-weight: 700;
        }
        .route-nav{
            margin-bottom: 30px;
        }
        .header{
            padding: 5px 15px;
        }
    </style>
@stop
@section('only_js')
@stop
@section('content')
    <div class="ibox-title header">
        <div class="pull-left">
            <h3>人员管理</h3>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('osce.admin.invigilator.getAddInvigilator')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
        </div>
    </div>
    <div class="container-fluid ibox-content">
        <ul class="nav nav-tabs">
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
                        <a href="{{route('osce.admin.invigilator.postEditInvigilator',['id'=>$item->id])}}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o"></i></span></a>
                        <a href="{{route('osce.admin.invigilator.getDelInvitation',['id'=>$item->id])}}"><span class="read  state2"><i class="fa fa-trash-o"></i></span></a>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
        <div class="row">
            <div class="pull-left">
                共{{$list->total()}}条
            </div>
            <div class="pull-right">
                <nav>
                    <ul class="pagination">
                        {!! $list->appends($_GET)->render() !!}
                    </ul>
                </nav>
            </div>
        </div>

    </div>
@stop