@extends('osce::theory.base')

@section('title')

	试卷预览
@stop
<?php
header("Content-Type: application/msword");
header("Content-Disposition: attachment; filename=".$data->name.".doc"); //指定文件名称
header("Pragma: no-cache");
header("Expires: 0");
?>
@section('head_css')
	<style>
		*,p{ margin: 0; padding: 0;}
		body { background-color:#f3f7f8 ;}
		.cBorder{border: 1px solid #e7eaec;}
		
 .body
{
    /*float: left;*/
    /*position: absolute;*/
    width: 95%;
    height: 95%;
    padding: 2.5%;
    text-align: left;
}	
.question_type { margin-bottom: 20px;}
.allSubject { padding: 0 2.5%;}
	
.subjectBox img { display: block; height: auto; max-width: 20%; width: auto;}	
.countdown {text-align: center; width: 800px;padding-left:400px; position: fixed; bottom: 5%; right: 0;}
.type_1 li ,.type_2 li,.type_3 li {cursor: pointer;}
.question_type li { padding: 5px 0;}
.allSubject div { padding: 10px 0 5px;}
#jiaojuan {}

        .colockbox{width:250px;height:30px;overflow: hidden; color:#000;}
        .colockbox span{
            float:left;display:inline-block;
            width:30px;height:29px;
            line-height:29px;font-size:20px;
            font-weight:bold;text-align:center;
            color:#ff0101; margin-right:5px;}
	
	.allSubject label { font-weight: normal;}
	
	.stu_a label,.stu_a span {color: #e86f64; font-weight: bolder;}
	.stu_ra label,.stu_ra span {color: #2d8f7b; font-weight: bolder;}
	.stu_cuo { border: 1px solid #e86f64;}
	.form-control { height: auto;}
	.dafen { display: none;}
	.radio_icon { display: none;}
	
	
	</style>
@stop
<?php
	$type=[
		1=>'单选题',
		2=>'多选题',
		3=>'判断题',
		4=>'填空题',
		5=>'名词解释题',
		6=>'论述题',
		7=>'简答题'
	];
?>

@section('body')
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
                <div class="ibox float-e-margins" style="margin-bottom: 0;">
                    <div class="ibox-content text-center p-md">
                        <h2>{{$data->name}}</h2>
                        <span style="margin-left: 1em;">创建时间：</span>
                        <span class="score">{{$data->ctime}}</span>
                        <span style="margin-left: 1em;">试卷总分：</span>
                        <span class="score">{{$data->score}}分</span>
                    </div>
                </div>
            </div>
			<div class="step-content body current">
				<div class="col-lg-12">
					@foreach(collect($data->questionHas)->sortBy('type')->groupBy('type') as $k => $val )

						<p><label class="font20"><?php echo $type[$k]?></label><span style="margin-left: 1em;">共<span>{{count($val)}}</span>题</span></p>
						@foreach($val as $key=>$value )
							<div class="question_type type_3">
								<div class="allSubject" _a="" _ra="undefined">
									<div class="clearfix">
										<span class="font16">{{($key+1)}}、{{$value['question']}}</span>
										<div class="dafen"><strong>分数：</strong>{{$value['poins']}}</div>
									</div>
									<label class="marl_10">{{$value['content']}}</label>
									{{--<ul><li class=""><div class="radio_icon left"></div><span class="marl_10">正确</span></li>
										<li class=""><div class="radio_icon left"></div><span class="marl_10">错误</span></li>
									</ul>--}}
								</div>
							</div>
						@endforeach
					@endforeach
            	</div>
			</div>
		</div>
	</div>
@stop{{-- 内容主体区域 --}}











