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
        height: 40px;
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
        font-size: 60px;
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
        height: 50px;
        line-height: 50px;
        font-size: 20px;
        position: absolute;
        top: 0px;
        right: 50px;
    }
    .status{
        width: 50px;
        height: 50px;
        border-radius: 50px;

    }
     p{margin-top: 45px;}
    .white{color: white;}
    .fl{float: left;margin-top: 150px;}
    .m5{margin-left: 15px;}
    .f_red{color: red;}
    #bottom{position:absolute;bottom:10px;right: 30px;height: 57px;width: 240px;}
     #background{
         position: absolute;
         bottom: 0;

     }
        .time-show{
            font-size:16px;
            float:right;
            margin-right: 10px;
        }
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

    <h3 class="top white center">
        {{$msg['exam_name']}}
        <span class="time-show"></span>
    </h3>
        <div class="exam_msg">
            <dl>
                <dt class="center">{{$msg['name']}}</dt>
                @if(!empty($data))
                @foreach($data as $k=>$v)
                <dd class="">{{$k+1..'）'.$v->name}}</dd>

                @endforeach
                @endif
                <dd class="">{{$room_name}}</dd>
            </dl>
            <!--p>
                考试时间：<span class="f_red">{{$msg['mins']}}分钟</span>，时间到请停止考试，根据腕表提示完成考试
            </p-->
            <p>
                考试时间：<span class="f_red">{{$msg['mins']}}分钟</span>，时间到请停止考试，根据提示完成考试
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
            <p><span class="next_room">@if($nextRoom == '') 完成考试后请交还考试卡结束考试 @else 完成此考场的考生请到<span style="color: red">{{$nextRoom}}</span>考场进行下一场考试@endif</span></p>
        </div>
     <div id="status_box">
         @if($status!=4)
         <div class="status fl @if($status==1) green @elseif($status==2) red @else blue @endif"></div>
         <div class="fl m5" style="font-family: 黑体;color:@if($status==1) green @elseif($status==2) red @else deepskyblue @endif ">@if($status==1) 准备完成 @elseif($status==2) 考试中 @else 考试完成 @endif</div>
         @endif
     </div>
</div>

 <!--<div id="bottom">
     <img src="{{asset('osce/images/u4.png')}}" width="100%" height="100%" align="right">
 </div>
 <div id="background">
     <img src="{{asset('osce/images/bg_02.png')}}" align="bottom">
 </div> -->

@stop{{-- 内容主体区域 --}}

@section('only_js')

    <script src="{{asset('osce/admin/plugins/js/plugins/layer/layer.min.js')}}"></script>
    <script>
        /*setInterval(function(){
        $(function () {
            show();
        })
		},3
		);*/

            function show() {

            try {
                //系统时间显示
                var nowTime = new Date(),
                        str_time = '';

                str_time = nowTime.getHours() +':' +nowTime.getMinutes();
                $('.time-show').text(str_time);

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
            }catch (e){

            }

        }
    </script>
@stop