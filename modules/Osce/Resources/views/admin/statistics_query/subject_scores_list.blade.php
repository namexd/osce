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
                <h5 class="title-label">科目成绩统计</h5>
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
                        <label class="pull-left exam-name">科目:</label>
                        <div  class="pull-left examinee-list">
                            <select name="" id="" class="form-control" style="width: 250px;">
                                <option value="">全部科目</option>
                            </select>
                            <span class="input-group-btn pull-left" style="margin-left: 10px;">
                                <button type="submit" class="btn btn-sm btn-primary" id="search">搜索</button>
                            </span>
                        </div>
                    </div>
                </div>

                <table class="table table-striped" id="table-striped" style="background:#fff">
                    <thead>
                    <tr>
                        <th>考试</th>
                        <th>科目</th>
                        <th>考试人数</th>
                        <th>平均成绩</th>
                        <th>平均用时</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $item)
                        <tr>
                            <th>{{$item->exam_name}}</th>
                            <th>{{$item->subject_name}}</th>
                            <th>{{$item->avg_total}}</th>
                            <th>{{$item->avg_score}}</th>
                            <th>{{$item->avg_time}}</th>
                            <th>操作</th>
                        </tr>
                    @endforeach
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