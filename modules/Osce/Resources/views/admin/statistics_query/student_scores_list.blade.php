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
                <h5 class="title-label">学生成绩统计</h5>
            </div>
        </div>
        <div class="panel blank-panel">
            <form class="container-fluid ibox-content" action="" method="get" id="list_form">
                <div  class="row" style="margin:20px 0;">

                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <label class="pull-left exam-name">考试:</label>
                        <div class="pull-left exam-list">
                            <select name="" id="" class="form-control" style="width: 250px;">
                                <option value="">全部考试</option>
                            </select>
                        </div>
                    </div>
                    <div class="input-group col-md-6 col-sm-6 col-xs-6">
                        <div  class="pull-left examinee-list">
                            <input type="text" placeholder="请输入姓名、考号、身份证号" name="student_name" class="input-md form-control" style="width: 250px;" value="">
                            <span class="input-group-btn pull-left">
                                <button type="submit" class="btn btn-sm btn-primary" id="search">搜索</button>
                            </span>
                        </div>
                    </div>
                </div>

                <table class="table table-striped" id="table-striped" style="background:#fff">
                    <thead>
                        <tr>
                            <th>姓名</th>
                            <th>学号</th>
                            <th>考试名称</th>
                            <th>考站数</th>
                            <th>总成绩</th>
                            <th>排名</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($data as $item)
                        <tr>
                            <th>{{$item->student_name}}</th>
                            <th>{{$item->student_code}}</th>
                            <th>{{$item->exam_name}}</th>
                            <th>{{$item->station_total}}</th>
                            <th>{{$item->score_total}}</th>
                            <th>{{$item->ranking}}</th>
                            <th>操作</th>
                        </tr>
                    @empty
                        <tr><td colspan="6">目前没有已结束的考试</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="pull-left">

                </div>
                <div class="btn-group pull-right">

                </div>
            </form>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}