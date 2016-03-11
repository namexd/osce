<?php
$errorsInfo =(array)$errors->getMessages();

if(!empty($errorsInfo))
{
    $errorsInfo = array_shift($errorsInfo);
}
?>
@forelse($errorsInfo as $errorItem)
    <style>

        .layui-layer-dialog .layui-layer-content{
            padding:12px 20px;
            text-align: center;
            color: #fff;
        }
        .layui-layer-msg1{min-width:120px;
            background:rgba(0,0,0,.6);}
    </style>
<div class="pnotice" style="border: #ad0051 2px solid;border:#ebccd1 1px solid;display: none;">
    <div class="" style="background-color: #f2dede;">
        <div style="float: left;" style="color: #a94442;">{{$errorItem}}</div>
        <div style="float:right;margin-right: 2px;cursor: pointer;" class="closeNotice">&nbsp;X&nbsp;</div>
        <div style="clear: both;"></div>
    </div>
</div>
@empty
@endforelse
<script>
    $(function(){
        $('.closeNotice').click(function(){
            $(this).parents('.pnotice').remove();
        });


        //错误提示
        var msg = $('.pnotice').find('div').find('div').eq(0).text();
        if(msg==''){
            return;
        }else{
            layer.msg($('.pnotice').find('div').find('div').eq(0).text(),{skin:"layui-layer-msg1",time: 2000});
        }

    })
</script>