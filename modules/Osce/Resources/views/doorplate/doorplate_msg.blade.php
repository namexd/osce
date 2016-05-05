@extends('osce::admin.layouts.admin_index')

@section('only_css')
    <style>
     .box{
         width: 80%;
         height: 500px;
         margin: auto;
     }
    .top{
        background:#999;
        height: 35px;
        line-height: 35px;
        padding-left: 30px;
        width: 100%;
        position: relative;
    }
    #ret{
        position: absolute;
        top:0;
        right: 15px;
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
    .blue{background: deepskyblue}
    .status{
        width: 50px;
        height: 50px;
        margin-top: 150px;
    }
     p{margin-top: 45px}
    .white{color: white}
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
    <h3 class="top white">{{$msg['exam_name']}}
        <a href="{{ route('osce.doorplate.doorplatestart')}}" id="ret">
            <span class="state1 abandon">
                <i class="fa fa-cog fa-2x"></i>
            </span>
        </a>
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
                考试时间：{{$msg['mins']}}分钟，时间到请停止考试，根据腕表提示完成考试
            </p>
            <p>
                <span>当前考生：</span>
                  <span class="current-set">
                    @if(!empty($current))
                        @foreach($current as $v)
                        <span>{{$v->student_name}}</span>　
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
                            <span>{{$v->student_name}}</span>　
                        @endforeach
                    @else
                        暂时没有考生
                    @endif
                </span>
            </p>

        </div>
     <div id="status_box">
         <div class="status @if($status==1) green @elseif($status==2) red @else blue @endif"></div>
     </div>
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
                    if(msg){
                        $(msg).each(function(i,k){

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
                    if(msg){
                        $(msg).each(function(i,k){

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
                        $('#status_box').html('<div class="status green"></div>');
                    }else if(e==2){
                        $('#status_box').html('<div class="status red"></div>');
                    }else{
                        $('#status_box').html('<div class="status blue"></div>');
                    }
                }
            });
        },5000)
    </script>
@stop