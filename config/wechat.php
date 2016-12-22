<?php return [
'use_alias'    => env('WECHAT_USE_ALIAS', true),
'app_id'       => env('WECHAT_APPID', 'wxea5fd73483163570'), // 必填
'secret'       => env('WECHAT_SECRET', 'a3a78120093fc569d55a221455d18826'), // 必填
'redirect_url' => env('WECHAT_REDIRECT', 'osce.wststore.com'), // 不必填，主域名非微信回调地址需要
'token'        => env('WECHAT_TOKEN', 'weixin'),  // 必填,http://good-doctor.cn/hx/wx_sample.php
'encoding_key' => env('WECHAT_ENCODING_KEY', 'YourEncodingAESKey') // 加密模式需要，其它模式不需要
];