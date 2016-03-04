@extends('osce::admin.layouts.admin_index')
@section('only_css')
    <style>
    .col-sm-1{margin-top: 6px;}
    .check_label{top: 8px;}
    .check_icon.check,.check_icon{vertical-align: middle;}
    .form-group.col-sm-1{margin-bottom: 0!important;}
    .images_upload {
        display: inline-block;
        height: 34px!important;
        width: 70px!important;
        border: 1px dashed #ccc;
        cursor: pointer;
        background-image:none!important;
        /*background: url(../images/add_img.png) no-repeat center; */
    }
    input#file0 {
        position: relative;
        top: -27px;
        left: -5px;
        height: 34px;
        width: 70px;
        opacity: 0;
    }

    /*文件上传*/
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
    .file-msg{
        position: relative;
        top: -26px;
        left: 109px;
        color: #42b2b1;
    }
    .upload_list{padding-top:10px;line-height:1em;color:#4f9fcf;}
    .fa-remove{cursor:pointer;}
    .check_label + i.form-control-feedback.glyphicon.glyphicon-ok {top: -2px;}
    .check_label + i.form-control-feedback.glyphicon.glyphicon-remove {top: -2px;}
    .checkbox_input{margin:0 10px 0 0;font-weight:100;cursor:pointer;}
    </style>
@stop

@section('only_js')
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.config.js')}}"></script>
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/ueditor.all.min.js')}}"></script>
 <script src="{{asset('osce/admin/plugins/js/plugins/UEditor/lang/zh-cn/zh-cn.js')}}"></script>
 <script src="{{asset('osce/wechat/common/js/ajaxupload.js')}}"></script>
 <script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
 <script>
     function asd(){
         var reg=document.getElementsByName("reg");
         if(reg[0].checked){
             document.getElementById("submit").disabled=false;
         }
         else {
             document.getElementById("submit").disabled=true;

         }
     }
 </script>



@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_notice_edit','url':'{{route('osce.api.communal-api.postAttchUpload')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>编辑通知</h5>
        </div>
        <div class="ibox-content">
            <form id="sourceForm" method="post" class="form-horizontal" action="">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">考试:</label>
                        <div class="col-sm-10">
                            <select id="select_Category"   class="form-control" name="exam_id" disabled>
                                @forelse($list as $exam)
                                    <option value="{{$exam->id}}" {{$exam->id==$item->exam_id? 'selected="selected"':''}}>{{$exam->name}}</option>
                                @empty
                                    <option value="">请创建考试</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group" >
                        <label class="col-sm-2 control-label">接收人:</label>
                        <div class="col-sm-10 select_code" id="checkbox_div">
                            <label class="check_label checkbox_input" >
                                <div class="check_icon {{in_array(1,explode(',',$item->accept))? 'check':''}}" style="display: inline-block"></div>
                                <input  name="reg" type="checkbox" name="accept[]" value="1" {{in_array(1,explode(',',$item->accept))? 'checked="checked"':''}} disabled >
                                <span class="check_name">考生</span>
                            </label>
                            <label class="check_label checkbox_input">
                                <div class="check_icon {{in_array(2,explode(',',$item->accept))? 'check':''}}" style="display: inline-block"></div>
                                <input type="checkbox" name="accept[]" value="2" {{in_array(2,explode(',',$item->accept))? 'checked="checked"':''}} disabled >
                                <span class="check_name">老师</span>
                            </label>
                            <label class="check_label checkbox_input">
                                <div class="check_icon {{in_array(3,explode(',',$item->accept))? 'check':''}}" style="display: inline-block"></div>
                                <input type="checkbox" name="accept[]" value="3" {{in_array(3,explode(',',$item->accept))? 'checked="checked"':''}} disabled >
                                <span class="check_name">sp老师</span>
                            </label>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">标题:</label>
                        <div class="col-sm-10" >
                            <input type="text"  id="examinee_id" name="name" value="{{$item->name}}" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" >内容:</label>
                        <div class="col-sm-10">
                            <div id="content" style="display: none;" >
                                {!! $item->content !!}
                            </div>
                            <script id="editor" type="text/plain" style="width:100%;height:500px;" name="content"></script>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">附件:</label>
                        <div class="col-sm-10">
                            <span class="images_uploads upload btn btn-white" >上传附件
                                <input type="file"  name="attchment" id="file0"/>
                            </span>
                            <span class="file-msg">(文件大小不得超过2M!)</span>
                            <div class="upload_list upload_list_doc">
                                @forelse(explode(',',$item->attachments) as $attachment)
                                    @if($attachment!='')
                                <p><input type="hidden" name="attach[]" id="" value="{{$attachment}}" /><?php $pathInfo=explode('/',$attachment)?>{{array_pop($pathInfo)}}&nbsp;<i class="fa fa-2x fa-remove clo6"></i></p>
                                    @endif
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" disabled>保存</button>
                            <a class="btn btn-white cancel" href="javascript:history.back(-1)">取消</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}