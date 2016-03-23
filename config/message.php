<?php
return array (
  'default' => 'pm',
  'messages' => 
  array (
    'sms' => 
    array (
      'driver' => 'sms',
      'cnname' => '1',
      'url' => 'http://mb345.com/ws/BatchSend.aspx?',
      'username' => 'LKSDK0004929',
      'password' => 'gogo@misrobot123',
    ),
    'wechat' => 
    array (
      'driver' => 'wechat',
      'use_alias' => env('WECHAT_USE_ALIAS', true),
      'app_id' => env('WECHAT_APP_ID','wx660216bfe4ef9b00'),//必填',
      'secret' => env('WECHAT_SECRET','6b99bb2e397d5ea1c650a5454a63ba55'),//必填',
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
      'username' => 'j511002@163.com',
      'password' => '5110021',
    ),
    'pm' => 
    array (
      'driver' => 'pm',
    ),
  ),
);