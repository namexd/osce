@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .allNums{
            margin: 20px 0;
        }

        .modal-dialog{
            margin: 300px auto;
        }
        #comment{
            margin-top: 10px;
            min-height: 150px;
        }
        .modal-footer{
            border-top: none;
            text-align: center;
        }
        .searchbox{
            margin-right: 20px;
        }
        .none-border{
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
                istime: false,
                istoday: false,
                choose: function (a) {
                    end.min = a;
                    end.start = a
                }
            };
            laydate(start);
            //紧急通知
            $(".state1").click(function(){
                var $currentId=$(this).parent().parent().find(".open-id").text();
                $("#Form2").attr("noticeid",$currentId);
            })
            //判断不通过原因是否为自定义
            $("#choose").change(function(){
                if($("#choose").find("option:selected").text()=="自定义通知"){
                    $("#comment").removeAttr("disabled");
                }else{
                    $("#comment").val("");
                    $("#comment").attr("disabled","disabled");
                }
            })
            //紧急通知ajax
            $("#sure-notice").click(function(){
                var str="";
                if($("#choose option:selected").text()=="自定义通知"){
                    str=$("#comment").val();
                }else{
                    str=$("#choose option:selected").text();
                }
                $.ajax({
                    url:"{{route('msc.lab-tools.OpenLabToolsUrgentNotice')}}",
                    type:"post",
                    dataType:"json",
                    data:{
                        id:$("#Form2").attr("noticeid"),
                        reject:str
                    },
                    success: function(result) {
                        location.reload();
                    }
                });
            })
            //根据设备不同状态变换字体颜色
            $(".status").each(function(){
                if($(this).text()=="已损坏"){
                    $(this).css("color","#ed5565");
                }else if($(this).text()=="完好"){
                    $(this).css("color","#408aff");
                }
            })
        })
    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-4">
                <form method="get" action="{{route('msc.admin.lab-tools.openLabToolsExaminedList')}}">
                    <div class="input-group pull-left col-md-5 searchbox">
                        <input placeholder="{{$rollmsg['0']}}" class="form-control layer-date laydate-icon" id="start" name="date">
                    </div>
                    <div class="input-group pull-left col-md-5 searchbox">
                        @if($rollmsg['1']=="")
                            <input type="text" placeholder="请输入实验室名称" class="input-sm form-control" name="keyword" value="">
                        @else
                            <input type="text" placeholder="{{$rollmsg['1']}}" class="input-sm form-control" name="keyword" value="">
                        @endif

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
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle none-border">开放设备<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu order-classroom">
                                <li value="1">
                                    <a href="{{route('msc.admin.lab-tools.openLabToolsExaminedList',['order_type'=>'asc','order_name'=>'1'])}}">升序</a>
                                </li>
                                <li value="-1">
                                    <a href="{{route('msc.admin.lab-tools.openLabToolsExaminedList',['order_type'=>'desc','order_name'=>'1'])}}">降序</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th>
                        日期
                    </th>
                    <th>时间</th>
                    <th>
                        编号
                    </th>
                    <th>
                        <div class="btn-group Examine">
                            <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle none-border">预约人<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu order-classroom">
                                <li value="1">
                                    <a href="{{route('msc.admin.lab-tools.openLabToolsExaminedList',['order_type'=>'asc','order_name'=>'2'])}}">升序</a>
                                </li>
                                <li value="-1">
                                    <a href="{{route('msc.admin.lab-tools.openLabToolsExaminedList',['order_type'=>'desc','order_name'=>'2'])}}">降序</a>
                                </li>
                            </ul>
                        </div>
                    </th>
                    <th class="toowidth">
                        预约理由
                    </th>
                    <th>
                        设备状态
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
                        <td>{{date('Y/m/d',strtotime($item['original_begin_datetime']))}}</td>
                        <td>{{date('H:i',strtotime($item['original_begin_datetime']))}}-{{date('H:i',strtotime($item['original_end_datetime']))}}</td>
                        <td>{{$item['code']}}</td>
                        <td>{{ $item['student_name'] }}</td>
                        <td>{{$item['detail']}}</td>
                        <td class="status">
                            @if($item['status'] === -2) 已损坏
                            @elseif($item['status'] === 0) 不允许借出
                            @elseif($item['status'] === 1) 正常
                            @elseif($item['status'] === 2) 已借出
                            @endif
                        </td>
                        <td class="opera">
                            <span class="read  state1 modal-control" data-toggle="modal" data-target="#myModal" >紧急通知</span>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
            <div class="pull-right">
                {!! $pagination->render() !!}
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