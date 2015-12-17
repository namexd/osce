@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .allNums{
            margin: 20px 0;
        }
        select{
            border: none;
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
        .free{
            color: green;
        }
        .searchbox{
            margin-right: 20px;
        }
        /*ui细节调整 20151217 mao*/
        .btn-link{color: #a6b0c3!important;}
        .btn-link:hover{color: #fff!important;}
        input.input-sm.form-control {
            width: 260px;
            height: 30px;
        }
        input#start {
            width: 160px;
            height: 30px;
        }
        button.btn-white.border-white.dropdown-toggle {
            border: 0;
            font-weight: bolder;
        }
        .input-group.pull-left.col-md-5.searchbox {margin-left: -60px;}
        .col-xs-6.col-md-4 {margin-left: -20px;}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/js/all_checkbox.js')}}"></script>
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
    <script>
        $(function(){
            /*模态框内容选择*/
            $('.opera').on('click','.modal-control',function(){
                var num = ['no','yes'];
                if($(this).attr('flag')==num[0]){
                    $("#comment").val("");
                    $('#Form2').show();
                    $('#Form3').hide();
                }else{
                    $('#Form3').show();
                    $('#Form2').hide();
                }
            });
            //判断实验室状态，如果空闲则字体变绿
            function openlabStatus(){
                var $status=$(".status");
                for(var i=0;i<$status.length;i++){
                    if($($status[i]).text()=="空闲"){
                        $($status[i]).addClass("free");
                    }
                }
            }
            //页面加载执行
            openlabStatus();
            //判断不通过原因是否为自定义
               $("#choose").change(function(){
                   if($("#choose").find("option:selected").text()=="自定义原因"){
                       $("#comment").removeAttr("disabled");
                   }else{
                       $("#comment").val("");
                       $("#comment").attr("disabled","disabled");
                   }
               })
            //审核通过
            $(".state1").click(function(){
                var $currentId=$(this).parent().parent().find(".open-id").text();
                $("#Form3").attr("openid",$currentId);
            })
            $("#apply-yes").click(function(){
                $.ajax({
                    url:"{{url('/msc/admin/lab/change-open-lab-apply-status')}}",
                    type:"post",
                    dataType:"json",
                    data:{
                        id:$("#Form3").attr("openid"),
                        status:1
                    },
                    success: function(result) {
                       location.reload();
                    }
                });
            })
            //审核不通过
            $(".state2").click(function(){
                var $currentId=$(this).parent().parent().find(".open-id").text();
                $("#Form2").attr("openid",$currentId);
            })
            $("#apply-no").click(function(){
                var str="";
                if($("#choose option:selected").text()=="自定义原因"){
                    str=$("#comment").val();
                }else{
                    str=$("#choose option:selected").text();
                }
                console.log(str);
                $.ajax({
                    url:"{{url('/msc/admin/lab/change-open-lab-apply-status')}}",
                    type:"post",
                    dataType:"json",
                    data:{
                        id:$("#Form2").attr("openid"),
                        status:2,
                        reject:str
                    },
                    success: function(result) {
                        location.reload();
                    }
                });
            })
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
        })
    </script>
@stop

@section('content')

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row table-head-style1 ">
                <div class="head-opera col-xs-3 col-md-2">
                    <button type="button" class="btn btn-link btn-sm">批量通过</button>
                    <button type="button" class="btn btn-link btn-sm">批量不通过</button>
                </div>

                <div class="col-xs-6 col-md-4">
                    <form method="get" action="/msc/admin/lab/open-lab-apply-list">
                        <div class="input-group pull-left col-md-5 searchbox">
                            <input placeholder="开始日期" class="form-control layer-date laydate-icon" id="start" name="date" value="{{$rollmsg[0]}}">
                        </div>
                        <div class="input-group pull-left col-md-5 searchbox">
                            <input type="text" placeholder="实验室名称" class="input-sm form-control" name="keyword" value="{{$rollmsg[1]}}">
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
                        <th width="100">
                            <label class="check_label all_checked">
                                <div class="check_icon"></div>
                                <input  type="checkbox"  value="">
                            </label>
                        </th>
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
                                <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">预约人<span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu order-classroom">
                                    <li value="1">
                                        <a href="{{route('msc.admin.lab.openLabApplyList',['order_type'=>'asc', 'order_name'=>'1'])}}">升序</a>
                                    </li>
                                    <li value="-1">
                                        <a href="{{route('msc.admin.lab.openLabApplyList',['order_type'=>'desc','order_name'=>'1'])}}">降序</a>
                                    </li>
                                </ul>
                            </div>
                        </th>
                        <th>
                            预约理由
                        </th>
                        <th>
                            <div class="btn-group Examine">
                                <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">学生组<span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu order-classroom">
                                    <li value="1">
                                        <a href="{{route('msc.admin.lab.openLabApplyList',['order_type'=>'asc', 'order_name' => '2'])}}">升序</a>
                                    </li>
                                    <li value="-1">
                                        <a href="{{route('msc.admin.lab.openLabApplyList',['order_type'=>'desc', 'order_name' => '2'])}}">降序</a>
                                    </li>
                                </ul>
                            </div>
                        </th>
                        <th>
                            <div class="btn-group Examine">
                                <button data-toggle="dropdown" class="btn-white border-white dropdown-toggle" type="button">实验室状态<span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu order-classroom">
                                    <li value="1">
                                        <a href="javascript:void(0)">空闲</a>
                                    </li>
                                    <li value="-1">
                                        <a href="javascript:void(0)">忙碌</a>
                                    </li>
                                </ul>
                            </div>
                        </th>
                        <th>
                            操作
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($pagination as $item)
                        <tr>
                            <td>
                                <label class="check_label checkbox_input">
                                    <div class="check_icon"></div>
                                    <input type="checkbox" class="check_id" name="check_id[]" value="{{$item['id']}}" />
                                </label>
                            </td>
                            <td class="open-id">{{$item['id']}}</td>
                            <td>{{$item['name']}}</td>
                            <td>{{date('Y/m/d',strtotime($item['original_begin_datetime']))}}</td>
                            <td>{{date('H:i',strtotime($item['original_begin_datetime']))}}-{{date('H:i',strtotime($item['original_end_datetime']))}}</td>
                            <td>{{$item['code']}}</td>
                            <td>{{empty($item->applyer->name) ? '-':$item->applyer->name}}</td>
                            <td>{{$item['detail']}}</td>
                            <td>{{empty($item->labApplyGroups->first()->groups->name) ? '-' : $item->labApplyGroups->first()->groups->name}}</td>
                            {{--<td>{{dd(empty($item->labApplyGroups))}}</td>--}}
                            <td class="status">{{$item['status']}}</td>
                            <td class="opera">
                                <span class="read  state1 modal-control" data-toggle="modal" data-target="#myModal" flag="yes">审核通过</span>
                                <span class="Scrap state2 modal-control" data-toggle="modal" data-target="#myModal" flag="no">审核不通过</span>
                            </td>
                        </tr>
                    @empty

                    @endforelse
                    </tbody>
                </table>
                <div class="pull-left allNums">
                    已选择<span class="sum">0</span>条
                </div>
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
            <h4 class="modal-title" id="myModalLabel">审核不通过</h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <label class="col-sm-3 control-label">不通过原因</label>
                <div class="col-sm-9">
                    <select class="form-control" id="choose">
                        <option value="已损坏">已损坏</option>
                        <option value="已借出">已借出</option>
                        <option value="">自定义原因</option>
                    </select>
                    <textarea id="comment" name="comment" class="form-control" required="" aria-required="true" disabled="disabled"></textarea>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id='apply-no' class="notAgree" data-dismiss="modal" aria-hidden="true">确定</button>
        </div>
    </form>
    <!-- 审核通过 -->
    <form class="form-horizontal" id="Form3" novalidate="novalidate">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">审核通过</h4>
        </div>
        <div class="modal-body">
            确定通过该申请?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-success agree" id="apply-yes" data-dismiss="modal" aria-hidden="true">确&nbsp;定</button>
        </div>
    </form>

@stop{{-- 内容主体区域 --}}