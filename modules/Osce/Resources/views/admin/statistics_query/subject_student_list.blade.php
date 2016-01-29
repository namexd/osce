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
                <a  href="" class="btn btn-outline btn-default" style="float: right;">&nbsp;&nbsp;返回&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <div class="row tabs">
                <div class="col-sm-2 col-md-2">考试：<span></span></div>
                <div class="col-sm-2 col-md-2">科目：<span></span></div>
                <div class="col-sm-2 col-md-2">平均成绩：<span></span></div>
                <div class="col-sm-2 col-md-2">平均用时：<span></span></div>
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


                </tbody>
            </table>

            <div class="pull-left">

            </div>
            <div class="pull-right">

            </div>

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