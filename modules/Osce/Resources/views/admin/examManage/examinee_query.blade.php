@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
        .exam-name{
            line-height: 34px;
            margin-right: 20px;
        }
        .exam-list{
            width: 70%;
        }
        .examinee-list{
            width: 80%;
        }
    </style>
@stop

@section('only_js')

@stop


@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考生查询</h5>
            </div>
        </div>
            <div class="panel blank-panel">
                <div class="container-fluid ibox-content">
                    <form method="get">
                        <div  class="row" style="margin:20px 0;">

                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <label class="pull-left exam-name">考试名称:</label>
                                <div class="pull-left exam-list">
                                    <input type="text" placeholder="请输入考试名称" name="exam_name" class="input-md form-control" style="width: 250px;" value="{{(isset($exam_name))?$exam_name:''}}">
                                </div>
                            </div>
                            <div class="input-group col-md-6 col-sm-6 col-xs-12">
                                <label class="pull-left exam-name">考生姓名:</label>
                                <div  class="pull-left examinee-list">
                                    <input type="text" placeholder="请输入姓名" name="student_name" class="input-md form-control" style="width: 250px;" value="{{(isset($student_name))?$student_name:''}}">
                                 <span class="input-group-btn pull-left">
                                    <button type="submit" style="height: 34px;" class="btn btn-sm btn-primary" id="search">搜索</button>
                                </span>
                                </div>
                            </div>
                        </div>
                    </form>


                    <table class="table table-striped" id="table-striped" style="background:#fff">
                        <thead>
                            <tr>
                                <th>考试名称</th>
                                <th>考生姓名</th>
                                <th>性别</th>
                                <th>学号</th>
                                <th>身份证号</th>
                                <th>联系电话</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                <td>{{ $item['exam_name']}}</td>
                                <td>{{ $item['student_name'] }}</td>
                                <td>{{is_null($item->userInfo)? '-':$item->userInfo->gender }}</td>
                                <td>{{ $item['code'] }}</td>
                                <td>{{ $item['idCard'] }}</td>
                                <td>{{ $item['mobile'] }}</td>
                                <td>
                                    <a href="{{route('osce.admin.machine.getCheckStudent')}}?id={{$item->id}}"><span class="read  state1 detail"><i class="fa  fa-search fa-2x"></i></span></a>
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
                </div>
            </div>
    </div>
@stop{{-- 内容主体区域 --}}