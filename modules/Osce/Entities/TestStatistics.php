<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2015/12/28
 * Time: 12:01
 */

namespace Modules\Osce\Entities;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class TestStatistics extends Model
{
    protected $connection = 'osce_mis';
    protected $table='g_test_statistics';
    protected $primaryKey='id';
    public $timestamps=false;
    protected $guarded      = [];
    protected $hidden       = [];
    protected $fillable     = [
        'id','logid','stuid','ipaddress','objective','subjective','time','ifexam','status'
    ];


  public function student(){
      return $this->hasOne('App\Entities\User', 'id', 'stuid');
  }

    public  function logdata(){
        return $this->hasOne('Modules\Osce\Entities\TestLog', 'id', 'logid');
    }


}


