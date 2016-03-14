@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        .tabs{
            margin: 20px 0;
            font-weight: 700;
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
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="{{route('osce.admin.course.getStudentScore')}}" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;返回&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="row tabs">
                <div class="col-sm-2 col-md-3">考试：{{$studentList[0]->exam_name}}<span></span></div>
                <div class="col-sm-2 col-md-4">
                    考试时间：{{date('Y-m-d H:i', strtotime($studentList[0]->begin_dt))}} ~ {{date('Y-m-d H:i', strtotime($studentList[0]->end_dt))}}
                    <span></span>
                </div>
                <div class="col-sm-2 col-md-2">姓名：{{$studentList[0]->student_name}}<span></span></div>
                <div class="col-sm-2 col-md-3">学号：{{$studentList[0]->student_code}}<span></span></div>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>科目</th>
                    <th>考试时间</th>
                    <th>耗时</th>
                    <th>成绩</th>
                    <th>评价老师</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($studentList as $key => $item)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$item->title}}</td>
                        <td>{{date('Y-m-d H:i', strtotime($item->begin_dt))}}</td>
                        <td>{{$item->time}}</td>
                        <td>{{$item->score}}分</td>
                        <td>{{$item->grade_teacher}}</td>
                        <td>
                            <a href="{{route('osce.admin.course.getResultVideo',[
                            'exam_id'=>$item->exam_id,
                            'student_id'=>$item->student_id,
                            'station_id'=>$item->station_id])}}">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if ($studentList->count() > 0)
            <div class="pull-left">
                共{{$studentList->total()}}条
            </div>
            <div class="btn-group pull-right">
               {!! $studentList->appends($_GET)->render() !!}
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
    <script>
        $(function(){
            //删除用户
            $(".fa-trash-o").click(function(){
                var thisElement=$(this);
                var eid=thisElement.attr("eid");
                layer.alert('确认删除？',{title:"删除",btn:['确认','取消']},function(){
                    $.ajax({
                        type:'post',
                        async:true,
                        url:'{{route('osce.admin.machine.postMachineDelete')}}',
                        data:{id:eid, cate_id:1},
                        success:function(data){
                            if(data.code == 1){
                                location.href='{{route("osce.admin.machine.getMachineList",["cate_id"=>1])}}'
                            }else {
                                layer.msg(data.message);
                            }
                        }
                    })
                });
            })
        })
    </script>
@stop{{-- 内容主体区域 --}}