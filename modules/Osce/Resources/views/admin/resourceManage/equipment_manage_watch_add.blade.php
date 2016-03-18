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
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.machine.postNameUnique')}}',//验证地址
                                message: '腕表名称已经存在',//提示消息
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                /*自定义提交数据，默认值提交当前input value*/
                                data: function(validator) {
                                    $(".btn-primary").css({"background":"#16beb0","border":"1px solid #16beb0","color":"#fff","opacity":"1"});

                                    return {
                                        cate: '3',
                                        name: $('[name="whateverNameAttributeInYourForm"]').val()
                                    }
                                }
                            },
                            stringLength: {
                                max:20,
                                message: '名称字数不超过20个'
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
                                regexp: /^[a-zA-Z0-9]+$/,
                                message: '请输入正确的设备ID'
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.machine.postNameUnique')}}',//验证地址
                                message: '设备ID已经存在',//提示消息
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                /*自定义提交数据，默认值提交当前input value*/
                                data: function(validator) {
                                    return {
                                        cate: '3',
                                        name: $('[name="whateverNameAttributeInYourForm"]').val()
                                    }
                                }
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
                    place: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '放置地点不能为空'
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
                max: "2099-06-16"
            };
            laydate(start);
        })
    </script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>新增腕表</h5>
            </div>
            <div class="ibox-content">
                <div class="row">

                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.machine.postAddMachine')}}">

                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备名称</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="name" name="name">
                                    <input type="hidden"  class="form-control" id="cate_id" name="cate_id" value="3" />
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">设备ID</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="code" name="code">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">感应ID</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="nfc_code" name="nfc_code">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">厂家</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="factory" name="factory">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">型号</label>
                                <div class="col-sm-10">
                                    <input type="text"  class="form-control" id="sp" name="sp">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">放置地点</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="place" name="place">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">采购日期</label>
                                <div class="col-sm-10">
                                    <input type="text" class="laydate-icon" id="purchase_dt" name="purchase_dt" readonly="readonly">
                                </div>
                            </div>



                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">状态</label>
                                <div class="col-sm-10">
                                    <select id=""  class="form-control m-b" name="status">
                                        <option value="0">正常</option>
                                        @foreach($status as $key => $value)
                                            @if($key >1)
                                                <option value="{{$key}}">{{$value}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">描述</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="description" name="description">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="{{route("osce.admin.machine.getMachineList",["cate_id"=>3])}}">取消</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop{{-- 内容主体区域 --}}