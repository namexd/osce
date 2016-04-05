@extends('osce::admin.layouts.admin_index')

@section('only_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
    <style>
    button.btn.btn-white.dropdown-toggle {
        border: none;
        font-weight: bolder;
    }
    .blank-panel .panel-heading {margin-left: -20px;}
    input.laydate-icon{
        border: 0;
        background-position: right;
        background-image: none;
        padding-right: 27px;
        display: inline-block;
        width: 171px;
        line-height: 30px;
    }
    .form-group {
        margin: 15px;
        line-height: 30px;
    }
    .time-modify{
        margin-top: 25px!important;
        margin-bottom: 30px!important;
    }
    .panel-options .nav.nav-tabs{
        margin-left: 20px!important;
    }
    .msg-success{
        background-color: #ddd;
    }
    </style>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'exam_basic_info','background_img':'{{asset('osce/admin/plugins/js/plugins/layer/laydate')}}'}" />
<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">考试安排</h5>
            </div>
            <div class="col-xs-6 col-md-2" style="float: right;">
                
            </div>
        </div>
    <div class="container-fluid ibox-content">
        <div class="panel blank-panel">
            <div class="panel-heading">
                <div class="panel-options">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="{{route('osce.admin.exam.getEditExam',['id'=>$id])}}">基础信息</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getChooseExamArrange',['id'=>$id])}}">考场安排</a></li>
                        <li class=""><a href="">考官安排</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamineeManage',['id'=>$id])}}">考生管理</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getIntelligence',['id'=>$id])}}">智能排考</a></li>
                        <li class=""><a href="{{route('osce.admin.exam.getExamRemind',['id'=>$id])}}">待考区说明</a></li>
                    </ul>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="row">
                    <div class="col-md-12 ">
                        <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.exam.postEditExam')}}">
                            <input type="hidden" name="exam_id" value="{{$id}}">
                            <div class="form-group clearfix">
                                <label class="col-sm-2 control-label">考试名称</label>

                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name" name="name" value="{{$examData['name']}}" {{$examData['status']==0?'':'disabled'}}>
                                    <input type="hidden" required class="form-control" id="cate_id" name="cate_id" value="2" />
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试地点</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="address" name="address" value="{{$examData['address']}}" {{$examData['status']==0?'':'disabled'}}>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试顺序</label>
                                <div class="col-sm-10">
                                    <select class="form-control" style="width:200px;"  {{$examData['status']==0?'':'disabled'}} name="sequence_cate" >
                                        <option value="1" {{($examData['sequence_cate']==1)?'selected=selected':''}}>随机</option>
                                        <option value="3" {{($examData['sequence_cate']==3)?'selected=selected':''}}>轮循</option>
                                        <option value="2" {{($examData['sequence_cate']==2)?'selected=selected':''}}>顺序</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">排考方式</label>
                                <div class="col-sm-10">
                                    <select class="form-control" style="width:200px;" {{$examData['status']==0?'':'disabled'}} name="sequence_mode" v>
                                        <option value="1" {{($examData['sequence_mode']==1)?'selected=selected':''}}>以考场分组</option>
                                        <option value="2" {{($examData['sequence_mode']==2)?'selected=selected':''}}>以考站分组</option>
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                 <label class="col-sm-2 control-label">教官配置</label>
                                 <div class="col-sm-10">
                                      <select class="form-control" style="width:200px;" name="" disabled>
                                           <option value="1">按考站配置考官</option>
                                           <option value="2">按考场配置考官</option>
                                      </select>
                                 </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="clearfix form-group" style="margin-bottom: 0;">
                                             <div class="col-sm-12" id="checkbox_div">
                                                 <label class="check_label checkbox_input checkbox_one" style="height: 34px;line-height: 23px;margin-left: 12.1%;">
                                                      <div class="check_icon" style="display: inline-block;margin:5px 0 0 5px;float:left;" disabled></div>
                                                      <input type="checkbox" name="" value="1"disabled>
                                                      <span class="check_name" style="display: inline-block;float:left;">要求考生同时进出考站（考站的时间采用最长考站时间）</span>
                                                 </label>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试内容</label>
                                <div class="col-sm-10">
                                     <select class="form-control" style="width:200px;" name="" disabled>
                                          <option value="1">由考官指定</option>
                                          <option value="2">由系统指定</option>
                                     </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                 <div class="row">
                                     <div class="col-md-12">
                                         <div class="clearfix form-group" style="margin-bottom: 0;">
                                              <div class="col-sm-12" id="checkbox_div">
                                                   <label class="check_label checkbox_input col-sm-2 control-label checkbox_two" style="height: 34px;line-height: 28px;">
                                                        <div class="check_icon" style="display: inline-block;float:right;margin:5px 0 0 5px;"></div>
                                                        <input type="checkbox" name="" value="1">
                                                        <span class="check_name" style="display: inline-block;float:right;">考生分阶段考试</span>
                                                   </label>
                                                   <div class="col-sm-10">
                                                        <input type="text" required class="form-control" id="address" name="" style="float:left;width:200px;" disabled><span style="float:left;margin-left:5px;">阶段</span>
                                                   </div>
                                              </div>
                                         </div>
                                     </div>
                                 </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                 <label class="col-sm-2 control-label">实时发布成绩</label>
                                 <div class="col-sm-10">
                                      <select class="form-control" style="width:200px;" name="" disabled>
                                            <option value="1">是</option>
                                            <option value="2">否</option>
                                      </select>
                                 </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">考试时间</label>
                                <div class="col-sm-10">
                                <a  href="javascript:void(0)"  class="btn btn-primary" id="quick-add" style="float: right;display:none;">快速新增时间</a>
                                    <a  href="javascript:void(0)"  class="btn btn-primary" id="add-new" style="float: right;">新增时间</a>
                                    <table class="table table-bordered" id="add-basic">
                                        <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>开始时间</th>
                                            <th>结束时间</th>
                                            <th>时长</th>
                                            <th>阶段</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody index="{{count($examScreeningData)}}">
                                        @forelse($examScreeningData as $key => $item)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td class="laydate">
                                                    <input type="hidden" name="time[{{$key+1}}][id]" value="{{$item->id}}">
                                                    <input type="hidden" name="time[{{$key+1}}][exam_id]" value="{{$id}}">
                                                    <input type="text"  {{$examData['status']==0?'':'disabled'}} readonly="readonly" class="laydate-icon end" name="time[{{$key+1}}][begin_dt]" class="laydate-icon end" value="{{date('Y-m-d H:i',strtotime($item->begin_dt))}}">
                                                </td>
                                                <td class="laydate">
                                                    <input type="text" {{$examData['status']==0?'':'disabled'}} readonly="readonly" class="laydate-icon end" name="time[{{$key+1}}][end_dt]" class="laydate-icon end" value="{{date('Y-m-d H:i',strtotime($item->end_dt))}}">
                                                </td>
                                                <?php
                                                    $one = strtotime($item->begin_dt);  //开始时间 时间戳
                                                    $tow = strtotime($item->end_dt);    //结束时间 时间戳
                                                    $cle = $tow - $one;                 //得出时间戳差值
                                                    $d = floor($cle/3600/24);
                                                    $h = floor(($cle%(3600*24))/3600);  //%取余
                                                    $m = floor(($cle%(3600*24))%3600/60);
                                                ?>
                                                <td>{{$d}} 天 {{$h}}小时 {{$m}}分</td>
                                                <td>
                                                     <select class="form-control" name="">
                                                          <option value="1">阶段一</option>
                                                          <option value="2">阶段二</option>
                                                          <option value="2">阶段三</option>
                                                     </select>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0)"><span class="read  state2"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                        </tbody>
                                    </table>

                                    <div class="btn-group pull-right">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2 time-modify">
                                    <button id="save" class="btn btn-primary" type="submit">保存</button>
                                    <a class="btn btn-white" href="javascript:history.back(-1)" {{--href="{{route("osce.admin.exam.getExamList")}}"--}}>取消</a>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

<script src="{{asset('osce/admin/plugins/js/plugins/layer/laydate/laydate.js')}}"></script>
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>

<script>
    $(function(){
        @if(isset($_GET['succ']))
            layer.msg('保存成功！',{skin:'msg-success',icon:1});
        @endif
    })
</script>
@stop