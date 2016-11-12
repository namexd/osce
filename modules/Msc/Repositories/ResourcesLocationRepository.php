<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/11
 * Time: 20:20
 */

namespace Modules\Msc\Repositories;

use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesImage;
use Modules\Msc\Entities\ResourcesLocation;
use Modules\Msc\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourcesLocationRepository extends BaseRepository
{
    public function __construct(ResourcesLocation $ResourcesLocation)
    {
        $this->model=$ResourcesLocation;
    }
    public function getResourcesLocationListByKeuword($keyword){
        return $this->getSearchWhere($keyword);
    }

}