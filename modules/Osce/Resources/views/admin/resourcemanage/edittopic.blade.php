@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
    table tr td .form-group {
        margin-bottom: 0;
    }
    .btn-outline:hover{color: #fff!important;}
    .ibox-content{padding-top: 20px;}
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
            <h5>编辑评分标准</h5>
        </div>
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-12 ">
                    <form method="post" class="form-horizontal" id="sourceForm" action="{{route('osce.admin.topic.postEditTopic')}}">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">名称</label>

                            <div class="col-sm-10">
                                <input type="hidden" class="form-control" id="id" name="id" value="{{$item->id}}">
                                <input type="text" required class="form-control" id="title" name="title" value="{{$item->title}}">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">描述</label>
                            <div class="col-sm-10">
                                <input id="select_Category" required  class="form-control m-b" name="description" value="{{$item->description}}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <h5>评分标准：</h5>
                                        <div class="ibox-tools">
                                            <a  href="{{route('osce.admin.topic.getToppicTpl')}}" class="btn btn-outline btn-default" style="float: right;color:#333;">下载模板</a>
                                            <button type="button" class="btn btn-outline btn-default" id="add-new">新增考核点</button>
                                        </div>
                                    </div>
                                    <div class="ibox-content">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>序号</th>
                                                    <th>考核内容</th>
                                                    <th width="80">分数</th>
                                                    <th>操作</th>
                                                </tr>
                                            </thead>
                                            <tbody index="{{$prointNum}}">
                                            @forelse($list as $data)
                                                <tr class="pid-{{$data->pid==0? $data->sort:$data->parent->sort}}" current="{{$optionNum[$data->id] or 0}}" {{$data->pid==0? 'parent='.$data->sort.'':'child="'.$data->sort.'"'}}>
                                                    <td>{{$data->pid==0? $data->sort:$data->parent->sort.'-'.$data->sort}}</td>
                                                    <td>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label">{{$data->pid==0? '考核点:':'考核项:'}}</label>
                                                            <div class="col-sm-10">
                                                                <input id="select_Category"  class="form-control" name="{{$data->pid==0? 'content['.$data->sort.'][title]':'content['.$data->parent->sort.']['.$data->sort.']'}}" value="{{$data->content}}"/>
                                                            </div>
                                                        </div>
                                                        @if($data->pid!=0)
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label">评分标准:</label>
                                                            <div class="col-sm-10">
                                                                <input id="select_Category" class="form-control" name="description[{{$data->parent->sort}}][{{$data->sort}}]" value="{{$data->answer}}">
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <select class="form-control" name="{{$data->pid==0? 'score['.$data->sort.'][total]':'score['.$data->parent->sort.']['.$data->sort.']'}}">
                                                            <option value="1" {{$data->score==1? 'selected="selected"':''}}>1</option>
                                                            <option value="2" {{$data->score==2? 'selected="selected"':''}}>2</option>
                                                            <option value="3" {{$data->score==3? 'selected="selected"':''}}>3</option>
                                                            <option value="4" {{$data->score==4? 'selected="selected"':''}}>4</option>
                                                            <option value="5" {{$data->score==5? 'selected="selected"':''}}>5</option>
                                                            <option value="6" {{$data->score==6? 'selected="selected"':''}}>6</option>
                                                            <option value="7" {{$data->score==7? 'selected="selected"':''}}>7</option>
                                                        </select>
                                                    </td>
                                                    @if($data->pid==0)
                                                    <td>
                                                        <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                        <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-plus fa-2x"></i></span></a>
                                                    </td>
                                                    @else
                                                    <td>
                                                        <a href="javascript:void(0)"><span class="read  state1 detail"><i class="fa fa-trash-o fa-2x"></i></span></a>
                                                        <a href="javascript:void(0)"><span class="read state11 detail"><i class="fa fa-arrow-up fa-2x"></i></span></a>
                                                        <a href="javascript:void(0)"><span class="read state11 detail"><i class="fa fa-arrow-down fa-2x"></i></span></a>
                                                    </td>
                                                    @endif
                                                </tr>
                                            @empty
                                            @endforelse
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
<script>
    
</script>
@stop{{-- 内容主体区域 --}}