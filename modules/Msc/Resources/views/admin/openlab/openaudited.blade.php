@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .modal-dialog{
            margin: 300px auto;
        }
        #comment{
            margin-top: 10px;
            min-height: 150px;
        }
        .modal-footer{
            border-top: none;
        }
        .modal-footer button{
            margin-right: 15px;
        }
        .searchbox{
            margin-right: 20px;
        }
        .notice{
            color:#408aff;
        }
        #start{
            margin-left: 30px;
            width: 160px;
        }
        #lab-search{
            width: 260px;
        }
        .border-none{
            border: none!important;
            outline: none!important;
        }
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/js/all_checkbox.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script>
        $(function(){
            /*时间选择*/
            var start = {
                elem: "#start",
                format: "YYYY-MM-DD",
                min: "1970-00-00",
                max: "2099-06-16",
                istime: true,
                istoday: false,
                choose: function (a) {
                    end.min = a;
                    end.start = a
                }
            };
            laydate(start);
            $(".notice").click(function(){
                var $currentId=$(this).parent().parent().find(".open-id").text();
                $("#Form2").attr("openid",$currentId);
            })
            $("#sure-notice").click(function(){
                var str="";
                if($("#choose option:selected").text()=="自定义通知"){
                    str=$("#comment").val();
                }else{
                    str=$("#choose option:selected").text();
                }
                if(str==""){
                    $(this).attr("data-dismiss","");
                }else{
                    $(this).attr("data-dismiss","modal");
                    $.ajax({
                        url:"{{url('/msc/admin/lab/open-lab-urgent-notice')}}",
                        type:"post",
                        dataType:"json",
                        data:{
                            id:$("#Form2").attr("openid"),
                            reject:str
                        },
                        success: function(result) {
                            location.reload();
                        }
                    });
                }

            })
            //判断原因是否为自定义
            $("#choose").change(function(){
                if($("#choose").find("option:selected").text()=="自定义通知"){
                    $("#comment").removeAttr("disabled");
                }else{
                    $("#comment").val("");
                    $("#comment").attr("disabled","disabled");
                }
            })
        })
    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-5">
                <form method="get" action="open-lab-apply-examined-list">
                    <div class="input-group pull-left col-md-4 searchbox">
                        <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start" name="date">
                    </div>
                    <div class="input-group pull-left col-md-5 searchbox">
                        <input type="text" placeholder="实验室名称" class="input-sm form-control" name="keyword" value="" id="lab-search">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
                            </span>
                    </div>
                </form>
            </div>
        </div>
        <div class="container-fluid ibox-content">
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>开放实验室</th>
                    <th>
                        日期
                    </th>
                    <th>时间</th>
                    <th>
                        编号
                    </th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle border-none" type="button">预约人<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu order-classroom">
                                <li value="1">
                                    <a href="{{route('msc.admin.lab.openLabApplyExaminedList',['order_type'=>'asc','order_name'=>'1'])}}">升序</a>
                                </li>
                                <li value="-1">
                                    <a href="{{route('msc.admin.lab.openLabApplyExaminedList',['order_type'=>'desc','order_name'=>'1'])}}">降序</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>
                        预约理由
                    </th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle border-none" type="button">学生组<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu order-classroom">
                                <li value="1">
                                    <a href="{{route('msc.admin.lab.openLabApplyExaminedList',['order_type'=>'asc','order_name'=>'2'])}}">升序</a>
                                </li>
                                <li value="-1">
                                    <a href="{{route('msc.admin.lab.openLabApplyExaminedList',['order_type'=>'desc','order_name'=>'2'])}}">降序</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>
                        状态
                    </th>
                    <th>
                        操作
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($pagination as $item)
                <tr>
                    <td class="open-id">{{$item['id']}}</td>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['apply_date']}}</td>
                    <td>{{$item['begintime']}}-{{$item['endtime']}}</td>
                    <td>{{$item['code']}}</td>
                    <td>{{$item['student_name'] or  $item['teacher_name']}}</td>
                    <td>{{$item['detail']}}</td>
                    <td>{{$groupNames[$item->id]}}</td>
                    <td class="status">{{$statusValues[$item['status']]}}</td>
                    <td class="opera">
                        <span class="read notice modal-control" data-toggle="modal" data-target="#myModal" >紧急通知</span>
                    </td>
                </tr>
                @empty
                @endforelse
                </tbody>
            </table>
            <div class="pull-right">
                {!! $pagination->appends($_GET)->render() !!}
                
            </div>
        </div>
    </div>


@stop
@section('layer_content')
    <form class="form-horizontal" id="Form2" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">紧急通知</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">紧急通知</label>
                <div class="col-sm-9">
                    <select class="form-control" id="choose">
                        <option value="">自定义通知</option>
                        <option value="已损坏">已损坏</option>
                        <option value="已借出">已借出</option>
                    </select>
                    <textarea id="comment" name="comment" class="form-control" required="" aria-required="true"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id='sure-notice' class="notAgree" data-dismiss="modal" aria-hidden="true">确定</button>
        </div>
    </form>
    <script>
        $(function(){

        })
    </script>
@stop{{-- 内容主体区域 --}}