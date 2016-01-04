<?php
/**
 * Created by PhpStorm.
 * User: CoffeeKizoku
 * Date: 2016/1/3
 * Time: 15:30
 */

namespace Modules\Osce\Entities;


class PlaceRoom extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'place';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'type', 'time','room','create_user_id'];
    public $search = [];

}