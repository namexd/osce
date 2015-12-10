@extends('msc::admin.layouts.admin')
@section('only_css')

    <style type="text/css">
        .cancel{
            background-color: #fff;
            border: 1px solid #ccc;
        }
        .com-btn{
            background-color: #fff;
            border: 1px solid #ccc;
            color: #666;
            font-size: 13px;
        }
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
            <h5><a href="{{route('msc.courses.courses',['id'=>$data->id])}}">课程详情</a></h5>
        </div>
        <div class="ibox-content">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">课程名称</label>
                    <input type="hidden" name="id" value="{{$data->id}}" />
                    <div class="col-sm-10">

                        <p class="form-control-static">{{$data->course_id==0? '紧急约课':(is_null($data->course)? '-':$data->course->name)}}</p>

                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">教室</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{$classroom}}</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">时间</label>
                    <div class="col-sm-10">

                        <p class="form-control-static">{{$data->currentdate}}&nbsp;{{$data->begintime}}-{{$data->endtime}}</p>

                    </div>
                </div>
                <div class="hr-line-dashed"></div>

                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">小组</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{count($groups)==0? '-':implode(',',$groups)}}</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">老师</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{count($teachers)==0? '-':implode(',',$teachers)}}</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">联系电话</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{count($mobiles)==0? '-':implode(',',$mobiles)}}</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                @if($data->type==3)
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">使用原因</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">暂时找不到</p>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                @endif
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">课程资源</label>
                    <div class="col-sm-10">
                        <div class="btn-group" role="group" aria-label="...">
                            <button type="button" class="btn btn-default com-btn"><span class="glyphicon glyphicon-book" aria-hidden="true">课件习题</span></button>
                            <button type="button" class="btn btn-default com-btn"><span class="glyphicon glyphicon-film" aria-hidden="true">视频</span></button>
                            <button type="button" class="btn btn-default com-btn"><span class="glyphicon glyphicon-file" aria-hidden="true">评价报告</span></button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
@stop