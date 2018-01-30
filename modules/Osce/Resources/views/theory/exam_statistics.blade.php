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

    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>成绩统计</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试总得分</label>
                                <div class="col-sm-10">
                                    <input type="text" disabled  class="form-control" id="name" value="{{$data['total_score']}}" name="total_score">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试平均分</label>
                                <div class="col-sm-10">
                                    <input type="text" disabled ng-model="description" id="description" class="form-control" name="avg_score" value="{{$data['avg_score']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">最高分</label>
                                <div class="col-sm-10">
                                    <input type="text" disabled ng-model="address" id="address" class="form-control" name="student_score_max" value="{{$data['student_score_max']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">最低分</label>
                                <div class="col-sm-10">
                                    <input type="text" disabled ng-model="location" id="location" class="form-control" name="student_score_min" value="{{$data['student_score_min']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">合格率</label>
                                <div class="col-sm-10">
                                    <input type="text" disabled ng-model="location" id="location" class="form-control" name="pass_percent" value="{{$data['pass_percent']}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="javascript:history.go(-1);">返回</a>
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