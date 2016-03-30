<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/3/30
 * Time: 16:23
 */

namespace Modules\Osce\Repositories;

use Redis;
class RedisRepository extends BaseRepository
{
    public function publish()
    {
        $redis = Redis::connection('message');
        $redis->publish();
    }
}