@extends('osce::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('osce/common/css/bootstrapValidator.css')}}" rel="stylesheet">
    <style type="text/css">
        .title{
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .content-box{
            padding: 20px;
            background: #fff;
            text-align: left;
        }
        .history-list{margin-top: 10px}
        .history-list li{
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        .history-list li textarea{
            width: 100%;
            height: 120px;
        }
        /*header*/
        .item-l,.item-c,.item-r{
            float: left;
            color: #cccccc;
            margin: 2px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .item-l{width: 50%;color:#42b2b1;}
        .item-c{width: 45%}
        .item-r{width: 5%}
        .title-con{padding-top: 10px;}

        /*content*/
        .content-header{margin-bottom: 10px;}
        .content-l{
            font-size: 12px;
            width: 60%;
            float: left;
        }
        .content-l span{margin: 0 3px;}
        .content-l span:first-child{margin-left: 0;}
        .content-l .student{color: #42b2b1;}
        .content-l .time{color: #cccccc;}
        .content-r{
            width: 40%;
            float: left;
        }
        .option{
            z-index: 10;
            position: absolute;
            background: #e7eaed;
            top: 45px;
            right: 0;
            display: none;
        }
        .option li{
            margin: 0;
            height: 30px;
            width: 60px;
            line-height: 30px;
            text-align: center;
        }
        .btn2{background: #1ab394}
        .has-feedback label~.form-control-feedback {top: 26px;}
        .layui-layer-title{
            background: #fff!important;
            color: #1ab394!important;
            font-size: 16px!important;
        }
        .layui-layer-btn {
            background: #fff !important;
            border-top: 1px #fff solid !important;
        }
        .layui-layer-btn0{
            border:1px solid #1ab394!important;
            background: #1ab394 !important;
        }
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
                    content: {/*键名username和input name值对应*/
                        validators: {
                            notEmpty: {/*非空提示*/
                                message: '不能为空！'
                            },
                            stringLength: {
                                max:200,
                                message: '回复内容长度必须少于200字符'
                            }
                        }
                    }
                }
            });
    //回复
    $('.btn2').click(function(){
        var content = $('#context').val();
        if(content==''){
            $.alert({
                title: '提示：',
                content: '回复内容不能为空!',
                confirmButton: '确定',
                confirm: function(){
                }
            });
            return;
        }

        $.ajax({
            type:'post',
            url:'{{route("osce.wechat.postAddReply")}}',
            data:{content:content,id:$('input[name=id]').val()},
            success:function(res){
                if(res.code!=1){
                    layer.alert(res.message);
                }else{
                	$.alert({
		                title: '提示：',
		                content: '回复成功!',
		                confirmButton: '确定',
		                confirm: function(){
		                	location.href = '{{route("osce.wechat.getCheckQuestion")}}?id='+$('input[name=id]').val();
		                }
		            });
                }
            }
        });
    });

})
</script> 
@stop


@section('content')
    <ul class="option">
        <li><a href="#">编辑</a></li>
        <li><a href="">删除</a></li>
    </ul>
    <div class="user_header">
        <a class="left header_btn" href="{{route('osce.wechat.getCheckQuestion',['id'=>$id])}}">
            <i class="fa fa-angle-left clof font26 icon_return"></i>
        </a>
       回复
        <a class="right header_btn" href="{{route('osce.wechat.index.getIndex')}}">
            <i class="fa fa-home clof font26 icon_return"></i>
        </a>
    </div>
    <div class="content-box">
        @foreach($list as $list)
        <div>
            <h2 class="title">{{  $list->title }}</h2>
            <div class="title-footer">
                <div class="item-l">{{  $list->getAuthor->name }}</div>
                <div class="item-c" style="width: 50%;text-align: right;">{{  $list->created_at }}</div>
                <div class="clearfix"></div>
            </div>
            <div class="title-con">
                <p>{{  $list->content    }}</p>
            </div>
        </div>
    </div>
    @endforeach
    <ul class="history-list">
        <li>
            <form id="list_form">
                <input type="hidden" name="id" value="{{ $list->id }}">

                <div class="form-group">
                  <label class="" for="name">&nbsp;</label>
                  <textarea style="width:96%;margin:0 2%;height:100px;resize: none;" class="form-control" id="context" name="content" placeholder="请输入要反馈的内容,不超过200字~" rows="5"></textarea>
                </div>
                <input  style="width:96%;margin:0 2%;" type="button" value="提交" class="btn2" />
            </form>
        </li>
    </ul>
@stop