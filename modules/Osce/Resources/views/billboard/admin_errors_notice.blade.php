<?php
$errorsInfo =(array)$errors->getMessages();
if(!empty($errorsInfo))
{
    if(array_key_exists('code',$errorsInfo))
    {
        $code       =   array_shift($errorsInfo['code']);
        $errorsInfo =   $errorsInfo['msg'];
    }
    else
    {
        $code   =   0;
        $errorsInfo = array_shift($errorsInfo);
    }
}else{

    $code   =   -123;

}


?>
{{--@if (session('success')===false||$code==0)--}}
{{--@forelse($errorsInfo as $errorItem)--}}
{{--<div class="pnotice" style="border: #ad0051 2px solid;border:#ebccd1 1px solid;display: none;">--}}
{{--<div class="" style="background-color: #f2dede;">--}}
{{--<div style="float: left;" style="color: #a94442;">{{$errorItem}}</div>--}}
{{--<div style="float:right;margin-right: 2px;cursor: pointer;" class="closeNotice">&nbsp;X&nbsp;</div>--}}
{{--<div style="clear: both;"></div>--}}
{{--</div>--}}
{{--</div>--}}
{{--@empty--}}
{{--@endforelse--}}
{{--@endif--}}

{{-- 添加成功提示 --}}
{{--@if (session('success')||$code==1)--}}
{{--@forelse($errorsInfo as $errorItem)--}}
{{--<div class="success-notice" style="border: #ad0051 2px solid;border:#ebccd1 1px solid;display: none;">--}}
{{--<div class="" style="background-color: #f2dede;">--}}
{{--<div style="float: left;" style="color: #a94442;">{{$errorItem}}</div>--}}
{{--<div style="float:right;margin-right: 2px;cursor: pointer;" class="closeNotice close-success-notice">&nbsp;X&nbsp;</div>--}}
{{--<div style="clear: both;"></div>--}}
{{--</div>--}}
{{--</div>--}}
{{--@empty--}}
{{--@endforelse--}}
{{--@endif--}}



<div class="msg" code="{{$code}}" style="display: none">
    @forelse($errorsInfo as $errorItem)
        <div class="msg-info">{{$errorItem}}</div>
    @empty
    @endforelse
</div>

<script>
    $(function(){

        var msg = $('.msg-info').text();

        if($('.msg').attr('code')==1){
            layer.msg(msg,{skin:'msg-success',icon:1});
        }
        else if($('.msg').attr('code')==0) {
            layer.msg(msg,{skin:'msg-error',icon:1});
        } else {
            return;
        }
    })
</script>