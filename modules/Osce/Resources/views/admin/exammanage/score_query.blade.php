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

@stop


@section('content')
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
                            <select id="select_Category" required  class="form-control m-b" name="exam_id">
                                @foreach($exams as $key=>$item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <label class="pull-left left-text">考站名称:</label>
                        <div class="pull-left right-list">
                            <select id="select_Category" required  class="form-control m-b" name="station_id">
                                @foreach($stations as $key=>$item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="input-group col-md-4 col-sm-4 col-xs-12">
                        <input type="text" placeholder="请输入考生姓名" style="height:36px;" name="name" class="input-md form-control">
                         <span class="input-group-btn">
                            <button type="submit" class="btn btn-md btn-primary" id="search">搜索</button>
                        </span>
                    </div>
                </div>
                <table class="table table-striped" id="table-striped">
                    <thead>
                    <tr>
                        <th>考试名称</th>
                        <th>考站名称</th>
                        <th>考生姓名</th>
                        <th>开始时间</th>
                        <th>用时</th>
                        <th>成绩</th>
                        <th>详情</th>
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
                            <td>{{$item->score}}</td>
                            <td><a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span></a></td>
                        </tr>
                        @endforeach
                        <tr>
                            <td>OSCE考试2015年第3期</td>
                            <td>肠胃炎考站</td>
                            <td>张三</td>
                            <td>2015-11-22 12:00:00</td>
                            <td>8:12</td>
                            <td>85</td>
                            <td><a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span></a></td>
                        </tr>
                        <tr>
                            <td>OSCE考试2015年第4期</td>
                            <td>疼风考站</td>
                            <td>李四</td>
                            <td>2015-11-22 12:00:00</td>
                            <td>12:34</td>
                            <td>55</td>
                            <td><a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span></a></td>
                        </tr>
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