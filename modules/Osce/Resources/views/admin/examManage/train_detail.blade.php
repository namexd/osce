@extends('osce::admin.layouts.admin_index')
@section('only_css')
	<link rel="stylesheet" href="{{asset('osce/admin/plugins/css/plugins/webuploader/webuploader.css')}}">
    <link rel="stylesheet" href="{{asset('osce/admin/plugins/css/demo/webuploader-demo.css')}}">
    <style>
    .col-sm-1{margin-top: 6px;}
    .col-sm-1>input[type="checkbox"]{vertical-align: sub;}
    .form-group.col-sm-1{margin-bottom: 0!important;}
    .upload{
        display:block;
        height: 34px!important;
        width: 100px!important;
        cursor: pointer;
        background-image:none!important;
        position:relative;
        margin:0!important;
    }
    #file0{position:absolute;top:0;left:0;width:100px;height:34px;opacity:0;cursor:pointer;}
    .upload_list{padding-top:10px;line-height:1em;color:#4f9fcf;}
    .fa-remove{cursor:pointer;}
    .laydate-icon{width:200px;}
    .txt{padding-top:7px;line-height:1.5em}
    </style>
@stop


@section('only_js')
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_notice_add'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title" style="position: relative;">
            <h5>查看考前培训</h5>
        </div>
        <div class="ibox-content">
            <form method="post" id="form1" class="form-horizontal" action="#">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训名称:</label>
                        <div class="col-sm-10">
                        	<p class="form-control-static">{{ $data['name']  }}</p>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训地点:</label>
                        <div class="col-sm-10">
                        	<p class="form-control-static">{{ $data['address']  }}</p>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">开始时间:</label>
                        <div class="col-sm-10">
                        	<p class="form-control-static">{{ date('Y-m-d H:i',strtotime($data['begin_dt'])) }}</p>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">结束时间:</label>
                        <div class="col-sm-10">
                        	<p class="form-control-static">{{ date('Y-m-d H:i',strtotime($data['end_dt'])) }}</p>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">培训讲师:</label>
                        <div class="col-sm-10">
                        	<p class="form-control-static">{{ $data['teacher']  }}</p>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" >内容:</label>
                            <div class="txt col-sm-10">{!! $data['content'] !!}</div>
                        </div>
                    <div class="hr-line-dashed"></div>
                @if($data['attachments'])
                @foreach($data['attachments'] as $key=>$list)
                	<div class="form-group">
                        <label class="col-sm-2 control-label">附件:</label>
                        <div class="col-sm-10">
							<div class="upload_list upload_list_doc">
								<p>
									<a href="{{ route('osce.admin.getDownloadDocument',['id'=>$data['id'],'attch_index'=>$key])}}">
                                    <input type="hidden" name="file[]" id="" value="{{ $list }}" />
									<i class="fa fa-2x fa-delicious"></i>&nbsp;<?php $pathInfo=explode('/',$list)?>{{array_pop($pathInfo)}}
                                    </a>
								</p>
							</div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                @endforeach
                @endif
             	<div class="form-group">
                    <label class="col-sm-2 control-label" ></label>
                    <div class="txt col-sm-10"><a href="javascript:history.back(-1)" class="btn btn-default">&nbsp;返回&nbsp;</a></div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}