@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('osce/plugins/js/plugins/validate/jquery.validate.min.js')}}"></script>
    <script src="{{asset('osce/plugins/js/plugins/messages_zh.min.js')}}"></script>
    <script>
        $(function(){
            $('#type').change(function(){
                var choose  =   $(this).find(':selected').val();
                if(choose=='3')
                {
                    $('.noTheory').hide();
                }
                else
                {
                    $('.noTheory').show();
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

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站名称</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name" name="name">
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

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站描述</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="description" name="description">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考站编号</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="code" name="code">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">时间限制</label>

                                <div class="col-sm-10">
                                    <input type="text"  required  ng-model="num" id="code" class="form-control" name="mins" value="{{$time}}">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label">关联摄像机</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="vcr_id">
                                        @foreach($vcr as $key=>$item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div><div class="form-group">
                                <label class="col-sm-2 control-label" required>所属考场</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="room_id">
                                        @foreach($room as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed noTheory"></div>
                            <div class="form-group noTheory">
                                <label class="col-sm-2 control-label">病例</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="case_id">
                                        @foreach($case as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed noTheory"></div>
                            <div class="form-group noTheory">
                                <label class="col-sm-2 control-label">评分标准</label>

                                <div class="col-sm-10">
                                    <select id="" required  class="form-control m-b" name="subject_id">
                                        @foreach($subject as $item)
                                            <option value="{{$item->id}}">{{$item->title}}</option>
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