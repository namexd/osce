<?php
return array (
  'default' => 'pm',
  'messages' => 
  array (
    'sms' => 
    array (
      'app_key' => '23576437',
      'app_secret' => 'f9fec0c87812bd9e5301ac49ea9bd6db',
      'SingName' => '智慧医教',
      'TemplateCode1' => 'SMS_34875276',
      'TemplateCode2' => 'SMS_35005390',
      'TemplateCode3' => 'SMS_34860490',
      'TemplateCode4' => 'SMS_35070529',
      'TemplateCode5' => 'SMS_35100044',
       'TemplateCode6' => 'SMS_35100040',


      'request_host' => 'http://sms.market.alicloudapi.com',
      'request_uri' => '/singleSendSms',
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