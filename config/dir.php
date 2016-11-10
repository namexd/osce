<?php
    //定义常量路径
	if(!defined('MESSAGE_CONFIG'))
	{
			define("MESSAGE_CONFIG",dirname(__FILE__) . '/message.php');
	}

	if(!defined('WECHAT_CONFIG'))
	{
			define("WECHAT_CONFIG",dirname(__FILE__) . DIRECTORY_SEPARATOR.'wechat.php');
	}
	if(!defined('MAIL_CONFIG'))
    {
			define("MAIL_CONFIG",dirname(__FILE__) . DIRECTORY_SEPARATOR.'mail.php');
	}
	if(!defined('OSCE_ATTACH'))
	{
			define("OSCE_ATTACH",dirname(dirname(__FILE__)) . '/modules/Osce/Attach/');
	}
    
	if(!defined('SYS_PARAM'))
	{
			define("SYS_PARAM", dirname(dirname(__FILE__)) . '/modules/Osce/Config/sysparam.php');
	}