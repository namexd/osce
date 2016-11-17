<?php
return array (
  'default' => 'pm',
  'messages' => 
  array (
    'sms' => 
    array (
      'driver' => 'sms',
      'cnname' => '1',
      'url' => 'http://atapp.com/message?',
      'username' => 'whatisyourname',
      'password' => 'nopass',
    ),
    'wechat' => 
    array (
      'driver' => 'wechat',
      'use_alias' => env('WECHAT_USE_ALIAS', true),
      'app_id' => env('WECHAT_APP_ID','helloapp'),//必填',
      'secret' => env('WECHAT_SECRET','hellosecret'),//必填',
      'token' => env('WECHAT_TOKEN','weixin'),// 必填,http://good-doctor.cn/hx/wx_sample.php',
      'encoding_key' => env('WECHAT_ENCODING_KEY','YourEncodingAESKey'),// 加密模式需要，其它模式不需要',
    ),
    'email' => 
    array (
      'driver' => 'email',
      'server' => 'smtp.163.com',
      'protocol' => 'POP3',
      'port' => '25',
      'ssl' => 'flase',
      'username' => 'hello@163.com',
      'password' => 'nopass',
    ),
    'pm' => 
    array (
      'driver' => 'pm',
    ),
  ),
);
