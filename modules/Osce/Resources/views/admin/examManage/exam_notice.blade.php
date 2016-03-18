@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')
 <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_notice','URL':'{{route("osce.admin.notice.getDelNotice")}}','reloads':'{{route("osce.admin.notice.getList")}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">资讯&通知</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.notice.getAddNotice')}}" class="btn btn-primary" style="float: right;">&nbsp;&nbsp;新增&nbsp;&nbsp;</a>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form">
            <div class="panel blank-panel">
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>标题</th>
                        <th>发布时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($list as $key => $item)
                    <tr>
                        <td>{{$item['name']}}</td>
                        <td>{{$item['created_at']}}</td>
                        <td>
                            <a href="{{route('osce.admin.notice.getEditNotice',['id'=>$item['id']])}}"><span class="read  state1"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                            <a href="javascript:void(0)" value="{{$item['id']}}"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
                <div class="pull-left">
                    共{{$list->total()}}条
                </div>
                <div class="btn-group pull-right">
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