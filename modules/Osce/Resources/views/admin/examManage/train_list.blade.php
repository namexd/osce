@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    #start,#end{width: 160px;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/resourcemanage/resourcemanage.js')}}" ></script>  
    <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'train_list','URL':'{{route('osce.admin.getDelTrain')}}','reloads':'{{route('osce.admin.getTrainList')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考前培训</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{ route('osce.admin.getAddTrain')  }}" class="btn btn-primary" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>标题</th>
                <th>培训时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($list as $key => $data)
                <tr>
                    <td>{{$key+1}}</td>
                    <td><a href="{{route('osce.admin.getTrainDetail',array('id'=>$data->id))}}">{{ $data->name }}</a></td>
                    <td>{{date('Y-m-d H:i',strtotime($data->begin_dt))}} ~ {{date('Y-m-d H:i',strtotime($data->end_dt))}}</td>
                    <td value="{{$data->id}}">
                    <!--<td value="1">-->
                        <a href="{{ route('osce.admin.getEditTrain',array('id'=>$data->id)) }}"><span class="read  state1 detail"><i class="fa fa-pencil-square-o fa-2x"></i></span></a>
                        <a href="javascript:void(0)"><span class="read state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pull-left">
            共{{$list->total()}}条
        </div>
        <div class="pull-right">
            {!! $list->render() !!}
        </div>
    </form>

</div>
@stop{{-- 内容主体区域 --}}