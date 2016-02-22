@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
<style>
.btn2{background: #1ab394}
.has-feedback label~.form-control-feedback {top: 26px;}
</style>
@stop
@section('only_head_js')
<script src="{{asset('osce/common/js/bootstrapValidator.js')}}"></script>
<script>
$(function(){
    $('#list_form').bootstrapValidator({
                message: 'This value is not valid',
                feedbackIcons: {/*输入框不同状态，显示图片的样式*/
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {/*验证*/
                    title: {/*键名username和input name值对应*/
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '标题不能为空！'
                            }
                        }
                    },
                    content: {/*键名username和input name值对应*/
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '不能为空！'
                            },
                            stringLength: {
                                max:200,
                                message: '内容长度必须少于200字符'
                            }
                        }
                    }
                }
            });
})
</script> 
@stop


@section('content')
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.getCheckQuestion',['id'=>$id])}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       	编辑
    </div>
    <form class="quiz_form" action="{{  route('osce.wechat.postEditQuestion') }}" method="post" id="list_form">
		@foreach($list as $list)
		<div class="form-group">
	      <label class="" for="name">名称：</label>
	      <input type="text" class="form-control" name="title" id="" value="{{  $list->title }}" placeholder="请输入名称">
	    </div>
	    <div class="form-group">
	      <label class="" for="name">内容：</label>
	      <textarea class="form-control" style="width:96%;margin:0 2%;height:100px;resize: none;" id="context" name="content" placeholder="请输入要反馈的内容,不超过200字~" rows="5">{{  $list->content  }}</textarea>
	    </div>
	    <div class="form-group">
			<input type="hidden" name="id" value="{{ $list->id }}">
    		<input style="width:96%;margin:0 2%;" class="btn btn2" type="submit" value="提交"/>
    	</div>
			@endforeach
    </form>
@stop