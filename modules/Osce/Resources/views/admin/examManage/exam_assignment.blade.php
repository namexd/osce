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
   <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script> 
@stop


@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_assignment','deletes':'{{route('osce.admin.exam.postDelete')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row table-head-style1 ">
        <div class="col-xs-6 col-md-2">
            <h5 class="title-label">考试安排</h5>
        </div>
        <div class="col-xs-6 col-md-2" style="float: right;">
            <a  href="{{route('osce.admin.exam.getAddExam')}}" class="btn btn-primary" style="float: right;">&nbsp;新增&nbsp;</a>
        </div>
    </div>
    <form class="container-fluid ibox-content" id="list_form">
        <table class="table table-striped" id="table-striped">
            <thead>
            <tr>
                <th>序号</th>
                <th>考试编号</th>
                <th>考试名称</th>
                <th>考试时间</th>
                <th>考试组成</th>
                <th>考试人数</th>
                <th>排考状态</th>
                <th style="width:12%;">操作</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>458</td>
                    <td>2016.03.31第4次若队列优化测试</td>
                    <td>2016-03-31 01:00 ~ 2016-03-31 04:00</td>
                    <td>1技能站</td>
                    <td>6</td>
                    <td><span class="co">是</span></td>
                    <td>
                        <a href=""><span class="read  state1 detail"><i class="fa  fa-cog fa-2x"></i></span></a>
                        <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        <button class="btn btn-primary" disabled type="button">发布成绩</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>578</td>
                    <td>2016.04.01第5次若队列优化测试</td>
                    <td>2016-04-01 01:00 ~ 2016-04-01 04:00</td>
                    <td>1技能站</td>
                    <td>6</td>
                    <td>否</td>
                    <td>
                        <a href=""><span class="read  state1 detail"><i class="fa  fa-cog fa-2x"></i></span></a>
                        <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        <button class="btn btn-primary" type="button">发布成绩</button>
                    </td>
                </tr>
                @foreach($data as $key=>$item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->id}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{date('Y-m-d H:i',strtotime($item->begin_dt))}} ~ {{date('Y-m-d H:i',strtotime($item->end_dt))}}</td>
                    <td>{{$item->constitute}}</td>
                    <td>{{$item->total}}</td>
                    <td>是</td>
                    <td value="{{$item->id}}">
                        <a href="{{route('osce.admin.exam.getEditExam',['id'=>$item->id])}}"><span class="read  state1 detail"><i class="fa  fa-cog fa-2x"></i></span></a>
                        <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                        <button class="btn btn-primary" disabled type="button">发布成绩</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pull-left">
            共{{$data->total()}}条
        </div>
        <div class="btn-group pull-right">
           {!! $data->appends($_GET)->render() !!}
        </div>
    </form>
</div>
@stop{{-- 内容主体区域 --}}