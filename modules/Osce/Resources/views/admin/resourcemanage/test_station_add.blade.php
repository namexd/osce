@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script>
        $(function(){
            $('#sourceForm').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    name: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '名称不能为空'
                            }
                        }
                    },
                    mins: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '时间限制不能为空'
                            },
                            regexp: {
                                regexp: /^\d+$/,
                                message: '请输入正确的时间'
                            }
                        }
                    }
                }
            });
        })
    </script>
@stop

@section('content')
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
                                    <select id="type" required  class="form-control m-b" name="type">
                                        @foreach($placeCate as $key=>$item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{--<div class="form-group">--}}
                                {{--<label class="col-sm-2 control-label">考站描述</label>--}}

                                {{--<div class="col-sm-10">--}}
                                    {{--<input type="text" required class="form-control" id="description" name="description">--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            {{--<div class="hr-line-dashed"></div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label class="col-sm-2 control-label">考站编号</label>--}}

                                {{--<div class="col-sm-10">--}}
                                    {{--<input type="text" required class="form-control" id="code" name="code">--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">时间限制</label>
                                <div class="col-sm-10">
                                    <input type="text"   ng-model="num" id="code" class="form-control" name="mins" value="{{$time}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">考试科目</label>

                                <div class="col-sm-10">
                                    <select id=""  class="form-control m-b" name="subject_id">
                                        @foreach($subject as $key=>$item)
                                            <option value="{{$item->id}}">{{$item->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="hr-line-dashed noTheory"></div>
                            <div class="form-group noTheory">
                                <label class="col-sm-2 control-label">病例</label>

                                <div class="col-sm-10">
                                    <select id=""  class="form-control m-b" name="case_id">
                                        @foreach($case as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">所属考场</label>

                                <div class="col-sm-10">
                                    <select id=""  class="form-control m-b" name="room_id">
                                        @foreach($room as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">关联摄像机</label>

                                <div class="col-sm-10">
                                    <select id=""  class="form-control m-b" name="vcr_id">
                                        @foreach($vcr as $key=>$item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="hr-line-dashed noTheory"></div>
                            {{--<div class="form-group noTheory">--}}
                                {{--<label class="col-sm-2 control-label">评分标准</label>--}}

                                {{--<div class="col-sm-10">--}}
                                    {{--<select id="" required  class="form-control m-b" name="subject_id">--}}
                                        {{--@foreach($subject as $item)--}}
                                            {{--<option value="{{$item->id}}">{{$item->title}}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                {{--</div>--}}

                            {{--</div>--}}
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="javascript:history.go(-1);">取消</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop{{-- 内容主体区域 --}}