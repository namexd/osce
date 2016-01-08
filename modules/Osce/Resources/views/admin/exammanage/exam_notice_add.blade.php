@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
    .col-sm-1{margin-top: 6px;}
    .col-sm-1>input[type="checkbox"]{vertical-align: sub;}
    .form-group.col-sm-1{margin-bottom: 0!important;}
    </style>
@stop

@section('only_js')
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.config.js')}}"></script>
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.all.min.js')}}"></script>
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/lang/zh-cn/zh-cn.js')}}"></script>
 <script src="{{asset('osce/admin/exammanage/js/exammanage.js')}}" ></script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_notice_add'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>新增通知</h5>
        </div>
        <div class="ibox-content">
            <form method="post" class="form-horizontal" action="{{route('osce.admin.notice.postAddNotice')}}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">考试:</label>
                        <div class="col-sm-10">
                            <select id="select_Category"   class="form-control" name="exam id">
                                @forelse($list as $exam)
                                <option value="{{$exam->id}}">{{$exam->name}}</option>
                                @empty
                                    <option value="">请创建考试</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">接收人:</label>
                        <div class="col-sm-10 select_code">
                            <div class="form-group col-sm-1">
                                <input type="checkbox" name="groups[]" value="1">
                                <label>考生</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input type="checkbox" name="groups[]" value="2">
                                <label>老师</label>
                            </div>
                            <div class="form-group col-sm-1">
                                <input type="checkbox" name="groups[]" value="3">
                                <label>sp老师</label>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">标题:</label>
                        <div class="col-sm-10">
                            <input type="text"  id="title" name="title" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" >内容:</label>
                        <div class="col-sm-10">
                            <script id="editor" type="text/plain" style="width:100%;height:500px;" name="content"></script>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">附件:</label>

                        <div class="col-sm-10">
                            <input type="text"  id="tell" name="tell" class="form-control">
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit">保存</button>
                            <button class="btn btn-white cancel" type="button">取消</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}