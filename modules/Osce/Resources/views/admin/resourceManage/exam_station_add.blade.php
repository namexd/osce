@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
    .paper-id{display: none;}
</style>
@stop

@section('only_js')
    <script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop


@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'exam_station_add','name':'{{route('osce.admin.station.postNameUnique')}}'}" />
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增考站</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.Station.postAddStation')}}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站名称</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站类型</label>
                                <div class="col-sm-10">
                                    <select id="type"  class="form-control" name="type">
                                        @foreach($placeCate as $key=>$item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed paper-id"></div>
                            <div class="form-group paper-id">
                                <label class="col-sm-2 control-label">考卷</label>
                                <div class="col-sm-10">
                                    <select  class="form-control" name="paper_id">
                                        @foreach($papers as $paper)
                                            <option value="{{$paper->id}}">{{$paper->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="form-group">--}}
                                {{--<label class="col-sm-2 control-label">时间限制(分钟)</label>--}}
                                {{--<div class="col-sm-10">--}}
                                    {{--<input type="text"   ng-model="num" id="code" class="form-control" name="mins" value="{{$time}}" placeholder="请输入分钟数">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="hr-line-dashed sub-id"></div>--}}

                            <div class="form-group sub-id">
                                <label class="col-sm-2 control-label">考试项目</label>
                                <div class="col-sm-10">
                                    <select id="subject_id"  class="form-control" name="subject_id">
                                        @foreach($subject as $key=>$item)
                                            <option value="{{$item->id}}">{{$item->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            {{--<div class="hr-line-dashed noTheory sub-id"></div>--}}

                            {{--<div class="form-group noTheory sub-id">--}}
                                {{--<label class="col-sm-2 control-label">病例</label>--}}
                                {{--<div class="col-sm-10">--}}
                                    {{--<select id=""  class="form-control" name="case_id">--}}
                                        {{--@foreach($case as $item)--}}
                                            {{--<option value="{{$item->id}}">{{$item->name}}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="hr-line-dashed"></div>--}}

                            <div class="form-group">
                                <label class="col-sm-2 control-label">所属考场</label>
                                <div class="col-sm-10">
                                    <select id=""  class="form-control" name="room_id">
                                        <option value="">请选择</option>
                                        @foreach($room as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">关联摄像机</label>
                                <div class="col-sm-10">
                                    <select id=""  class="form-control" name="vcr_id">
                                        <option value="0">请选择</option>
                                        @foreach($vcr as $key=>$item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed noTheory"></div>

                            {{--<div class="form-group">--}}
                                {{--<label class="col-sm-2 control-label">考站编号</label>--}}
                                {{--<div class="col-sm-10">--}}
                                    {{--<input type="text" required class="form-control" id="code" name="code">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="hr-line-dashed"></div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="col-sm-2 control-label">考站描述</label>--}}
                                {{--<div class="col-sm-10">--}}
                                    {{--<input type="text" required class="form-control" id="description" name="description">--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            {{--<div class="hr-line-dashed"></div>--}}

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" id="cancel-btn" href="javascript:void(0)">取消</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
$(function() {
    /**
     * 弹出新增
     * @author mao
     * @version 3.4
     * @date    2016-04-20
     */
    var type_status = location.href.split('?')[1];
    $('#cancel-btn').click(function() {
        if(type_status != undefined){
            parent.layer.close(parent.layer.getFrameIndex(window.name));
        } else {
            location.href = '{{route("osce.admin.Station.getStationList")}}';
        }
    });
})
</script>
@stop{{-- 内容主体区域 --}}