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
use App\Repositories\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourcesRepository extends BaseRepository
{
    public function __construct(Resources $resources)
    {
        $this->model=$resources;
    }
    public function getResourcesByParam($where,$pagesize=10,$order=['id','desc']){
        if(empty($where))
        {
            $model= $this->model;
        }
        else
        {
            $model=$this->model;
            foreach($where as $param)
            {
                $model=$model->where($param[0],$param[1],$param[2]);
            }
        }
        return $model->orderBy($order[0],$order[1])->paginate($pagesize);
    }
    public function getResourcesListByKeuword($keyword,$feild=''){
        return $this->model->searchByKeyword($keyword,$feild);//getSearchWhere($keyword);
    }
    public function ResourcesImageDel($id,$images){
        $imageBuilder=ResourcesImage::where('Resources_id','=',$id);

        if(!empty($images))
        {
            $imageBuilder=$imageBuilder->whereNotIn('url',$images);
        }
        $delList=$imageBuilder->get();

        $delist=[];
        foreach($delList as $image)
        {
            $delData=[
                'id'=>$image->id,
                'url'=>$image->url,
            ];
            $result=$image->delete();
            $delData['result']=$result;
            if($result)
            {
                $imageDir=Storage::disk('images');
                $unlinkResult=$imageDir->delete($delData['url']);
                if(!$unlinkResult)
                {
                    Log::error(date('Y-m-d H:i:s').' : '.$image->url.'文件删除失败');
                }
                $delist[]=$delData;
            }
        }
        return $delist;
    }
    public function getResourcesBorrow(){

    }
}