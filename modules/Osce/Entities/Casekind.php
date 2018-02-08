<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/30
 * Time: 11:47
 */

namespace Modules\Osce\Entities;

use DB;

class Casekind extends CommonModel
{
    protected $connection = 'osce_mis';
    protected $table = 'cases_kind';
    public $timestamps = true;
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $guarded = [];
    protected $hidden = [];
    protected $fillable = ['name', 'code', 'description', 'create_user_id'];
    public $search = [];


}