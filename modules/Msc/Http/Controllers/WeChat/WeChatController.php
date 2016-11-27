<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@163.com>
 * Date: 2015/11/24
 * Time: 11:32
 */

namespace Modules\Msc\Http\Controllers\WeChat;

use Modules\Msc\Http\Controllers\MscWeChatController;
use Illuminate\Http\Request;
use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesImage;

class WeChatController extends  MscWeChatController
{
    public function getTools(){
        echo 123;
    }
}