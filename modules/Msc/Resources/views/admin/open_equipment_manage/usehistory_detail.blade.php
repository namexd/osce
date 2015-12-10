@extends('msc::admin.layouts.admin')
@section('only_css')
    <style type="text/css">

    </style>
@stop

@section('only_js')

    <script>
        $(function(){

        })
    </script>
@stop

@section('content')
    <div>
        <div class="ibox-title">
            <h5>使用历史详情</h5>
        </div>
        <div class="ibox-content">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">开放设备名称</label>
                    <input type="hidden" name="id" value="" />
                    <div class="col-sm-10">

                        <p class="form-control-static"></p>

                    </div>
                </div>


                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">使用时段</label>
                    <div class="col-sm-10">

                        <p class="form-control-static"></p>

                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">是否为延时使用</label>
                    <div class="col-sm-10"></p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">编号</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"></p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">使用者</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"></p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">使用理由</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"></p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">地址</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"></p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">设备状态</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"></p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">是否复位设备</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop