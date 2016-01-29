
return [
'use_alias'    => env('WECHAT_USE_ALIAS', {{$use_alias}}),
'app_id'       => env('WECHAT_APPID', '{{$app_id}}'), // 必填
'secret'       => env('WECHAT_SECRET', '{{$secret}}'), // 必填
'token'        => env('WECHAT_TOKEN', '{{$token}}'),  // 必填,http://good-doctor.cn/hx/wx_sample.php
'encoding_key' => env('WECHAT_ENCODING_KEY', '{{$encoding_key}}') // 加密模式需要，其它模式不需要
];