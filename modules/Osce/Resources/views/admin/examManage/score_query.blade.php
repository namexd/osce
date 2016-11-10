@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
        .left-text{
            line-height: 34px;
            margin-right: 20px;
        }
        .right-list{
            width: 60%;
        }
    </style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'score_query','URL':'{{route("osce.admin.getExamStationList")}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">成绩查询</h5>
            </div>
        </div>
        <form class="container-fluid ibox-content" id="list_form" action="{{route('osce.admin.geExamResultList')}}" method="get">
            <div class="panel blank-panel">
                <div  class="row" style="margin:20px 0;">
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <label class="pull-left left-text">考试名称:</label>

                        <div class="pull-left right-list">
                            <select id="select_Category" class="form-control m-b" name="exam_id">
                                <option value="">全部考试</option>
                                @foreach($exams as $key=>$item)
                                <option value="{{$item->id}}" {{$exam_id==$item->id?"selected":""}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <label class="pull-left left-text">考站名称:</label>
                        <div class="pull-left right-list">
                            <select id="station_Category" class="form-control m-b" name="station_id">
                                <option value="">全部考站</option>
                                @foreach($stations as $key=>$item)
                                <option value="{{$item->id}}" {{$station_id==$item->id?"selected":""}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-group col-md-4 col-sm-4 col-xs-12">
                        <input type="text" placeholder="请输入考生姓名" style="height:36px;" name="name" value="{{$name!=null?$name:''}}"class="input-md form-control">
                         <span class="input-group-btn">
                            <button type="submit" class="btn btn-md btn-primary" id="search">搜索</button>
                        </span>
                    </div>
                </div>
                <table class="table table-striped" id="table-striped" style="margin-bottom: 20px;">
                    <thead>
                    <tr>
                        <th>考试名称</th>
                        <th>考站名称</th>
                        <th>考生姓名</th>
                        <th>开始时间</th>
                        <th>用时</th>
                        <th>成绩</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($examResults as $key=>$item)
                        <tr>
                            <td>{{$item->exam_name}}</td>
                            <td>{{$item->station_name}}</td>
                            <td>{{$item->student_name}}</td>
                            <td>{{$item->begin_dt}}</td>
                            <td>{{$item->time}}</td>
                            <td>{{$item->score}}分</td>
                            <td>
                                <a href="{{route('osce.admin.getExamResultDetail')}}?id={{$item->id}}"><span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pull-left">
                    共{{$examResults->total()}}条
                </div>
                <div class="btn-group pull-right">
                   {!! $examResults->appends($_GET)->render() !!}
                </div>
            </div>
        </form>
    </div>
@stop{{-- 内容主体区域 --}}