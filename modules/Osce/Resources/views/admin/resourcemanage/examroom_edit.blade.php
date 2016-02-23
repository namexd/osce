@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <link href="{{asset('/osce/common/select2-4.0.0/css/select2.min.css')}}" rel="stylesheet">
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
                                message: '考场名称不能为空'
                            },
                            stringLength: {/*长度提示*/
                                min: 2,
                                max: 20,
                                message: '名称长度请在2到20之间'
                            },
                            threshold :  1 , //有6字符以上才发送ajax请求，（input中输入一个字符，插件会向服务器发送一次，设置限制，6字符以上才开始）
                            remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
                                url: '{{route('osce.admin.room.postNameUnique')}}',//验证地址
                                message: '考场名称已经存在',//提示消息
                                delay :  2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                                type: 'POST',//请求方式
                                /*自定义提交数据，默认值提交当前input value*/
                                data: function(validator) {
                                    $(".btn-primary").css({"background":"#16beb0","border":"1px solid #16beb0","color":"#fff","opacity":"1"});
                                    return {
                                        id: '{{$_GET['id']}}',
                                        name: $('[name="whateverNameAttributeInYourForm"]').val()
                                    }
                                }
                            }
                        }
                    },
                    description: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '功能描述不能为空'
                            },
                            stringLength: {/*长度提示*/
                                min: 0,
                                max: 50,
                                message: '描述长度请在0到50之间'
                            }
                        }
                    },
                    address: {
                        /*键名username和input name值对应*/
                        message: 'The username is not valid',
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '地址不能为空'
                            },
                            stringLength: {/*长度提示*/
                                min: 0,
                                max: 50,
                                message: '地址长度请在0到50之间'
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
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">场所类别</label>

                            <div class="col-sm-10">
                                <select class="form-control" name="cate">
                                    @if($type==='0')
                                        <option value="0" {{0==$type? 'selected="selected"':''}}>考场</option>
                                    @else
                                        @forelse($cateList as $cate)
                                            <option value="{{$cate->cate}}"  {{$cate->cate==$type? 'selected="selected"':''}} >{{$cate->cate}}</option>
                                        @empty
                                        @endforelse
                                    @endif
                                </select>
                            </div>
                        </div>
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
    <script>
        $(function(){
            $('[name=cate]').select2({
                tags: true,
                tokenSeparators: [',', ' '],
                maximumInputLength: 12
            }).change(function(){
                var val =   $(this).val();
                val     =   val.toString();
                var info=   val.split(',');
                var choose  =   info.pop();
                $(this).val([choose, choose]).trigger("change");
                $(this).select2("close");
            });
        })
    </script>
@stop