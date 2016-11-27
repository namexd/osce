@extends('layouts.usermanage')

@section('only_css')

    <style>
        .clear_padding{
            padding: 0;
        }
        .clear_margin{
            margin: 0;
        }
        .border-bottom{
            border-bottom: none!important;
        }
        .btn-default{
            color: #9c9c9c;
        }
        .marb_none{margin-bottom: 0}
    </style>
@stop

@section('only_js')
    <script src="{{asset('msc/admin/labmanage/booking_examine.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'booking_examine_other'}"/>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs">
                        <li><a href="/msc/admin/laboratory/lab-order-list?type=1">待处理</a></li>
                        <li class="active"><a href="/msc/admin/laboratory/lab-order-list?type=2">已处理</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-2" class="tab-pane active">
                            <div class="wrapper wrapper-content animated fadeInRight">
                                <div class="row table-head-style1 ">
                                    <div class="col-xs-6 col-md-3">
                                        <form action="" method="get">
                                            <div class="input-group">
                                                <input type="text" id="keyword" name="keyword" placeholder="申请人" class="input-sm form-control" value="">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-sm btn-primary" id="search"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="ibox float-e-margins">
                                <div class="container-fluid ibox-content">
                                    <form action="" class="wait_handle" id="list_form">
                                        <table class="table table-striped" id="table-striped">
                                            <thead>
                                            <tr>
                                                <th>
                                                    <label class="check_label checkbox_input check_all marb_none">
                                                        <div class="check_real check_icon display_inline"></div>
                                                        <input type="hidden" name="" value="">
                                                    </label>
                                                </th>
                                                <th>序号</th>
                                                <th>实验室名称</th>
                                                <th>地址</th>
                                                <th>预约日期</th>
                                                <th>预约时段</th>
                                                <th>申请人</th>
                                                <th>申请时间</th>
                                                <th>操作</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if(!empty($LabOrderList))
                                                @foreach($LabOrderList as $k=>$list)
                                                    <tr>
                                                        <td>
                                                            <label class="check_label checkbox_input check_one marb_none">
                                                                <div class="check_real check_icon display_inline"></div>
                                                                <input type="hidden" name="" value="">
                                                            </label>
                                                        </td>
                                                        <td class="code">{{@$k+1}}</td>
                                                        <td class="name">{{@$list->labname}}</td>
                                                        <td class="status">{{@$list->address}}</td>
                                                        <td>
                                                            {{--@if(empty(@$list->begintime) && empty(@$list->endtime))--}}
                                                                {{--{{@$list->playyear}}--}}
                                                            {{--@else--}}
                                                                {{@$list->apply_time}}
                                                            {{--@endif--}}

                                                        </td>
                                                        @if(empty(@$list->begintime) && empty(@$list->endtime))
                                                            <td class="code">
                                                                {!!@$list->playdate!!}
                                                            </td>
                                                        @else
                                                            <td class="code">
                                                                <p>{{@$list->begintime}}-{{@$list->endtime}}</p>
                                                            </td>
                                                        @endif
                                                        <td class="name">{{@$list->name}}</td>
                                                        <td class="status">{{@$list->created_at}}</td>
                                                        <td class="opera">
                                                            @if($list->status == 1)
                                                                <a class="state1 pass" style="text-decoration: none" data-id="{{@$list->id}}"><span>通过</span></a>
                                                                <a class="state2 refuse" style="text-decoration: none" data-toggle="modal" data-target="#myModal" data-id="{{@$list->id}}"><span>不通过</span></a>
                                                            @else
                                                                @if($list->status == 2)
                                                                    <a class="state1" style="text-decoration: none" data-id="{{@$list->id}}"><span>已通过</span></a>
                                                                @elseif($list->status == 3)
                                                                    <a class="state2 refuse" style="text-decoration: none" data-toggle="modal"><span>未通过</span></a>
                                                                @endif
                                                            @endif
                                                            <a class="state1 detail" style="text-decoration: none" data-toggle="modal" data-target="#myModal" data-id="{{@$list->id}}"><span>详情</span></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--分页--}}
        <div class="btn-group pull-right">
            <?php echo $LabOrderList->appends(['type'=>$type])->render();?>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}

@section('layer_content')
    {{--详情       --}}
    <form class="form-horizontal" id="detail_from" novalidate="novalidate" action="" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">详情</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">实验室名称</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name labname" name="code" value="临床医学实验室" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">地址</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name address" name="code" value="临床教学楼3楼3-13" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">预约日期</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name ordertime" name="code" value="2015-12-31" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">预约时段</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name orderdate" name="code" value="8:00-10:00;10:00-12:00" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">教学课程</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name class" name="code" value="导尿术" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">学生人数</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name total" name="code" value="20" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">申请人</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name player" name="code" value="张三（学号）" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">申请原因</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name reason" name="code" value="课程需要" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">申请时间</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name applytime" name="code" value="2015-12-22" disabled="disabled"/>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary right" data-dismiss="modal">确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                </div>
            </div>
        </div>
    </form>
    {{--不通过--}}
    <form class="form-horizontal" id="refuse_from" novalidate="novalidate" action="" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">提示</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label">审核状态</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control name add-name" name="code" value="不通过" disabled="disabled"/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">不通过原因</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="reason"></textarea>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <div class="col-sm-4 col-sm-offset-2 right">
                    <button class="btn btn-primary sure_btn"  type="submit" >确&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;定</button>
                    <button class="btn btn-white2 right" type="button" data-dismiss="modal">取&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;消</button>
                </div>
            </div>
        </div>
    </form>
@stop
