@extends('msc::admin.layouts.admin')
@section('only_css')
    <style>
        .layer-date{max-width: 100%!important;}
    </style>
@stop

@section('only_js')
  

@stop

@section('content')

<div class="wrapper wrapper-content animated fadeInRight detail">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h5>实验室详情</h5>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="col-md-12">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('msc.admin.resourcesManager.getAddResources')}}">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" id="name" value="{{$openLabDetail->name}}" disabled/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">类别</label>

                                <div class="col-sm-10">
                                    @if($openLabDetail['opened'] == 1)
                                        <input type="text"  id="detail" name="type" class="form-control" value="开放实验室(只能预约实验室)" disabled>
                                    @elseif($openLabDetail['opened'] == 2)
                                        <input type="text"  id="detail" name="type" class="form-control" value="开放实验室(只能预约设备)" disabled>
                                        @else
                                        <input type="text"  id="detail" name="type" class="form-control" value="普通实验室" disabled>
                                        @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">负责人</label>
                                <div class="col-sm-10">
                                    <input type="text"  id="manager_name" name="manager_name" class="form-control" value="{{$openLabDetail->manager_name}}" disabled>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" >负责人电话</label>
                                <div class="col-sm-10">
                                    <input type="text" id="manager_mobile" name="manager_mobile"  class="form-control"  value="{{$openLabDetail->manager_mobile}}" disabled>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>

                                <div class="col-sm-10">
                                    @if($openLabDetail['status'] == 1)
                                        <input type="text"  id="detail" name="type" class="form-control" value="正常" disabled>
                                    @elseif($openLabDetail['name'] == 2)
                                        <input type="text"  id="detail" name="type" class="form-control" value="已预约" disabled>
                                    @else
                                        <input type="text"  id="detail" name="type" class="form-control" value="不允许使用" disabled>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">地址</label>
                                <div class="col-sm-10">
                                    <input type="text" id="location" name="location" class="form-control"  value="{{$openLabDetail->location}}" disabled>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">开放开始时间</label>

                                <div class="col-sm-10">
                                    <input class="form-control layer-date laydate-icon" id="start" name="begintime" value="{{$openLabDetail->begintime}}" disabled>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">开放结束时间</label>
                                <div class="col-sm-10">
                                    <input  class="form-control layer-date laydate-icon" id="end" name="endtime" value="{{$openLabDetail->endtime}}" disabled>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">说明(功能描述)</label>

                                <div class="col-sm-10">
                                    <input type="text" name="detail" id="detail" class="form-control"  value="{{$openLabDetail->detail}}" disabled>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">最大预约人数</label>

                                <div class="col-sm-10">
                                    <input type="text" name="person_total" id="maxorder" class="form-control" value="{{$openLabDetail->person_total}}" disabled>
                                </div>

                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                
                                <div class="col-sm-4 col-sm-offset-2 right">
                                    <button class="btn btn-primary right" type="button" data-dismiss="modal">关&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;闭</button>
                                </div>
                            </div>

                    </form>
                </div>
            </div>

        </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}