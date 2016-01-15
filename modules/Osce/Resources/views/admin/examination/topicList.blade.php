@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .blank-panel .panel-heading {margin-left: -20px;}
    #start,#end{width: 160px;}
    .input-group input{height: 34px;}
    </style>
@stop

@section('only_js')
    
@stop


@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考核点管理</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.topic.getAddTopic')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
    <form class="container-fluid ibox-content" id="list_form">
        <div class="panel blank-panel">
          <form method="post" action="{{route('osce.admin.topic.getList')}}">
            <div class="input-group" style="width: 290px;margin:20px 0;">
                <input type="text" name="name" placeholder="请输入关键字" class="input-sm form-control">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-sm btn-primary" id="search">搜索</button>
                </span>
            </div>
          </form>

            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>课题名称</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @forelse($list as $key => $item)

                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->title}}</td>
                        <td>{{$item->description}}</td>
                        <td>
                            <a href="{{route('osce.admin.topic.getEditTopic',['id'=>$item->id])}}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                            <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>

            <br/>
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
    </form>
</div>
@stop{{-- 内容主体区域 --}}