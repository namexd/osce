<?php
$errorsInfo =(array)$errors->getMessages();
if(!empty($errorsInfo))
{
    $errorsInfo = array_shift($errorsInfo);
}
?>
@forelse($errorsInfo as $errorItem)
<div class="pnotice" style="border: #ad0051 2px solid;border:#ebccd1 1px solid;">
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
    })
</script>