<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2015/12/3 0003
 * Time: 16:11
 */

namespace Modules\Msc\Repositories;

use Modules\Msc\Entities\ResourcesLabHistory;

class ResourcesLabHistoryRepository extends BaseRepository
{
    public function __construct(ResourcesLabHistory $resourcesLabHistory)
    {
        $this->model = $resourcesLabHistory;
    }
}