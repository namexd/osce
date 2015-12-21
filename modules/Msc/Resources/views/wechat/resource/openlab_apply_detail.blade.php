@extends('msc::wechat.layouts.admin')

@section('only_head_css')
<link href="{{asset('msc/wechat/resourcemanage/css/information.css')}}" rel="stylesheet" type="text/css" />
<style>
    .w_94.submit_box div{
        width: 50%;
        float: left;
        padding: .5em;
    }
    .btn1{margin-top: 0;}
    .form-group p{
        font-size: 16px!important;
        margin-left: 5.75em!important;
        padding-top: 8px;
    }
    .form-group:last-child{min-height: 50px;}
    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {background-color: #fff;}

   .jconfirm .jconfirm-box div.content{text-align: center;}
   .jconfirm.white .jconfirm-box .buttons{float:none;text-align: center;}
</style>
@stop


@section('content')
<div class="user_header">
    <a class="left header_btn" href="javascript:history.back(-1)">
        <i class="fa fa-angle-left clof font26 icon_return"></i>
    </a>
        预约申请管理
    <a class="right header_btn" href="{{route('msc.personalCenter.infoManage')}}">
        <i class="fa fa-home clof font26 icon_return"></i>
    </a>
</div>


<form id="sourceForm">
    <div class="add_main">
        <div class="form-group">
            <label for="">教室</label>
            <input type="text" readonly="readonly" value="{{$apply->lab->name}}" class="form-control">
        </div>
        <div class="form-group">
            <label for="">申请人</label>
            <input type="text" readonly="readonly" value="{{$user->name}}" class="form-control">
        </div>
        <div class="form-group">
            <label for="">时间段</label>
            <input type="text" readonly="readonly" value="{{$apply->apply_date}} {{date('H:i',strtotime($apply->calendar->begintime))}}-{{date('H:i',strtotime($apply->calendar->endtime))}}" class="form-control">
        </div>
        <div class="form-group">
            <label for="">理由</label>
            <p>{{empty($apply->detail) ? '' : $apply->detail}}</p>
        </div>
    </div>
    <div class="w_94 submit_box" value="{{$apply->id}}">
        <div><input type="button" class="btn5" value="审核不通过"/></div>
        <div><input type="button" class="btn1" value="审核通过"/></div>
    </div>
</form>
@stop

@section('only_head_js')
<script>
$(function(){
    /**
    *跳转页面
    */
    $('.btn5').on('click',function(){
        var id = $(this).parent().parent().attr('value');
        location.href = "{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@getRefusedOpenLabApply')}}?id="+id;
    })

      /**
      *通过申请弹出框
      */
     $('.btn1').on('click',function(){
         var id = $(this).parent().parent().attr('value');
         $.confirm({
             title: ' ',
             content: '确定通过该申请？',
             confirmButton: '　　　是　　　 ' ,
             cancelButton: '　　　否　　　',
             confirmButtonClass: 'btn-info',
             cancelButtonClass: 'btn-danger',
             confirm:function(){
                 //提交
                 $.ajax({
                     url:"{{action('\Modules\Msc\Http\Controllers\WeChat\LabController@postChangeOpenLabApplyStatus')}}",
                     type:"post",
                     dataType:"json",
                     cache:false,
                     data:{id:id,status:1},
                     success: function(res) {
                         if(res.code != 1){
                             layer.alert((res.message).split(':')[1]);
                             console.log(res.message);
                         }else{
                             //成功的操作
                             layer.alert('预约成功！');
                             console.log('通过！')
                             window.location.reload();
                         }
                     },

                 });
             }
         });
     })
})
</script>
@stop