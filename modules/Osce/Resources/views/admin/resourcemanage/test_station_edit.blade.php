@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/plugins/js/plugins/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('osce/plugins/js/plugins/messages_zh.min.js')}}"></script>
    <script>

    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>编辑考站</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.Station.postEditStation')}}">
                            <input type="hidden" name="id" value="{{$rollmsg['id']}}">
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站名称</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name" name="name" value="{{$rollmsg['name']}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站类型</label>
                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="type" >
                                        @foreach($placeCate as $key=>$item)
                                            <option value="{{$key}}"
                                                @if($rollmsg['type'] == $key)
                                                    selected="selected"
                                                @endif>{{$item}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">时间限制</label>

                                <div class="col-sm-10">
                                    <input type="text"  required  ng-model="num" id="code" class="form-control" name="mins" value="{{$rollmsg['mins']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">关联摄像机</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="vcr_id">
                                        <option value="0">请选择</option>
                                        @foreach($vcr as $key=>$item)
                                            <option value="{{$item['id']}}"
                                                    @if($rollmsg['vcr_id'] == $item['id'])
                                                        selected="selected"
                                                    @endif
                                            >{{$item['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label" required>所属考场</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="room_id">
                                        <option value="0">请选择</option>
                                        @foreach($room as $key=>$item)
                                            <option value="{{$item['id']}}"
                                                @if($rollmsg['room_id'] == $item['id'])
                                                    selected="selected"
                                                @endif
                                            >{{$item['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">病例</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="case_id">
                                        <option value="0">请选择</option>
                                        @foreach($case as $key=>$item)
                                            <option value="{{$item['id']}}"
                                                @if($rollmsg['case_id'] == $item['id'])
                                                    selected="selected"
                                                @endif
                                            >{{$item['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">评分标准</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="subject_id">
                                        <option value="0">请选择</option>
                                        @foreach($subject as $key=>$item)
                                            <option value="{{$item['id']}}"
                                                @if($rollmsg['subject_id'] == $item['id'])
                                                    selected="selected"
                                                @endif
                                            >{{$item['title']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <input type="button" class="btn btn-white" value="取消">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop{{-- 内容主体区域 --}}