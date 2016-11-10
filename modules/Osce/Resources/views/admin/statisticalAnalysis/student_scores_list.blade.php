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
                <h5 class="title-label">考生成绩统计</h5>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content">
                <div  class="row" style="margin:20px 0;">
                    <form action="{{route('osce.admin.course.getStudentScore')}}" method="get">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <label class="pull-left exam-name">考试:</label>
                            <div class="pull-left exam-list">
                                <select name="exam_id" id="exam_id" class="form-control" style="width: 250px;">
                                    @forelse($examDownlist as $exam)
                                        <option value="{{$exam->id}}" {{$exam_id == $exam->id?'selected':''}}>{{$exam->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="input-group col-md-6 col-sm-6 col-xs-6">
                            <div  class="pull-left examinee-list">
                                <input type="text" placeholder="请输入姓名、考号、身份证号" name="message" class="input-md form-control" style="width: 250px;margin-right:10px;"
                                       value="{{$message}}">
                                <span class="input-group-btn pull-left">
                                    <button type="submit" class="btn btn-sm btn-primary" style="height:34px;border-radius:3px" id="search">搜索</button>
                                </span>
                            </div>
                        </div>
                    </form>
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
                            <td>{{$item->student_name}}</td>
                            <td>{{$item->student_code}}</td>
                            <td>{{$item->exam_name}}</td>
                            <td>{{$item->station_total}}</td>
                            <td>{{$item->score_total}}分</td>
                            <td>{{$item->ranking}}</td>
                            <td>
                                <a href="{{route('osce.admin.course.getStudentDetails',['student_id'=>$item->student_id])}}">
                                    <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">{{$backMes}}</td></tr>
                    @endforelse
                    </tbody>
                </table>
                @if(count($data)>0)
                    <div class="pull-left">
                        共{{$data->count()}}条
                    </div>
                    <div class="btn-group pull-right">
                        {!! $data->appends($_GET)->render() !!}
                    </div>
                @else
                    <div class="pull-left">
                        共0条
                    </div>
                    <div class="btn-group pull-right">
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}