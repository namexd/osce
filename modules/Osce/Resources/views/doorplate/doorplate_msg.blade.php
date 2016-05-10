@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>

     .box{
         width: 100%;

         height: 500px;
         margin: auto;
     }
    .top{
        background:#364150;
        height: 60px;
        line-height: 60px;
        font-size: 20px;
        width: 100%;
        position: relative;
    }
    #top_img{
        position: absolute;
        top:0;
        left: 10px;
        width: 35px;
        height: 35px;
    }

    .f20{
        font-size: 20px;
    }
    .exam_msg{
        width: 90%;
        margin: auto;
        font-size: 25px;
        margin-top: 50px;
    }
    .green{
        background: green;
    }
    .red{
        background: red;
    }
    .blue{background: deepskyblue;}
    #status_box{
        width: 200px;
        height: 30px;
        line-height: 30px;
        font-size: 20px;
        position: absolute;
        top: 0px;
        right: 107px;
    }
    .status{
        width: 30px;
        height: 30px;
        border-radius: 30px;

    }
     p{margin-top: 45px;}
    .white{color: white;}
    .fl{float: left;margin-top: 150px;}
    .m5{margin-left: 15px;}
    .f_red{color: red;}
    #bottom{position:absolute;bottom:20%;right: 175px}
    </style>
@stop
<?php
$errorsInfo =(array)$errors->getMessages();
if(!empty($errorsInfo)){
    $errorsInfo = array_shift($errorsInfo);
}
?>
@forelse($errorsInfo as $errorItem)
    <div class="pnotice" style="display: none;">{{$errorItem}}</div>
@empty
@endforelse

@section('content')
 <div class="box">

    <h3 class="top white center">{{$msg['exam_name']}}
        <a href="{{ route('osce.doorplate.doorplatestart')}}" id="top_img"><img src="{{asset('osce/images/uuz.png')}}" width="9px" height="14px" align="center"></a>

    </h3>
        <div class="exam_msg">
            <dl>
                <dt class="center">{{$msg['name']}}</dt>
                @if(!empty($data))
                @foreach($data as $v)
                <dd class="">{{$v->name}}</dd>

                @endforeach
                @endif
            </dl>
            <p>
                考试时间：<span class="f_red">{{$msg['mins']}}</span>分钟，时间到请停止考试，根据腕表提示完成考试
            </p>
            <p>
                <span>当前考生：</span>
                  <span class="current-set">
                    @if(!empty($current))
                        @foreach($current as $v)
                        @if(!empty($v->student_name))
                        <span>{{$v->student_name}}</span>　
                        @endif
                        @endforeach
                    @else
                        暂时没有考生
                    @endif
                    </span>
            </p>

            <p>
                <span>下组考生：</span>
                <span class="next-set">
                @if(!empty($next))
                        @foreach($next as $v)
                            @if(!empty($v->student_name))
                            <span>{{$v->student_name}}</span>　
                            @endif
                        @endforeach
                 @else
                        暂时没有考生
                 @endif
                </span>
            </p>

        </div>
     <div id="status_box">
         @if($status!=4)
         <div class="status fl @if($status==1) green @elseif($status==2) red @else blue @endif"></div>
         <div class="fl m5" style="font-family: 黑体;color:@if($status==1) green @elseif($status==2) red @else deepskyblue @endif ">@if($status==1) 准备完成 @elseif($status==2) 考试中 @else 考试完成 @endif</div>
         @endif
     </div>
</div>
 <div id="bottom">
     <img src="{{asset('osce/images/u4.png')}}" width="135px" height="53px" align="right">
 </div>
@stop{{-- 内容主体区域 --}}

@section('only_js')

    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
    <script>
        setInterval(function(){
            //当前组
            var opstr='',opst='';
            $.ajax({
                type: "GET",
                url: "{{route('osce.doorplate.getexaminee')}}",
                data: {'room_id':{{$room_id}},'exam_id':{{$exam_id}},'data':{{count($data)}},'screen_id':{{$screen_id}}},
                success: function(msg){

                    if(msg[0]){
                        $(msg).each(function(i,k){
                            if(k.student_name)
                            opstr += '<span>'+k.student_name+'</span>　';
                        });
                        $('.current-set').html(opstr);
                    }else{
                        $('.current-set').html('<span>暂时没有考生</span>');
                    }
                }
            });
        //下一组
            $.ajax({
                type: "GET",
                url: "{{route('osce.doorplate.getnextexaminee')}}",
                data: {'room_id':{{$room_id}},'exam_id':{{$exam_id}},'data':{{count($data)}},'screen_id':{{$screen_id}}},
                success: function(msg){
                    if(msg[0]){
                        $(msg).each(function(i,k){
                            if(k.student_name)
                            opst += '<span>'+k.student_name+'</span>　';
                        });
                        $('.next-set').html(opst);
                    }else{
                        $('.next-set').html('<span>暂时没有考生</span>');
                    }
                }
            });
            //状态
            $.ajax({
                type: "GET",
                url: "{{route('osce.doorplate.getstatusstatus')}}",
                data: {'room_id':{{$room_id}},'exam_id':{{$exam_id}},'screen_id':{{$screen_id}}},
                success: function(e){
                    if(e==1){
                        $('#status_box').html('<div class="status fl green"></div> <div class="fl m5" style="font-family: 黑体;color:green ">准备完成 </div>');

                    }else if(e==2){
                        $('#status_box').html('<div class="status fl red"></div> <div class="fl m5" style="font-family: 黑体;color: red ">考试中 </div>');
                    }else if(e==3){
                        $('#status_box').html('<div class="status fl blue"></div> <div class="fl m5" style="font-family: 黑体;color: deepskyblue "> 考试完成</div>');
                    }
                }
            });
        },5000)
    </script>
@stop