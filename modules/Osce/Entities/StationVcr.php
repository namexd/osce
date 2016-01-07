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
}