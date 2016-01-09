<?php

/**
 * 系统发送消息配置
 */

return [

    'default' => env('MESSAGE_DRIVER', 'pm'),

    'messages'=>[
        'sms'               =>  [
            'driver'        =>  'sms',
            'cnname'        =>  '凌凯',
            'url'           =>  'http://mb345.com/ws/BatchSend.aspx?',
            'username'      =>  'LKSDK0004929',
            'password'      =>  'jkwqm814@',
        ],
        'wechat'            =>  [
            'driver'        =>  'wechat',
            'use_alias'     =>  env('WECHAT_USE_ALIAS', false),
            'app_id'        =>  env('WECHAT_APPID', 'wx660216bfe4ef9b00'), // 必填
            'secret'        =>  env('WECHAT_SECRET', '6b99bb2e397d5ea1c650a5454a63ba55'), // 必填
            'token'         =>  env('WECHAT_TOKEN', 'weixin'),  // 必填,http://good-doctor.cn/hx/wx_sample.php
            'encoding_key'  =>  env('WECHAT_ENCODING_KEY', 'YourEncodingAESKey') // 加密模式需要，其它模式不需要
        ],
        'email'             =>  [
            'driver'        =>  'email',
            'server'        =>  '',//smtp.xx.xx
            'port'          =>  '25',
            'ssl'           =>  false,
            'username'      =>  '',
            'password'      =>  ''
        ],
        'pm'              =>    [
            'driver'        =>  'pm',
        ]
    ]
];