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
                <h5 class="title-label">科目成绩统计</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                <a  href="javascript:history.go(-1)" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;返回&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="row tabs">
                <div class="col-sm-3 col-md-3">考试：{{$exam}}<span></span></div>
                <div class="col-sm-3 col-md-3">科目：{{$subject}}<span></span></div>
                <div class="col-sm-3 col-md-3">平均成绩：{{$avgScore}}<span></span></div>
                <div class="col-sm-3 col-md-3">平均用时：{{$avgTime}}<span></span></div>
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>考生名字</th>
                    <th>排名</th>
                    <th>成绩</th>
                    <th>用时</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $item)
                    <tr>
                        <td>{{$item->student_name}}</td>
                        <td>{{$item->ranking}}</td>
                        <td>{{$item->exam_result_score}}</td>
                        <td>{{$item->exam_result_time}}</td>
                        <td>
                            <a href="{{route('osce.admin.getExamResultDetail')}}?id={{$item->exam_result_id}}">
                                <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if(count($data)>0)
            <div class="pull-left">
                共{{$data->total()}}条
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
    <script>
        $(function(){
            //删除用户
            $(".fa-trash-o").click(function(){
                var thisElement=$(this);
                var eid=thisElement.attr("eid");
                layer.alert('确认删除？',{btn:['确认','取消']},function(){
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