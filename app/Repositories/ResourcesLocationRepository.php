<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/11
 * Time: 20:20
 */

namespace App\Repositories;

use App\Entities\Msc\Resources;
use App\Entities\Msc\ResourcesImage;
use App\Entities\Msc\ResourcesLocation;
use App\Repositories\BaseRepository;
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