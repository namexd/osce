<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/3
 * Time: 15:25
 */

namespace Modules\Osce\Entities;


class StationVcr extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'station_vcr';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['station_id', 'vcr_id'];
    public $search = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vcr()
    {
        return $this->belongsTo('Modules\Osce\Entities\Vcr', 'vcr_id', 'id');
    }

    public function getTime($vcr_id,$startTime,$endTime){
        $startTime=strtotime($startTime);
        $endTime=strtotime($endTime);
        $list=$this->select(DB::raw(
            implode(',',[
                $this->table.'.id as id',
                $this->table.'.station_id as station_id',
                $this->table.'.begin_dt as begin_dt',
                $this->table.'.end_dt as end_dt',
                $this->table.'.created_at as created_at',
            ])
        )
        );
        if($startTime){
            $list=$list->whereRaw(
                'unix_timestamp('.$this->table.'.begin_dt) <= ?',
                [
                    $startTime
                ]
            );
        }

        if($endTime){
            $list=$list->whereRaw(
                'unix_timestamp('.$this->table.'.end_dt) => ?',
                [
                    $endTime
                ]
            );
        }

        if($vcr_id){
            $list=$list->where('vcr_id',$vcr_id);
        }
        return $list;

    }
}