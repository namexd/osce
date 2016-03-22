@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <link href="{{asset('/osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
	<style type="text/css">
    	.select2-container--default .select2-selection--single{border:1px solid #e5e6e7;height:34px;line-height:34px;}
    	.select2-container--default .select2-selection--single .select2-selection__rendered{line-height:34px;}
    </style>
@stop


@section('only_js')
<script src="{{asset('osce/admin/resourceManage/resource_manage.js')}}" ></script>
@stop

@section('content')

<input type="hidden" id="parameter" value="{'pagename':'site_manage_edit','name': '{{route("osce.admin.room.postNameUnique")}}','id':'{{$data->id}}'}"  />
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>编辑</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.room.postEditRoom')}}">
                        <input type="hidden" name="id" value="{{$data->id}}">
                        <input type="hidden" name="type" value="{{$type}}">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>
                            <div class="col-sm-10">
                                <input type="text"  class="form-control" id="name" value="{{$data->name}}" name="name">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group" style="display:none">
                            <label class="col-sm-2 control-label">编号</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="num" id="code" name="code" value="{{$data->code}}" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">场所类别</label>

                            <div class="col-sm-10">
                                @if($type==='0')
                                <select class="form-control" name="cate" disabled="disabled">
                                        <option value="0" {{0==$type? 'selected="selected"':''}} >考场</option>
                                </select>
                                    @else
                                    <select class="form-control" name="cate">
                                        @forelse($cateList as $cate)
                                            <option value="{{$cate->cate}}"  {{$cate->cate==$type? 'selected="selected"':''}} >{{$cate->cate}}</option>
                                        @empty
                                        @endforelse
                                    @endif
                                    </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">关联摄像机</label>
                            <div class="col-sm-10">
                                <select name="vcr_id" id="vcr_id" class="form-control">
                                    @foreach($vcr as $key=>$item)
                                        <option value="{{$item->id}}" {{($data->vcr_id==$item->id)?'selected=selected':''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="description" id="description" class="form-control" name="description" value="{{$data->description}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">地址</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="address" id="address" class="form-control" name="address" value="{{$data->address}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">所在楼层</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="location" id="location" class="form-control" name="floor" value="{{$data->floor}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">房号</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="location" id="location" class="form-control" name="room_number" value="{{$data->room_number}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">可使用面积</label>
                            <div class="col-sm-10">
                                <input type="text" ng-model="location" id="location" class="form-control" name="proportion" value="{{$data->proportion}}">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
                                {{--<button class="btn btn-white" type="submit">取消</button>--}}
                            </div>
                        </div>


                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

@stop{{-- 内容主体区域 --}}
@section('footer_js')
    @parent
    <script src="{{asset('/osce/common/select2-4.0.0/js/select2.full.min.js')}}"></script>
    <script src="{{asset('/osce/common/select2-4.0.0/js/i18n/zh-CN.js')}}"></script>
@stop