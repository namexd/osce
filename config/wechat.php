<?php return [
'use_alias'    => env('WECHAT_USE_ALIAS', true),
'app_id'       => env('WECHAT_APPID', 'helloapp'), // 必填
'secret'       => env('WECHAT_SECRET', 'hellosecret'), // 必填
'token'        => env('WECHAT_TOKEN', 'weixin'),  // 必填,http://good-doctor.cn/hx/wx_sample.php
'encoding_key' => env('WECHAT_ENCODING_KEY', 'YourEncodingAESKey') // 加密模式需要，其它模式不需要
];
