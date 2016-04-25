
return [
   /*
    * 通知选项保存
    */
    'inform' => [
        'wechat' => {{empty($inform['wechat']) ? 0 : 1}},
        'sms' => {{empty($inform['sms']) ? 0 : 1}},
        'mail' => {{empty($inform['mail']) ? 0 : 1}},
        'user_pm' => {{empty($inform['user_pm']) ? 0 : 1}},
    ],

   /*
    * 邀请选项保存
    */
    'invite' => [
        'wechat' => {{empty($invite['wechat']) ? 0 : 1}},
        'sms' => {{empty($invite['sms']) ? 0 : 1}},
        'mail' => {{empty($invite['mail']) ? 0 : 1}},
        'user_pm' => {{empty($invite['user_pm']) ? 0 : 1}},
    ],


   /*
    * 学生通知方式保存
    */
    'student' => [
        'wechat' => {{empty($student['wechat']) ? 0 : 1}},
        'sms' => {{empty($student['sms']) ? 0 : 1}},
        'mail' => {{empty($student['mail']) ? 0 : 1}},
        'user_pm' => {{empty($student['user_pm']) ? 0 : 1}},
    ],

   /*
    * 间隔时间保存
    */
    'mins' => {{$mins}}
];