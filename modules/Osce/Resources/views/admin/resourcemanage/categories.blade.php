@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
    table tr td .form-group {
        margin-bottom: 0;
    }
    td input{margin: 5px 0;}
</style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/resourcemanage/js/resourcemanage.js')}}" ></script> 
<script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
@stop

@section('content')
    <input type="hidden" id="parameter" value="{'pagename':'categories','excel':'{{route('osce.admin.topic.postImportExcel')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增评分标准</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.topic.postAddTopic')}}">

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>

                            <div class="col-sm-10">
                                <input type="text" required class="form-control" id="name" name="title">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input id="select_Category" required  class="form-control m-b" name="description"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>评分标准：</h5>
                                        <div class="ibox-tools">
                                            <a href="javascript:void(0)" class="btn btn-outline btn-default" id="file1" style="height:34px;padding:5px;width:184px;">
                                                <input type="file" name="topic" id="file0" multiple="multiple" />
                                            </a>
                                            
                                            <button type="button" class="btn btn-outline btn-default" id="add-new">新增考核点</button>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>序号</th>
                                                    <th>考核内容</th>
                                                    <th width="120">分数</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody index="0">

                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                        <div class="hr-line-dashed"></div>


                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">保存</button>
                                <button class="btn btn-white" type="submit">取消</button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

</div>
@stop{{-- 内容主体区域 --}}