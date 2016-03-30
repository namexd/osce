<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/30
 * Time: 20:20
 */

namespace Modules\Osce\Repositories;

use Redis;
class PropelRepositories extends BaseRepository
{
    public function propelling($user, $message) {
        $redis = Redis::connection('message');
        $redis->publish($user, $message);
    }
}