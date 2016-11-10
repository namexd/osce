@extends('osce::admin.layouts.admin_index')
@section('only_css')
<style>
    .form-group {
        margin: 10px 0 30px;
        padding: 10px;
    }
    .col-sm-12 i{
        margin: 0 10px;
        font-size:16px;
        color: #ccc;
    }
    .perfect{
        color: #f8ac59!important;
    }
    .video{
        display: inline-block;
        height: 15px;
        width: 18px;
        background-size:18px 15px;
        background-image: url('{{asset("osce/images/iconfont-shipinliebiao.svg")}}');
    }

    .carousel-control.right,.carousel-control.left{background-image: none;}
    .carousel-caption {
        position: relative;
        right: 0;
        bottom:0;
        left: 0;
        padding-top: 10px;
        padding-bottom: 0;
        color: #fff;
        text-align: center;
         text-shadow: none;
    }
    table tbody tr td:last-child{width: initial!important;}
</style>
@stop

@section('only_js')
<script src="{{asset('osce/admin/plugins/js/plugins/echarts/echarts-all.js')}}"></script>
<script src="{{asset('osce/admin/examManage/exam_manage.js')}}" ></script>
@stop

@section('content')
<input type="hidden" id="parameter" value="{'pagename':'score_query_detail'}" />
<div class="wrapper wrapper-content animated fadeInRight">
<div style="display:none;">
    <ul id="standard">
        @foreach($standard as $item)
            <li value="{{$item}}"></li>
        @endforeach
    </ul>
    <ul id="avg">
        @foreach($avg as $item)
            <li value="{{$item}}"></li>
        @endforeach
    </ul>
</div>
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>考生成绩明细</h5>
            <a href="javascript:history.back(-1)" class="btn btn-outline btn-default" style="float:right;margin:-10px 10px 0 0;">返回</a>
            <a href="{{route('osce.admin.course.getResultVideo')}}?exam_id={{$result['student']->exam_id}}&student_id={{$result['student']->id}}&station_id={{$result['station_id']}}" class="btn btn-outline btn-default" style="float:right;margin:-10px 10px 0 0;">查看视频</a>
        </div>
        <div class="ibox-content">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b>考试</b></td>
                        <td colspan="3">{{$result['exam_name']}}</td>
                        <td><b>科目</b></td>
                        <td>{{$result['subject_title']}}</td>
                    </tr>
                    <tr>
                        <td><b>姓名</b></td>
                        <td id="student">{{$result['student']->name}}</td>
                        <td><b>学号</b></td>
                        <td>{{$result['student']->code}}</td>
                        <td><b>评价老师</b></td>
                        <td>{{$result['teacher']->name}}</td>
                    </tr>
                    <tr>
                        <td><b>答题开始时间</b></td>
                        <td>{{$result['begin_dt']}}</td>
                        <td><b>耗时</b></td>
                        <td>{{$result['time']}}</td>
                        <td><b>总成绩</b></td>
                        <td>{{$result['score']}}分</td>
                    </tr>
                    <tr>
                        <td><b>评价</b></td>
                        <td colspan="5">{{($result['evaluate']=='null'?'':$result['evaluate'])}}</td>
                    </tr>
                </tbody>
            </table>
            <div >
                <!-- 五星评价 -->
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <div class="col-sm-12">
                            <label class="control-label">操作的连贯性：</label>
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['operation']?'perfect':''}}"></i>
                            @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                            <label class="control-label">工作的娴熟度：</label>
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['skilled']?'perfect':''}}"></i>
                            @endfor
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group">
                            <div class="col-sm-12">
                            <label class="control-label">病人关怀情况：</label>
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['patient']?'perfect':''}}"></i>
                            @endfor
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                            <label class="control-label">沟通亲和力：</label>
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fa fa-star {{$i<$result['affinity']?'perfect':''}}"></i>
                            @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div id="score" style="height:340px;"></div>
                <!-- 统计图 -->
            </div>
            <table class="table table-striped" id="table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th width="200">考核内容</th>
                    <th>总分</th>
                    <th>成绩</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    @forelse($scores as $key => $value)
                        <tr>
                            <td>{{$value['sort']}}</td>
                            <td><div title="{{$value['content']}}" style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap; width:600px;">
                                    {{$value['content']}}
                                </div>
                            </td>
                            <td>{{$value['tScore']}}分</td>
                            <td>{{$value['score']}}分</td>
                            <td>
                                @if(count($value['image']) != 0)
                                <a href="javascript:void(0)">
                                    <span class="read  state1 detail"><i class="fa fa-picture-o fa-2x"></i></span>
                                    <ul class="img" style="display:none;">
                                        @foreach($value['image'] as $img)
                                            <li value="{{$img->url}}" download="{{route('osce.admin.getDownloadImage',array('id'=>$img->id))}}"></li>
                                        @endforeach
                                    </ul>
                                </a>
                                {{--@else--}}
                                    {{--<span class="read  state1 detail">未上传图片与音频</span>--}}
                                @endif
                            </td>
                        </tr>
                        @forelse($value['items'] as $k => $item)
                            <tr>
                                <td>{{$item['standard']->parent->sort.'-'.$item['standard']->sort}}</td>
                                <td><div title="{{$item['standard']->content}}" style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap; width:600px;">
                                        {{$item['standard']->content}}
                                    </div>
                                </td>
                                <td>{{$item['standard']->score}}分</td>
                                <td>{{$item['score']}}分</td>
                                <td>&nbsp;</td>
                                {{--<td>--}}
                                    {{--<a href="javascript:void(0)">--}}
                                        {{--<span class="read  state1 detail"><i class="fa fa-picture-o fa-2x"></i></span>--}}
                                        {{--<ul class="img" style="display:none;">--}}
                                            {{--@foreach($item['image'] as $k=>$img)--}}
                                                {{--<li value="{{$img->url}}" download="{{route('osce.admin.getDownloadImage',array('id'=>$img->id))}}"></li>--}}
                                            {{--@endforeach--}}
                                        {{--</ul>--}}
                                    {{--</a>--}}
                                {{--</td>--}}
                            </tr>
                        @empty
                        @endforelse
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop{{-- 内容主体区域 --}}