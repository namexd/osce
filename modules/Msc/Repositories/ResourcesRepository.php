<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Zouyuchao@sulida.com>
 * Date: 2015/11/11
 * Time: 20:20
 */

namespace Modules\Msc\Repositories;

use Modules\Msc\Entities\Resources;
use Modules\Msc\Entities\ResourcesImage;
use Modules\Msc\Repositories\BaseRepository;
use Modules\Msc\Entities\ResourcesBorrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Common;

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
        $imageBuilder=ResourcesImage::where('resources_id','=',$id);

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

    /**
     *  根据where 条件获取 借出记录 查询语句对象
     * * string        $where        筛选条件(必须的) e.g[['id','=',1],['code','=',12]]
     *
     * @return bulider对象
     *
     */
    public function getResourcesBorrowBuilderByWhere($where){
        $ResourcesBorrowingModel=new ResourcesBorrowing();
        $model=$ResourcesBorrowingModel;
        foreach($where as $param)
        {
            $model=$model->where($param[0],$param[1],$param[2]);
        }
        return $model;
    }
    public function changeBorrowApplyStatus($id,$data){
        $apply=ResourcesBorrowing::find($id);

        $time_start=$data['begindate'];
        $time_end=$data['enddate'];
        $validated=$data['validated'];
        try{
            if(!is_null($apply))
            {
                if($apply->status==-2)
                {
                    throw new \Exception('该申请已经作废');
                }
                if($validated==1&&strtotime($apply->begindate)>strtotime($time_start))
                {
                    throw new \Exception('请在申请使用期内设定取件时间');
                }
                if($validated==1&&strtotime($apply->enddate)<strtotime($time_end))
                {
                    throw new \Exception('请在申请使用期内设定取件时间');
                }
                $apply->apply_validated=$validated;
            }
            else
            {
                throw new \Exception('没有找到该申请');
            }
            $result=$apply->save();
            if($result)
            {
                $msg=Common::CreateWeiXinMessage([
                    ['title'=>'测试文本1'],
                    ['title'=>'测试文本','picUrl'=>'http://image.golaravel.com/5/c9/44e1c4e50d55159c65da6a41bc07e.jpg']
                ]);
                //Common::sendWeiXin(123,$msg);
                return true;
            }
        }
        catch(\Exception $ex)
        {
            throw $ex;
        }
    }
}