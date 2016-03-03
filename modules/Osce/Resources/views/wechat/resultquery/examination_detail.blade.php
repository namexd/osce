@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link rel="stylesheet" href="{{asset('osce/wechat/css/resultquery.css')}}" type="text/css" />
@stop
@section('only_head_js')

@stop

@section('content')
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.student-exam-query.getResultsQueryIndex')}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	成绩查询
       	<a class="right header_btn nou clof header_a" href="javascript:;">
       	</a>
    </div>
    <div class="form-group">
        <select  id="examination" disabled="disabled" class="form-control normal_select select_indent" name="student_type" required>
            <option value="3">{{$examName->name}}</option>
        </select>
    </div>
  	<div class="examination_msg">
		<div class="form-group">
            <label for="">评价老师</label>
            <div class="txt">{{$examresultList->teacher->name}}</div>
        </div>
        <div class="form-group">
            <label for="">成绩</label>
            <div class="txt">{{$examresultList->score}}</div>
        </div>
        <div class="form-group">
            <label for="">提交时间</label>
            <div class="txt">{{$examresultList->end_dt}}</div>
        </div>
        <div class="form-group">
            <label for="">评价</label>
            <div class="txt">{{($examresultList->evaluate == 'null'?'':$examresultList->evaluate)}}</div>
        </div>
  	</div>

  	<div class="detail_box">
	  	<table id="detail_tb">
	  		<tr>
	  			<th width="15%">序号</th>
	  			<th width="55%">考核内容</th>
	  			<th width="15%" style="text-align: center">满分</th>
	  			<th width="15%" style="text-align: center">得分</th>
	  		</tr>

            @forelse($examScoreList as $key => $value)
                <tr class="active">
                    <td>{{$value['sort']}}</td>
                    <td>{{$value['content']}}</td>
                    <td style="text-align: center">{{$value['tScore']}}</td>
                    <td style="text-align: center">{{$value['score']}}</td>
                </tr>
                @forelse($value['items'] as $k => $item)
                    <tr>
                        <td>{{$item['standard']->parent->sort.'-'.$item['standard']->sort}}</td>
                        <td>{{$item['standard']->content}}</td>
                        <td style="text-align: center">{{$item['standard']->score}}</td>
                        <td style="text-align: center">{{$item['score']}}</td>
                    </tr>
                @empty
                @endforelse
            @empty
            @endforelse



			{{--@forelse($examScoreList as $examScore)--}}
                {{--<tr class="active">--}}
                    {{--<td>{{$examScore->standard->pid==0? $examScore->standard->sort:$examScore->standard->parent->sort.'-'.$examScore->standard->sort}}</td>--}}
                    {{--<td>{{$examScore->standard->content}}</td>--}}
                    {{--<td>{{$examScore->standard->score}}</td>--}}
                    {{--<td>{{$examScore->score}}</td>--}}
                {{--</tr>--}}
            {{--@empty--}}
			{{--@endforelse--}}

	  	</table>
	</div>
@stop