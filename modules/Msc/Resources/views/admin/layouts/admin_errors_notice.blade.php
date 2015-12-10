<?php
$errorsInfo =(array)$errors->getMessages();
if(!empty($errorsInfo))
{
    $errorsInfo = array_shift($errorsInfo);
}
?>
@forelse($errorsInfo as $errorItem)
    <div>
        {{$errorItem}}
    </div>
@empty
@endforelse
