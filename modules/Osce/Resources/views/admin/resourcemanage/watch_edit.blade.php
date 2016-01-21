@extends('osce::admin.layouts.admin_index')
@section('only_css')

@stop

@section('only_js')
    <script src="{{asset('msc/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
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
                                message: '腕表名称不能为空'
                            }
                        }
                    },
                    code: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '设备ID不能为空'
                            },
                            regexp: {
                                regexp: /^\d+$/,
                                message: '请输入正确的设备ID'
                            }
                        }
                    },
                    factory: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '厂家不能为空'
                            }
                        }
                    },
                    sp: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '型号不能为空'
                            }
                        }
                    },
                    purchase_dt: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '采购日期不能为空'
                            }
                        }
                    }

                }
            });
            /*时间选择*/
            var start = {
                elem: "#purchase_dt",
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

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>编辑腕表</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.machine.postEditMachine')}}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备名称</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name" value="{{$item['name']}}">
                                    <input type="hidden"  class="form-control" id="cate_id" name="cate_id" value="3" />
                                    <input type="hidden"  class="form-control" id="id" name="id" value="{{$item['id']}}" />
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备ID</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="code" name="code" value="{{$item['code']}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">厂家</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="factory" name="factory" value="{{$item['factory']}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">型号</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="sp" name="sp" value="{{$item['sp']}}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">采购日期</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="purchase_dt" name="purchase_dt" value="{{$item['purchase_dt']}}">

                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10">
                                    <select id="status"   class="form-control m-b" name="status">
                                        @foreach($status as $key => $value)
                                            <option value="{{$key}}" {{($item['status']==$key)?'selected="selected"':''}}>{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">描述</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="description" name="description" value="{{$item['description']}}">
                                </div>
                            </div>

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