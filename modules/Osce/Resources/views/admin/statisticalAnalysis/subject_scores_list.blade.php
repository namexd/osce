@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
        button.btn.btn-white.dropdown-toggle {
            border: none;
            font-weight: bolder;
        }
        .blank-panel .panel-heading {margin-left: -20px;}
        #start,#end{width: 160px;}
        .exam-name{
            line-height: 34px;
            margin-right: 20px;
        }
        .exam-list{
            width: 70%;
        }
        .examinee-list{
            width: 80%;
        }
    </style>
@stop

@section('only_js')
<script>
$(function(){
    $('#exam-id').change(function(){
        var examId = $(this).val();
        $.ajax({
            type:'get',
            url:'{{route("osce.admin.course.getSubject")}}',
            data:{exam_id:examId},
            success:function(res){
                if(res.code!=1){
                    layer.alert(res.message);
                }else{
                    var data = res.data;
                    var html = '<option value="">全部考试项目</option>';
                    for(var i in data){
                        var sign = true;
                        for (var k in data[+i]){
                            if (k == 'station_id' ){
                                sign = false;
                                break;
                            }
                        }
                        if(sign){
                            html += '<option value="'+data[+i].id+'" id="'+data[+i].id+'_sid">'+data[+i].name+'</option>';
                        }else{
                            html += '<option value="'+data[+i].id+'">'+data[+i].name+'</option>';
                        }
                    }

                    $('#subject-id').html(html);
                }
            },
            error:function(res){
                layer.alert('通讯失败！')
            }
        });
    });
    $('#subject-id').change(function(){
        var sub = $("#subject-id :selected").attr('id');
//        alert(sub);
        if(sub){
            $('input[name="sign"]').val('subject');

        }else {
            $('input[name="sign"]').val('paper');
        }
    });
})
</script>
@stop

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row table-head-style1 ">
            <div class="col-xs-6 col-md-2">
                <h5 class="title-label">科目成绩统计</h5>
            </div>
        </div>
        <div class="panel blank-panel">
            <div class="container-fluid ibox-content">
                <form action="">
                    <div  class="row" style="margin:20px 0;">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <label class="pull-left exam-name">考试:</label>
                            <div class="pull-left exam-list">
                                <select name="exam_id" id="exam-id" class="form-control" style="width: 250px;height: 36px;">
                                    @forelse($examDownlist as $exam)
                                        <option value="{{$exam->id}}" {{$exam_id==$exam->id?'selected':''}}>{{$exam->name}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="input-group col-md-6 col-sm-6 col-xs-6">
                            <label class="pull-left subject-name">考试项目:</label>
                            <div  class="pull-left subject-list">
                                <select name="subject_id" id="subject-id" class="form-control" style="width: 250px;">
                                    <option value="">全部考试项目</option>

                                    @forelse($subjectDownlist as $value)
                                        <option value="{{$value['id']}}" {{array_key_exists('subject_id', $value)? 'id="'.$value['id'].'_sid"':''}} @if($subject_id==$value['id'])selected="selected" @endif>{{$value['name']}}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                            <input type="hidden" name="sign" value="">
                            <span class="input-group-btn pull-left" style="margin-left: 10px;">
                                <button type="submit" class="btn btn-primary" id="search" style="border-radius: 3px;height: 34px;">搜索</button>
                            </span>
                        </div>
                    </div>
                </form>

                <table class="table table-striped" id="table-striped" style="background:#fff">
                    <thead>
                    <tr>
                        <th>考试</th>
                        <th>考试项目</th>
                        <th>考试人数</th>
                        <th>平均成绩</th>
                        <th>平均用时</th>
                        <th>详情</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--{{dd($data)}}--}}
                    @forelse($data as $item)
                        <tr>
                            <td>{{$exam_name}}</td>
                            <td>{{$item->subject_name==""?$item->paper_name:$item->subject_name}}</td>
                            <td>{{$item->avg_total}}</td>
                            <td>{{$item->avg_score}}分</td>
                            <td>{{$item->avg_time}}</td>
                            <td><a href="{{route('osce.admin.course.getStudent',[
                                                'exam_id'       => $exam_id,
                                                'subject_id'    => $item->subject_id==''?$item->paper_id:$item->subject_id,
                                                'sign'          => $item->subject_name==""?'paper':'subject',
                                                'subject'       => $item->subject_name==""?$item->paper_name:$item->subject_name,
                                            ])}}">
                                    <span class="read  state1 detail"><i class="fa fa-search fa-2x"></i></span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">{{$backMes}}</td></tr>
                    @endforelse
                    </tbody>
                </table>
                @if(count($data) != 0)
                    <div class="pull-left">
                        共{{$data->total()}}条
                    </div>
                    <div class="btn-group pull-right">
                       {!! $data->appends($_GET)->render() !!}
                    </div>
                @else
                    <div class="pull-left">
                        共0条
                    </div>
                    <div class="btn-group pull-right">

                    </div>
                @endif
            </div>
        </div>
    </div>
@stop{{-- 内容主体区域 --}}