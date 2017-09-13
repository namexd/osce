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
    <script>
        $(function () {
            $("#export").click(function () {
                var exam_id=$('#exam_id option:selected');
                if (exam_id.val()==""){
                    layer.alert('请选择一场考试');
                }else {
                    var a=document.getElementById('export');
                    a.href='{{route('osce.admin.getExportAllScore')}}?exam_id=';
                    a.href+=exam_id.val();
                }
            })
        })
    </script>
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
                    <form action="{{route('osce.admin.course.getStudentAllScore')}}" method="get">
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
                                <span class="input-group-btn pull-left">
                                    <button type="submit" class="btn btn-sm btn-primary" style="height:34px;border-radius:3px;margin-right:50px;" id="search">搜索</button>
                                     <a class="btn btn-md btn-primary" id="export" style="height:34px;border-radius:3px" href="javascript:void(0)">导出</a>
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
                            <th>技能考试总成绩</th>
                            <th>理论考试总成绩</th>
                            <th>总成绩</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($newdata as $item)
                        <tr>
                            <td>{{$item->student_name}}</td>
                            <td>{{$item->student_code}}</td>
                            <td>{{$item->exam_name}}</td>
                            <td>{{$item->station_total}}</td>
                            <td>{{$item->score_total}}分</td>
                            <td>{{$item->score_theory}}分</td>
                            <td>{{$item->score_all}}分</td>
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